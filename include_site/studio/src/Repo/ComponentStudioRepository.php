<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Uuid;

/**
 * Editable component metadata and presets.
 *
 * Executable component code never lives in the database: templates and schemas
 * come from ComponentRegistry. This repository only stores documentation,
 * lifecycle flags, presets and release notes attached to those code-owned keys.
 */
final class ComponentStudioRepository
{
    private const STATUSES = ['stable', 'experimental', 'deprecated'];

    /** @return list<array<string,mixed>> */
    public function catalogue(): array
    {
        $rows = Db::all('SELECT * FROM tbl_studio_component_meta');
        $meta = [];
        foreach ($rows as $row) {
            $meta[(string) $row['type_key']] = $row;
        }
        $catalogue = [];
        foreach (ComponentRegistry::catalog() as $component) {
            $type = (string) $component['type'];
            $stored = $meta[$type] ?? null;
            $tags = $stored !== null ? Json::decode((string) $stored['tags']) : null;
            $animation = $stored !== null ? Json::decode((string) $stored['animation']) : null;
            $catalogue[] = $component + [
                'meta' => [
                    'status' => (string) ($stored['status'] ?? $component['status']),
                    'tags' => is_array($tags) ? $tags : [$component['category']],
                    'docs' => $stored !== null && trim((string) $stored['docs_md']) !== ''
                        ? (string) $stored['docs_md']
                        : $this->defaultDocs($component),
                    'a11y' => $stored !== null && trim((string) $stored['a11y_notes']) !== ''
                        ? (string) $stored['a11y_notes']
                        : $this->defaultA11y($component),
                    'animation' => is_array($animation) ? $animation : ['preset' => 'rise', 'duration' => 480],
                    'updated_by' => $stored['updated_by'] ?? null,
                    'updated_at' => $stored['updated_at'] ?? null,
                ],
            ];
        }
        return $catalogue;
    }

    /** @param array<string,mixed> $data */
    public function saveMeta(string $type, array $data, string $actorId): array
    {
        $definition = ComponentRegistry::get($type);
        if ($definition === null) {
            throw new Problem(404, 'Unknown component type.');
        }
        $status = is_string($data['status'] ?? null) ? $data['status'] : 'stable';
        if (!in_array($status, self::STATUSES, true)) {
            throw new Problem(422, 'Unsupported lifecycle status.');
        }
        $docs = is_string($data['docs'] ?? null) ? $data['docs'] : '';
        $a11y = is_string($data['a11y'] ?? null) ? $data['a11y'] : '';
        $tags = $data['tags'] ?? [];
        $animation = $data['animation'] ?? ['preset' => 'rise', 'duration' => 480];
        if (mb_strlen($docs) > 8000 || mb_strlen($a11y) > 2000) {
            throw new Problem(422, 'Documentation is too long.');
        }
        if (!is_array($tags) || count($tags) > 12) {
            throw new Problem(422, 'Tags must be a list of at most 12 items.');
        }
        foreach ($tags as $tag) {
            if (!is_string($tag) || mb_strlen($tag) > 40) {
                throw new Problem(422, 'Each tag must be a short string.');
            }
        }
        if (!is_array($animation) || !in_array($animation['preset'] ?? null, ['none','fade','rise','slide','scale','stagger'], true)) {
            throw new Problem(422, 'Unsupported animation preset.');
        }
        Db::run(
            'INSERT INTO tbl_studio_component_meta (type_key, status, tags, docs_md, a11y_notes, animation, updated_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status = VALUES(status), tags = VALUES(tags), docs_md = VALUES(docs_md),
                a11y_notes = VALUES(a11y_notes), animation = VALUES(animation), updated_by = VALUES(updated_by)',
            [$type, $status, Json::encode(array_values($tags)), $docs, $a11y, Json::encode($animation), $actorId]
        );
        foreach ($this->catalogue() as $component) {
            if ($component['type'] === $type) {
                return $component;
            }
        }
        throw new Problem(500, 'The component metadata could not be reloaded.');
    }

    /** @return list<array<string,mixed>> */
    public function presets(?string $type = null): array
    {
        if ($type !== null) {
            return Db::all(
                'SELECT id, type_key, name, variant, props, created_by, created_at
                 FROM tbl_studio_component_presets WHERE type_key = ? ORDER BY created_at DESC',
                [$type]
            );
        }
        return Db::all(
            'SELECT id, type_key, name, variant, props, created_by, created_at
             FROM tbl_studio_component_presets ORDER BY created_at DESC LIMIT 200'
        );
    }

    /** @param array<string,mixed> $props */
    public function createPreset(string $type, string $name, string $variant, array $props, string $actorId): array
    {
        $definition = ComponentRegistry::get($type);
        if ($definition === null) {
            throw new Problem(404, 'Unknown component type.');
        }
        if (!in_array($variant, ComponentRegistry::variantIds($type), true)) {
            throw new Problem(422, 'Unknown variant for this component.');
        }
        $name = trim($name);
        if ($name === '' || mb_strlen($name) > 120) {
            throw new Problem(422, 'A preset name of at most 120 characters is required.');
        }
        $checks = (new \ExpertsAcademy\Studio\Contract\Validator());
        $sample = \ExpertsAcademy\Studio\Contract\Samples::single($type, $variant);
        $sample['sections'][0]['components'][0]['props'] = $props;
        $errors = $checks->errors($sample);
        if ($errors !== []) {
            throw Problem::invalid($errors, 'The preset props are invalid.');
        }
        $id = Uuid::v4();
        Db::run(
            'INSERT INTO tbl_studio_component_presets (id, type_key, name, variant, props, created_by)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$id, $type, $name, $variant, Json::encode($props), $actorId]
        );
        return Db::one('SELECT * FROM tbl_studio_component_presets WHERE id = ?', [$id]) ?? [];
    }

    public function deletePreset(string $id): void
    {
        if (Db::run('DELETE FROM tbl_studio_component_presets WHERE id = ?', [Uuid::require($id)]) === 0) {
            throw new Problem(404, 'Preset not found.');
        }
    }

    /** @return list<array<string,mixed>> */
    public function versions(?string $type = null): array
    {
        if ($type !== null) {
            return Db::all(
                'SELECT type_key, version, label, notes, released_at
                 FROM tbl_studio_component_versions WHERE type_key = ? ORDER BY version DESC',
                [$type]
            );
        }
        return Db::all(
            "SELECT type_key, version, label, notes, released_at
             FROM tbl_studio_component_versions ORDER BY released_at DESC, type_key LIMIT 300"
        );
    }

    /** Mandatory, reviewable component contract surfaced to Component Studio. */
    public function contract(string $type): array
    {
        foreach ($this->catalogue() as $component) {
            if ($component['type'] === $type) {
                return [
                    'id' => $type,
                    'version' => $component['version'],
                    'schema' => $component['schema'],
                    'variants' => $component['variants'],
                    'defaults' => $component['defaultProps'],
                    'responsive' => ['breakpoints' => ['mobile', 'tablet', 'desktop']],
                    'rtl' => true,
                    'renderer' => 'php',
                    'assets' => ['css' => 'cms-pages.css'],
                    'a11y' => $component['meta']['a11y'],
                    'animation' => $component['meta']['animation'],
                    'docs' => $component['meta']['docs'],
                    'status' => $component['meta']['status'],
                ];
            }
        }
        throw new Problem(404, 'Unknown component type.');
    }

    private function defaultDocs(array $component): string
    {
        $variants = implode(', ', array_map(static fn (array $v): string => $v['label'], $component['variants']));
        return "**{$component['label']}** — {$component['description']}\n\n"
            . "Variants: {$variants}.\n\n"
            . "Properties are validated by the page contract; content is escaped server-side and rich text is sanitized.";
    }

    private function defaultA11y(array $component): string
    {
        return match ($component['category']) {
            'navigation' => 'Expose a single landmark, keep focus order linear and provide a visible focus ring. Menus are usable without JavaScript.',
            'media' => 'Every image needs a text alternative; decorative media is marked aria-hidden. Video requires captions and controls.',
            'forms' => 'Every field has a persistent label, errors are announced with aria-live and the form is keyboard operable.',
            'conversion' => 'Headings follow the section hierarchy; each call to action has a distinct accessible name.',
            default => 'One heading per section, logical heading levels, no keyboard traps and text contrast of at least 4.5:1.',
        };
    }
}
