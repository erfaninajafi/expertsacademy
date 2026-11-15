<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\DesignTokens;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Uuid;

/**
 * Drafts -> validation -> controlled publication for the design system.
 *
 * Editing never touches the active theme. Publishing appends an immutable
 * token version, promotes a new theme version for the locale and records the
 * publication, all inside one transaction.
 */
final class DesignSystemRepository
{
    /** @return list<array<string,mixed>> */
    public function drafts(string $locale): array
    {
        return Db::all(
            'SELECT id, name, locale, base_theme_id, version, status, checksum, updated_by, updated_at
             FROM tbl_studio_theme_drafts WHERE locale = ? ORDER BY updated_at DESC LIMIT 50',
            [$locale]
        );
    }

    /** @return array<string,mixed> */
    public function draft(string $id): array
    {
        $row = Db::one('SELECT * FROM tbl_studio_theme_drafts WHERE id = ?', [$id]);
        if ($row === null) {
            throw new Problem(404, 'Design draft not found.');
        }
        $tokens = Json::decode((string) $row['tokens']);
        $row['tokens'] = is_array($tokens) ? DesignTokens::withDefaults($tokens) : DesignTokens::defaults();
        return $row;
    }

    /** Latest working draft for a locale, creating one from the active theme when absent. */
    public function workingDraft(string $locale, string $actorId): array
    {
        $row = Db::one(
            "SELECT id FROM tbl_studio_theme_drafts WHERE locale = ? AND status = 'draft'
             ORDER BY updated_at DESC LIMIT 1",
            [$locale]
        );
        if ($row !== null) {
            return $this->draft((string) $row['id']);
        }
        $active = (new TokenRepository())->defaultFor($locale);
        $tokens = is_array($active['tokens'] ?? null)
            ? DesignTokens::withDefaults($active['tokens'])
            : DesignTokens::defaults();
        return $this->create($active['name'] ?? 'Academy / Editorial', $locale, $tokens, $actorId, $active['id'] ?? null);
    }

    /** @param array<string,mixed> $tokens */
    public function create(string $name, string $locale, array $tokens, string $actorId, ?string $baseThemeId = null): array
    {
        $this->assertValid($tokens);
        $id = Uuid::v4();
        Db::run(
            'INSERT INTO tbl_studio_theme_drafts
                (id, name, locale, base_theme_id, tokens, checksum, version, status, created_by, updated_by)
             VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?)',
            [$id, mb_substr(trim($name), 0, 120), $locale, $baseThemeId,
             Json::encode($tokens), Json::checksum($tokens), 'draft', $actorId, $actorId]
        );
        return $this->draft($id);
    }

    /** @param array<string,mixed> $tokens */
    public function save(string $id, array $tokens, string $actorId, int $expectedVersion): array
    {
        $this->assertValid($tokens);
        $changed = Db::run(
            "UPDATE tbl_studio_theme_drafts
             SET tokens = ?, checksum = ?, version = version + 1, status = 'draft', updated_by = ?
             WHERE id = ? AND version = ?",
            [Json::encode($tokens), Json::checksum($tokens), $actorId, $id, $expectedVersion]
        );
        if ($changed === 0) {
            throw new Problem(409, 'This design draft changed in another session. Reload before saving.');
        }
        return $this->draft($id);
    }

    public function publish(string $id, string $actorId, int $expectedVersion): array
    {
        $draft = $this->draft($id);
        if ((int) $draft['version'] !== $expectedVersion) {
            throw new Problem(409, 'This design draft changed in another session. Reload before publishing.');
        }
        $tokens = $draft['tokens'];
        $this->assertValid($tokens);

        $result = Db::transaction(function () use ($draft, $tokens, $actorId, $id, $expectedVersion): array {
            $locale = (string) $draft['locale'];
            $themeId = Uuid::v4();
            $tokensId = Uuid::v4();
            $version = 1;
            Db::run(
                'INSERT INTO tbl_studio_design_tokens (id, version, name, document, checksum, created_by)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [$tokensId, $version, (string) $draft['name'], Json::encode($tokens), Json::checksum($tokens), $actorId]
            );
            Db::run("UPDATE tbl_studio_themes SET is_default = 0 WHERE locale = ?", [$locale]);
            Db::run(
                'INSERT INTO tbl_studio_themes
                    (id, version, name, locale, tokens_id, tokens_version, status, is_default, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)',
                [$themeId, $version, (string) $draft['name'], $locale, $tokensId, $version, 'active', $actorId]
            );
            $publicationId = Uuid::v4();
            Db::run(
                'INSERT INTO tbl_studio_theme_publications
                    (id, draft_id, theme_id, tokens_id, tokens_version, locale, checksum, published_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [$publicationId, $id, $themeId, $tokensId, $version, $locale, Json::checksum($tokens), $actorId]
            );
            $changed = Db::run(
                "UPDATE tbl_studio_theme_drafts SET status = 'published', version = version + 1, updated_by = ?
                 WHERE id = ? AND version = ?",
                [$actorId, $id, $expectedVersion]
            );
            if ($changed === 0) {
                throw new Problem(409, 'This design draft changed while publishing.');
            }
            return [
                'publicationId' => $publicationId,
                'themeId' => $themeId,
                'tokensVersion' => $version,
                'locale' => $locale,
            ];
        });

        (new AuditRepository())->record($actorId, 'theme.publish', 'theme', $result['themeId'], null, [
            'draft' => $id,
            'locale' => $result['locale'],
            'checksum' => Json::checksum($tokens),
        ]);

        return $result + ['draft' => $this->draft($id)];
    }

    /** @return list<array<string,mixed>> */
    public function history(string $locale): array
    {
        return Db::all(
            'SELECT p.id, p.draft_id, p.theme_id, p.tokens_version, p.locale, p.checksum, p.published_at,
                    p.published_by, t.name, t.document
             FROM tbl_studio_theme_publications p
             JOIN tbl_studio_design_tokens t ON t.id = p.tokens_id AND t.version = p.tokens_version
             WHERE p.locale = ? ORDER BY p.published_at DESC, p.id DESC LIMIT 50',
            [$locale]
        );
    }

    /** @return array<string,string> */
    public function errors(mixed $tokens): array
    {
        return DesignTokens::errors($tokens);
    }

    private function assertValid(array $tokens): void
    {
        $errors = DesignTokens::errors($tokens);
        if ($errors !== []) {
            throw Problem::invalid($errors, 'The design tokens are invalid.');
        }
    }
}
