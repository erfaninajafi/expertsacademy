<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Database;

use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\DesignTokens;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;

/**
 * Mirrors the code-owned registry into the database and installs the default
 * theme and starter template. Idempotent: safe to run after every migration.
 */
final class Seeder
{
    public const TOKENS_ID = '11111111-1111-4111-8111-111111111111';
    public const THEME_EN_ID = '22222222-2222-4222-8222-222222222221';
    public const THEME_FA_ID = '22222222-2222-4222-8222-222222222222';
    public const TEMPLATE_ID = '33333333-3333-4333-8333-333333333333';

    /** @return array<string,int> */
    public function run(): array
    {
        return Db::transaction(function (): array {
            $counts = ['components' => 0, 'variants' => 0, 'tokens' => 0, 'themes' => 0, 'templates' => 0];
            foreach (ComponentRegistry::all() as $definition) {
                $fingerprint = Json::checksum($definition);
                Db::run(
                    'INSERT INTO tbl_studio_components
                        (type_key, label, category, description, current_version, is_container, status, fingerprint)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE label=VALUES(label), category=VALUES(category),
                        description=VALUES(description), current_version=VALUES(current_version),
                        is_container=VALUES(is_container), status=VALUES(status), fingerprint=VALUES(fingerprint)',
                    [
                        $definition['type'], $definition['label'], $definition['category'],
                        $definition['description'], $definition['version'],
                        $definition['container'] ? 1 : 0, $definition['status'], $fingerprint,
                    ]
                );
                Db::run(
                    'INSERT INTO tbl_studio_component_versions (type_key, version, label, schema_document, schema_checksum)
                     VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE label=VALUES(label), schema_document=VALUES(schema_document),
                        schema_checksum=VALUES(schema_checksum)',
                    [
                        $definition['type'], $definition['version'], $definition['label'],
                        Json::encode($definition['props']), Json::checksum($definition['props']),
                    ]
                );
                Db::run('DELETE FROM tbl_studio_component_variants WHERE type_key = ? AND version = ?', [$definition['type'], $definition['version']]);
                foreach ($definition['variants'] as $order => $variant) {
                    Db::run(
                        'INSERT INTO tbl_studio_component_variants (type_key, version, variant_key, label, sort_order)
                         VALUES (?, ?, ?, ?, ?)',
                        [$definition['type'], $definition['version'], $variant['id'], $variant['label'], $order]
                    );
                    $counts['variants']++;
                }
                Db::run(
                    'INSERT IGNORE INTO tbl_studio_component_lifecycle (type_key, version, status, replacement_type, note, updated_by)
                     VALUES (?, ?, ?, NULL, ?, ?)',
                    [$definition['type'], $definition['version'], $definition['status'], 'Synced from the code registry.', 'system']
                );
                $counts['components']++;
            }

            $tokens = DesignTokens::defaults();
            $counts['tokens'] = Db::run(
                'INSERT IGNORE INTO tbl_studio_design_tokens (id, version, name, document, checksum, created_by)
                 VALUES (?, 1, ?, ?, ?, ?)',
                [self::TOKENS_ID, $tokens['name'], Json::encode($tokens), Json::checksum($tokens), 'system']
            );
            foreach (['en' => self::THEME_EN_ID, 'fa' => self::THEME_FA_ID] as $locale => $themeId) {
                $counts['themes'] += Db::run(
                    'INSERT IGNORE INTO tbl_studio_themes
                        (id, version, name, locale, tokens_id, tokens_version, status, is_default, created_by)
                     VALUES (?, 1, ?, ?, ?, 1, ?, 1, ?)',
                    [$themeId, $tokens['name'], $locale, self::TOKENS_ID, 'active', 'system']
                );
            }
            $template = Samples::page('en');
            Db::run(
                'INSERT IGNORE INTO tbl_studio_templates (id, version, name, page_schema_version, status, document)
                 VALUES (?, 1, ?, 1, ?, ?)',
                [self::TEMPLATE_ID, 'Editorial starter', 'active', Json::encode($template)]
            );
            $counts['templates'] = 0;
            foreach (Samples::templates() as $starter) {
                Db::run(
                    'INSERT INTO tbl_studio_templates
                        (id, version, name, category, description, page_schema_version, status, document)
                     VALUES (?, 1, ?, ?, ?, 1, ?, ?)
                     ON DUPLICATE KEY UPDATE name = VALUES(name), category = VALUES(category),
                        description = VALUES(description), document = VALUES(document), status = VALUES(status)',
                    [
                        $starter['id'], $starter['name'], $starter['category'], $starter['description'],
                        'active', Json::encode($starter['document']),
                    ]
                );
                $counts['templates']++;
            }
            return $counts;
        });
    }
}
