<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Uuid;

/**
 * Short-lived, unguessable preview links. Only a SHA-256 hash is stored; the
 * plain token is returned exactly once and is never written to logs.
 */
final class PreviewTokenRepository
{
    public const DEFAULT_TTL = 3600;

    /** @return array{token:string,url:string,expiresAt:string} */
    public function issue(string $pageId, string $actorId, int $ttl = self::DEFAULT_TTL): array
    {
        $page = Db::one('SELECT draft_revision_id FROM tbl_studio_pages WHERE id = ?', [$pageId]);
        if ($page === null) {
            throw new Problem(404, 'Page not found.');
        }
        $revisionId = (string) ($page['draft_revision_id'] ?? '');
        if ($revisionId === '') {
            throw new Problem(422, 'This page has no draft revision to preview.');
        }
        $ttl = max(300, min(86400, $ttl));
        $token = bin2hex(random_bytes(32));
        Db::run('DELETE FROM tbl_studio_preview_tokens WHERE expires_at < UTC_TIMESTAMP()');
        Db::run(
            'INSERT INTO tbl_studio_preview_tokens (id, page_id, revision_id, token_hash, expires_at, created_by)
             VALUES (?, ?, ?, ?, DATE_ADD(UTC_TIMESTAMP(), INTERVAL ? SECOND), ?)',
            [Uuid::v4(), $pageId, $revisionId, hash('sha256', $token), $ttl, $actorId]
        );
        return [
            'token' => $token,
            'url' => 'preview_link.php?token=' . $token,
            'expiresAt' => gmdate('c', time() + $ttl),
        ];
    }

    /** @return array<string,mixed>|null */
    public function resolve(string $token): ?array
    {
        if (preg_match('/\A[a-f0-9]{64}\z/', $token) !== 1) {
            return null;
        }
        $row = Db::one(
            'SELECT t.page_id, t.revision_id, r.document
             FROM tbl_studio_preview_tokens t
             JOIN tbl_studio_revisions r ON r.id = t.revision_id
             WHERE t.token_hash = ? AND t.expires_at > UTC_TIMESTAMP() LIMIT 1',
            [hash('sha256', $token)]
        );
        if ($row === null) {
            return null;
        }
        $document = Json::decode((string) $row['document']);
        if (!is_array($document)) {
            return null;
        }
        return [
            'pageId' => (string) $row['page_id'],
            'revisionId' => (string) $row['revision_id'],
            'document' => $document,
        ];
    }
}
