<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\DesignTokens;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Uuid;

/** Immutable design-token versions and the themes that bind them. */
final class TokenRepository
{
    /** @return array{id:string,version:int,name:string,tokens:array<string,mixed>}|null */
    public function defaultFor(string $locale): ?array
    {
        $row = Db::one(
            'SELECT t.id, t.version, t.name, t.document
             FROM tbl_studio_themes th
             JOIN tbl_studio_design_tokens t ON t.id = th.tokens_id AND t.version = th.tokens_version
             WHERE th.locale = ? AND th.status = ? AND th.is_default = 1
             ORDER BY th.version DESC LIMIT 1',
            [$locale, 'active']
        );
        if ($row === null) {
            return null;
        }
        $tokens = Json::decode((string) $row['document']);
        return [
            'id' => (string) $row['id'],
            'version' => (int) $row['version'],
            'name' => (string) $row['name'],
            'tokens' => is_array($tokens) ? $tokens : DesignTokens::defaults(),
        ];
    }

    /** @return list<array{id:string,version:int,name:string,checksum:string,created_at:string}> */
    public function versions(int $limit = 50): array
    {
        $limit = max(1, min(200, $limit));
        return Db::all(
            "SELECT id, version, name, checksum, created_at FROM tbl_studio_design_tokens
             ORDER BY created_at DESC, version DESC LIMIT {$limit}"
        );
    }

    /** @return list<array<string,mixed>> */
    public function themes(): array
    {
        return Db::all(
            'SELECT th.id, th.version, th.name, th.locale, th.is_default, th.status, th.created_at,
                    t.id AS tokens_id, t.version AS tokens_version, t.document
             FROM tbl_studio_themes th
             JOIN tbl_studio_design_tokens t ON t.id = th.tokens_id AND t.version = th.tokens_version
             ORDER BY th.locale, th.version DESC'
        );
    }

    /**
     * Appends a new immutable token version and a theme version pointing at it.
     *
     * @return array{themeId:string,version:int,name:string}
     */
    public function createTheme(string $name, string $locale, array $tokens, string $actorId): array
    {
        $errors = DesignTokens::errors($tokens);
        if ($errors !== []) {
            throw Problem::invalid($errors, 'The design tokens are invalid.');
        }
        $themeId = Uuid::v4();
        $tokensId = $themeId; // one token set per theme family for a simple, explicit model
        return Db::transaction(function () use ($name, $locale, $tokens, $actorId, $themeId, $tokensId): array {
            $version = (int) (Db::one('SELECT COALESCE(MAX(version), 0) + 1 AS v FROM tbl_studio_design_tokens WHERE id = ?', [$tokensId])['v'] ?? 1);
            Db::run(
                'INSERT INTO tbl_studio_design_tokens (id, version, name, document, checksum, created_by)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [$tokensId, $version, $name, Json::encode($tokens), Json::checksum($tokens), $actorId]
            );
            Db::run('UPDATE tbl_studio_themes SET is_default = 0 WHERE locale = ?', [$locale]);
            Db::run(
                'INSERT INTO tbl_studio_themes
                    (id, version, name, locale, tokens_id, tokens_version, status, is_default, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)',
                [$themeId, $version, $name, $locale, $tokensId, $version, 'active', $actorId]
            );
            return ['themeId' => $themeId, 'version' => $version, 'name' => $name];
        });
    }
}
