<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Contract\PageDocument;
use ExpertsAcademy\Studio\Contract\Validator;
use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Uuid;
use ExpertsAcademy\Studio\Routing\PageRoute;

/**
 * Pages, immutable revisions and publications.
 *
 * Every write is validated, recorded as a revision and appended to the audit
 * trail. Optimistic locking uses tbl_studio_pages.lock_version; concurrent
 * saves fail with 409 instead of overwriting one another.
 */
final class PageRepository
{
    public function __construct(
        private readonly Validator $validator = new Validator(),
        private readonly AuditRepository $audit = new AuditRepository()
    ) {}

    /** @return array<string,mixed> */
    public function create(array $document, string $actorId, ?string $legacyCode = null, ?string $templateId = null, ?string $fullPath = null): array
    {
        $this->validator->assert($document);
        if ($legacyCode !== null && preg_match('/\A[A-Za-z0-9_-]{1,64}\z/D', $legacyCode) !== 1) {
            throw Problem::invalid(['legacyCode' => 'Binding must be 1-64 letters, digits, dashes or underscores.']);
        }
        if ($fullPath !== null) {
            $fullPath = PageRoute::normalize(
                $fullPath,
                (string) $document['locale']
            );
        }
        $id = Uuid::v4();
        $revisionId = Uuid::v4();

        return Db::transaction(function () use ($id, $revisionId, $document, $actorId, $legacyCode, $templateId, $fullPath): array {
            Db::run(
                'INSERT INTO tbl_studio_pages
                    (id, title, full_path, locale, direction, legacy_code, lock_version, status, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?)',
                [
                    $id, mb_substr((string) $document['title'], 0, 200), $fullPath, $document['locale'],
                    $document['direction'], $legacyCode, 'draft', $actorId,
                ]
            );
            $this->insertRevision($id, $revisionId, 1, $document, $actorId);
            Db::run('UPDATE tbl_studio_pages SET draft_revision_id = ? WHERE id = ?', [$revisionId, $id]);
            $this->audit->record($actorId, 'page.created', 'page', $id, $id, [
                'revision' => 1,
                'locale' => $document['locale'],
                'template' => $templateId,
            ]);
            return $this->get($id);
        });
    }

    /** @return array<string,mixed> */
    public function get(string $id): array
    {
        $page = Db::one(
            'SELECT id, title, slug, full_path, locale, direction, legacy_code, lock_version, status,
                    draft_revision_id, published_revision_id, published_at, scheduled_at, archived_at,
                    created_at, updated_at
             FROM tbl_studio_pages WHERE id = ?',
            [$id]
        );
        if ($page === null) {
            throw new Problem(404, 'Page not found.');
        }
        $document = null;
        if ($page['draft_revision_id'] !== null) {
            $revision = Db::one('SELECT document FROM tbl_studio_revisions WHERE id = ?', [$page['draft_revision_id']]);
            if ($revision !== null) {
                $decoded = Json::decode((string) $revision['document']);
                $document = is_array($decoded) ? $decoded : null;
            }
        }
        $revisions = Db::all(
            'SELECT id, revision_number, checksum, created_by, created_at
             FROM tbl_studio_revisions WHERE page_id = ? ORDER BY revision_number DESC LIMIT 50',
            [$id]
        );
        return [
            'id' => (string) $page['id'],
            'title' => (string) $page['title'],
            'slug' => $page['slug'] !== null ? (string) $page['slug'] : null,
            'fullPath' => $page['full_path'] !== null ? (string) $page['full_path'] : null,
            'locale' => (string) $page['locale'],
            'direction' => (string) $page['direction'],
            'legacyCode' => $page['legacy_code'] !== null ? (string) $page['legacy_code'] : null,
            'version' => (int) $page['lock_version'],
            'status' => (string) $page['status'],
            'draftRevision' => $page['draft_revision_id'] !== null ? (string) $page['draft_revision_id'] : null,
            'publishedRevision' => $page['published_revision_id'] !== null ? (string) $page['published_revision_id'] : null,
            'publishedAt' => $page['published_at'] !== null ? (string) $page['published_at'] : null,
            'scheduledAt' => $page['scheduled_at'] !== null ? (string) $page['scheduled_at'] : null,
            'archivedAt' => $page['archived_at'] !== null ? (string) $page['archived_at'] : null,
            'createdAt' => (string) $page['created_at'],
            'updatedAt' => (string) $page['updated_at'],
            'schedules' => Db::all(
                'SELECT id, revision_id, publish_at, timezone, status, attempts, last_error, created_at, processed_at
                 FROM tbl_studio_schedules WHERE page_id = ? ORDER BY created_at DESC LIMIT 20',
                [$id]
            ),
            'document' => $document,
            'revisions' => array_map(static fn (array $row): array => [
                'id' => (string) $row['id'],
                'revision_number' => (int) $row['revision_number'],
                'checksum' => (string) $row['checksum'],
                'created_by' => (string) $row['created_by'],
                'created_at' => (string) $row['created_at'],
            ], $revisions),
        ];
    }

    /** @return array{pages:list<array<string,mixed>>,total:int} */
    public function listing(int $limit = 30, int $offset = 0): array
    {
        $limit = max(1, min(100, $limit));
        $offset = max(0, $offset);
        $rows = Db::all(
            "SELECT id, title, slug, full_path, locale, legacy_code, lock_version, status, published_at, scheduled_at, updated_at
             FROM tbl_studio_pages ORDER BY updated_at DESC, id DESC LIMIT {$limit} OFFSET {$offset}"
        );
        $total = (int) (Db::one('SELECT COUNT(*) AS c FROM tbl_studio_pages')['c'] ?? 0);
        return [
            'pages' => array_map(static fn (array $row): array => [
                'id' => (string) $row['id'],
                'title' => (string) $row['title'],
                'locale' => (string) $row['locale'],
                'slug' => $row['slug'] !== null ? (string) $row['slug'] : null,
                'fullPath' => $row['full_path'] !== null ? (string) $row['full_path'] : null,
                'legacyCode' => $row['legacy_code'] !== null ? (string) $row['legacy_code'] : null,
                'version' => (int) $row['lock_version'],
                'status' => (string) $row['status'],
                'publishedAt' => $row['published_at'] !== null ? (string) $row['published_at'] : null,
                'scheduledAt' => $row['scheduled_at'] !== null ? (string) $row['scheduled_at'] : null,
                'updatedAt' => (string) $row['updated_at'],
            ], $rows),
            'total' => $total,
        ];
    }

    /** @return array<string,mixed> */
    public function save(string $id, array $document, int $expectedVersion, string $actorId): array
    {
        $this->validator->assert($document);
        return Db::transaction(function () use ($id, $document, $expectedVersion, $actorId): array {
            $page = Db::one('SELECT lock_version, status FROM tbl_studio_pages WHERE id = ? FOR UPDATE', [$id]);
            if ($page === null) {
                throw new Problem(404, 'Page not found.');
            }
            if ((int) $page['lock_version'] !== $expectedVersion) {
                throw new Problem(409, 'This page was changed by someone else. Reload before saving.');
            }
            $next = (int) (Db::one('SELECT COALESCE(MAX(revision_number), 0) + 1 AS n FROM tbl_studio_revisions WHERE page_id = ?', [$id])['n'] ?? 1);
            $revisionId = Uuid::v4();
            $this->insertRevision($id, $revisionId, $next, $document, $actorId);
            $status = (string) $page['status'];
            if ($status === 'scheduled') {
                Db::run(
                    "UPDATE tbl_studio_schedules SET status = 'cancelled', processed_at = CURRENT_TIMESTAMP
                     WHERE page_id = ? AND status = 'pending'",
                    [$id]
                );
                $status = 'draft';
            } elseif ($status === '') {
                $status = 'draft';
            }
            Db::run(
                'UPDATE tbl_studio_pages
                 SET draft_revision_id = ?, title = ?, direction = ?, status = ?, scheduled_at = NULL,
                     lock_version = lock_version + 1
                 WHERE id = ? AND lock_version = ?',
                [$revisionId, mb_substr((string) $document['title'], 0, 200),
                 $document['direction'], $status, $id, $expectedVersion]
            );
            $this->audit->record($actorId, 'page.saved', 'page', $id, $id, ['revision' => $next]);
            return $this->get($id);
        });
    }

    /**
     * Atomically publish a revision. Passing a revision id publishes an earlier
     * revision (rollback) without rewriting history.
     *
     * @return array<string,mixed>
     */
    public function publish(string $id, int $expectedVersion, string $actorId, ?string $revisionId = null): array
    {
        return Db::transaction(function () use ($id, $expectedVersion, $actorId, $revisionId): array {
            $page = Db::one(
                'SELECT lock_version, draft_revision_id, locale, legacy_code, full_path FROM tbl_studio_pages WHERE id = ? FOR UPDATE',
                [$id]
            );
            if ($page === null) {
                throw new Problem(404, 'Page not found.');
            }
            if ((int) $page['lock_version'] !== $expectedVersion) {
                throw new Problem(409, 'This page was changed by someone else. Reload before publishing.');
            }
            $revisionId = $revisionId ?? (string) ($page['draft_revision_id'] ?? '');
            if ($revisionId === '') {
                throw new Problem(422, 'There is no revision to publish.');
            }
            $revision = Db::one(
                'SELECT document, checksum FROM tbl_studio_revisions WHERE id = ? AND page_id = ?',
                [$revisionId, $id]
            );
            if ($revision === null) {
                throw new Problem(422, 'The selected revision does not belong to this page.');
            }
            $document = Json::decode((string) $revision['document']);
            if (!is_array($document)) {
                throw new Problem(422, 'The stored revision is not readable.');
            }
            $this->validator->assert($document);

            Db::run(
                'INSERT INTO tbl_studio_publications
                    (id, page_id, revision_id, locale, path, checksum, status, published_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE status = VALUES(status), path = VALUES(path), unpublished_at = NULL, published_at = CURRENT_TIMESTAMP',
                [
                    Uuid::v4(), $id, $revisionId, (string) $page['locale'],
                    (string) ($page['full_path'] ?: ($page['legacy_code'] ?? '')), (string) $revision['checksum'], 'published', $actorId,
                ]
            );
            Db::run(
                "UPDATE tbl_studio_pages SET published_revision_id = ?, status = 'published',
                    published_at = CURRENT_TIMESTAMP, scheduled_at = NULL, archived_at = NULL,
                    lock_version = lock_version + 1
                 WHERE id = ? AND lock_version = ?",
                [$revisionId, $id, $expectedVersion]
            );
            Db::run(
                "UPDATE tbl_studio_schedules SET status = 'cancelled', processed_at = CURRENT_TIMESTAMP
                 WHERE page_id = ? AND status IN ('pending','processing')",
                [$id]
            );
            $this->audit->record($actorId, 'page.published', 'page', $id, $id, [
                'revisionId' => $revisionId,
                'rollback' => $revisionId !== (string) ($page['draft_revision_id'] ?? ''),
            ]);
            return $this->get($id);
        });
    }

    /**
     * Schedule the current draft (or a selected revision) for future publication.
     * Editing the page afterwards cancels the pending schedule and returns it to
     * draft, so a stale revision can never be published silently.
     *
     * @return array<string,mixed>
     */
    public function schedule(
        string $id,
        string $publishAt,
        string $timezone,
        int $expectedVersion,
        string $actorId,
        ?string $revisionId = null
    ): array {
        try {
            $zone = new \DateTimeZone($timezone);
            $utc = (new \DateTimeImmutable($publishAt, $zone))->setTimezone(new \DateTimeZone('UTC'));
        } catch (\Throwable) {
            throw Problem::invalid(['publishAt' => 'Use a valid ISO date and timezone.']);
        }
        if ($utc <= new \DateTimeImmutable('now', new \DateTimeZone('UTC'))) {
            throw Problem::invalid(['publishAt' => 'Scheduled time must be in the future.']);
        }

        return Db::transaction(function () use ($id, $utc, $timezone, $expectedVersion, $actorId, $revisionId): array {
            $page = Db::one(
                'SELECT lock_version, draft_revision_id, locale FROM tbl_studio_pages WHERE id = ? FOR UPDATE',
                [$id]
            );
            if ($page === null) {
                throw new Problem(404, 'Page not found.');
            }
            if ((int) $page['lock_version'] !== $expectedVersion) {
                throw new Problem(409, 'This page was changed by someone else. Reload before scheduling.');
            }
            $revisionId = $revisionId ?? (string) ($page['draft_revision_id'] ?? '');
            $revision = $revisionId !== ''
                ? Db::one('SELECT document FROM tbl_studio_revisions WHERE id = ? AND page_id = ?', [$revisionId, $id])
                : null;
            if ($revision === null) {
                throw new Problem(422, 'There is no revision to schedule.');
            }
            $document = Json::decode((string) $revision['document']);
            if (!is_array($document)) {
                throw new Problem(422, 'The stored revision is not readable.');
            }
            $this->validator->assert($document);

            Db::run(
                "UPDATE tbl_studio_schedules SET status = 'cancelled', processed_at = CURRENT_TIMESTAMP
                 WHERE page_id = ? AND status = 'pending'",
                [$id]
            );
            $scheduleId = Uuid::v4();
            Db::run(
                "INSERT INTO tbl_studio_schedules
                    (id, page_id, revision_id, publish_at, timezone, status, created_by)
                 VALUES (?, ?, ?, ?, ?, 'pending', ?)",
                [$scheduleId, $id, $revisionId, $utc->format('Y-m-d H:i:s'), $timezone, $actorId]
            );
            Db::run(
                "UPDATE tbl_studio_pages SET status = 'scheduled', scheduled_at = ?, lock_version = lock_version + 1
                 WHERE id = ? AND lock_version = ?",
                [$utc->format('Y-m-d H:i:s'), $id, $expectedVersion]
            );
            $this->audit->record($actorId, 'page.scheduled', 'page', $id, $id, [
                'scheduleId' => $scheduleId,
                'revisionId' => $revisionId,
                'publishAt' => $utc->format('c'),
                'timezone' => $timezone,
            ]);
            return $this->get($id) + ['scheduleId' => $scheduleId];
        });
    }

    /** @return array<string,mixed> */
    public function cancelSchedule(string $id, int $expectedVersion, string $actorId): array
    {
        return Db::transaction(function () use ($id, $expectedVersion, $actorId): array {
            $page = Db::one('SELECT lock_version FROM tbl_studio_pages WHERE id = ? FOR UPDATE', [$id]);
            if ($page === null) {
                throw new Problem(404, 'Page not found.');
            }
            if ((int) $page['lock_version'] !== $expectedVersion) {
                throw new Problem(409, 'This page changed. Reload before cancelling the schedule.');
            }
            Db::run(
                "UPDATE tbl_studio_schedules SET status = 'cancelled', processed_at = CURRENT_TIMESTAMP
                 WHERE page_id = ? AND status = 'pending'",
                [$id]
            );
            Db::run(
                "UPDATE tbl_studio_pages SET status = 'draft', scheduled_at = NULL, lock_version = lock_version + 1
                 WHERE id = ? AND lock_version = ?",
                [$id, $expectedVersion]
            );
            $this->audit->record($actorId, 'page.schedule.cancelled', 'page', $id, $id);
            return $this->get($id);
        });
    }

    /** @return array<string,mixed> */
    public function unpublish(string $id, int $expectedVersion, string $actorId): array
    {
        return Db::transaction(function () use ($id, $expectedVersion, $actorId): array {
            $page = Db::one('SELECT lock_version, status FROM tbl_studio_pages WHERE id = ? FOR UPDATE', [$id]);
            if ($page === null) {
                throw new Problem(404, 'Page not found.');
            }
            if ((int) $page['lock_version'] !== $expectedVersion) {
                throw new Problem(409, 'This page changed. Reload before unpublishing.');
            }
            if (!in_array((string) $page['status'], ['published', 'scheduled'], true)) {
                throw new Problem(422, 'Only a published or scheduled page can be unpublished.');
            }
            Db::run(
                "UPDATE tbl_studio_schedules SET status = 'cancelled', processed_at = CURRENT_TIMESTAMP
                 WHERE page_id = ? AND status IN ('pending','processing')",
                [$id]
            );
            Db::run(
                "UPDATE tbl_studio_pages SET status = 'unpublished', scheduled_at = NULL, lock_version = lock_version + 1
                 WHERE id = ? AND lock_version = ?",
                [$id, $expectedVersion]
            );
            Db::run(
                "UPDATE tbl_studio_publications SET status = 'unpublished', unpublished_at = CURRENT_TIMESTAMP
                 WHERE page_id = ? AND status = 'published'",
                [$id]
            );
            $this->audit->record($actorId, 'page.unpublished', 'page', $id, $id);
            return $this->get($id);
        });
    }

    /** @return array<string,mixed> */
    public function archive(string $id, int $expectedVersion, string $actorId): array
    {
        return Db::transaction(function () use ($id, $expectedVersion, $actorId): array {
            $page = Db::one('SELECT lock_version FROM tbl_studio_pages WHERE id = ? FOR UPDATE', [$id]);
            if ($page === null) {
                throw new Problem(404, 'Page not found.');
            }
            if ((int) $page['lock_version'] !== $expectedVersion) {
                throw new Problem(409, 'This page changed. Reload before archiving.');
            }
            Db::run(
                "UPDATE tbl_studio_schedules SET status = 'cancelled', processed_at = CURRENT_TIMESTAMP
                 WHERE page_id = ? AND status IN ('pending','processing')",
                [$id]
            );
            Db::run(
                "UPDATE tbl_studio_pages SET status = 'archived', scheduled_at = NULL, archived_at = CURRENT_TIMESTAMP,
                    lock_version = lock_version + 1
                 WHERE id = ? AND lock_version = ?",
                [$id, $expectedVersion]
            );
            Db::run(
                "UPDATE tbl_studio_publications SET status = 'archived', unpublished_at = CURRENT_TIMESTAMP
                 WHERE page_id = ? AND status = 'published'",
                [$id]
            );
            $this->audit->record($actorId, 'page.archived', 'page', $id, $id);
            return $this->get($id);
        });
    }

    /** @return array<string,mixed> */
    /**
     * Change a Studio page's public path.
     *
     * Published/scheduled pages are temporarily protected until
     * redirect handling and public routing are implemented.
     *
     * @return array<string,mixed>
     */
    public function updatePublicPath(
        string $id,
        ?string $fullPath,
        int $expectedVersion,
        string $actorId
    ): array {
        return Db::transaction(function () use (
            $id, $fullPath, $expectedVersion, $actorId
        ): array {
            $page = Db::one(
                'SELECT id, locale, status, full_path, lock_version
                 FROM tbl_studio_pages
                 WHERE id = ?
                 FOR UPDATE',
                [$id]
            );

            if ($page === null) {
                throw new Problem(404, 'Page not found.');
            }

            if ((int) $page['lock_version'] !== $expectedVersion) {
                throw new Problem(
                    409,
                    'This page has changed. Reload before editing its path.'
                );
            }

            $normalized = null;

            if ($fullPath !== null && trim($fullPath) !== '') {
                $normalized = PageRoute::normalize(
                    $fullPath,
                    (string) $page['locale']
                );
            }

            $previous = $page['full_path'] !== null
                ? (string) $page['full_path']
                : null;

            if ($previous === $normalized) {
                return $this->get($id);
            }

            // Do not change public URLs until automatic redirects
            // and the unified public router are ready.
            if (in_array(
                (string) $page['status'],
                ['published', 'scheduled'],
                true
            )) {
                throw new Problem(
                    409,
                    'Changing a published or scheduled URL is not enabled yet.'
                );
            }

            if ($normalized !== null) {
                $existing = Db::one(
                    'SELECT id
                     FROM tbl_studio_pages
                     WHERE full_path = ? AND id <> ?
                     LIMIT 1',
                    [$normalized, $id]
                );

                if ($existing !== null) {
                    throw Problem::invalid([
                        'fullPath' => 'This public path is already in use.'
                    ]);
                }

                $redirect = Db::one(
                    'SELECT id
                     FROM tbl_studio_redirects
                     WHERE locale = ?
                       AND from_path = ?
                       AND active = 1
                     LIMIT 1',
                    [(string) $page['locale'], $normalized]
                );

                if ($redirect !== null) {
                    throw Problem::invalid([
                        'fullPath' => 'This path is reserved by a redirect.'
                    ]);
                }
            }

            Db::run(
                'UPDATE tbl_studio_pages
                 SET full_path = ?,
                     lock_version = lock_version + 1
                 WHERE id = ? AND lock_version = ?',
                [$normalized, $id, $expectedVersion]
            );

            $this->audit->record(
                $actorId,
                'page.public_path_changed',
                'page',
                $id,
                $id,
                [
                    'from' => $previous,
                    'to' => $normalized,
                ]
            );

            return $this->get($id);
        });
    }

    public function updateRoute(string $id, ?string $slug, ?string $legacyCode, int $expectedVersion, string $actorId): array
    {
        $slug = $slug !== null ? trim($slug) : '';
        $slug = $slug === '' ? null : $slug;
        if ($slug !== null && preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/D', $slug) !== 1) {
            throw Problem::invalid(['slug' => 'Use lowercase letters, digits and single dashes (max 120).']);
        }
        if ($slug !== null && strlen($slug) > 120) {
            throw Problem::invalid(['slug' => 'Slug must be at most 120 characters.']);
        }
        $legacyCode = $legacyCode !== null ? trim($legacyCode) : '';
        $legacyCode = $legacyCode === '' ? null : $legacyCode;
        if ($legacyCode !== null && preg_match('/\A[A-Za-z0-9_-]{1,64}\z/D', $legacyCode) !== 1) {
            throw Problem::invalid(['legacyCode' => 'Binding must be 1-64 letters, digits, dashes or underscores.']);
        }

        return Db::transaction(function () use ($id, $slug, $legacyCode, $expectedVersion, $actorId): array {
            $page = Db::one('SELECT lock_version, locale FROM tbl_studio_pages WHERE id = ? FOR UPDATE', [$id]);
            if ($page === null) {
                throw new Problem(404, 'Page not found.');
            }
            if ((int) $page['lock_version'] !== $expectedVersion) {
                throw new Problem(409, 'This page changed. Reload before editing the route.');
            }
            if ($slug !== null) {
                $taken = Db::one(
                    'SELECT id FROM tbl_studio_pages WHERE slug = ? AND locale = ? AND id <> ?',
                    [$slug, (string) $page['locale'], $id]
                );
                if ($taken !== null) {
                    throw Problem::invalid(['slug' => 'Another page already uses this slug in this locale.']);
                }
            }
            if ($legacyCode !== null) {
                $taken = Db::one(
                    'SELECT id FROM tbl_studio_pages WHERE legacy_code = ? AND locale = ? AND id <> ?',
                    [$legacyCode, (string) $page['locale'], $id]
                );
                if ($taken !== null) {
                    throw Problem::invalid(['legacyCode' => 'Another page already binds this CMS code in this locale.']);
                }
            }
            Db::run(
                'UPDATE tbl_studio_pages SET slug = ?, legacy_code = ?, lock_version = lock_version + 1
                 WHERE id = ? AND lock_version = ?',
                [$slug, $legacyCode, $id, $expectedVersion]
            );
            $this->audit->record($actorId, 'page.route', 'page', $id, $id, [
                'slug' => $slug,
                'legacyCode' => $legacyCode,
            ]);
            return $this->get($id);
        });
    }

    /** @return array<string,mixed> */
    public function revision(string $pageId, string $revisionId): array
    {
        $revision = Db::one(
            'SELECT id, page_id, revision_number, document, checksum, created_by, created_at
             FROM tbl_studio_revisions WHERE id = ? AND page_id = ?',
            [$revisionId, $pageId]
        );
        if ($revision === null) {
            throw new Problem(404, 'Revision not found.');
        }
        $document = Json::decode((string) $revision['document']);
        if (!is_array($document)) {
            throw new Problem(422, 'The stored revision is not readable.');
        }
        return [
            'id' => (string) $revision['id'],
            'pageId' => (string) $revision['page_id'],
            'revisionNumber' => (int) $revision['revision_number'],
            'checksum' => (string) $revision['checksum'],
            'createdBy' => (string) $revision['created_by'],
            'createdAt' => (string) $revision['created_at'],
            'document' => $document,
        ];
    }

    /**
     * Restore an earlier revision by appending it as a new draft revision.
     * History is never rewritten, so the restore itself is auditable and
     * reversible.
     *
     * @return array<string,mixed>
     */
    public function restore(string $id, string $revisionId, int $expectedVersion, string $actorId): array
    {
        return Db::transaction(function () use ($id, $revisionId, $expectedVersion, $actorId): array {
            $page = Db::one(
                'SELECT lock_version, status FROM tbl_studio_pages WHERE id = ? FOR UPDATE',
                [$id]
            );
            if ($page === null) {
                throw new Problem(404, 'Page not found.');
            }
            if ((int) $page['lock_version'] !== $expectedVersion) {
                throw new Problem(409, 'This page was changed by someone else. Reload before restoring.');
            }
            $source = Db::one(
                'SELECT document, revision_number FROM tbl_studio_revisions WHERE id = ? AND page_id = ?',
                [$revisionId, $id]
            );
            if ($source === null) {
                throw new Problem(404, 'Revision not found.');
            }
            $document = Json::decode((string) $source['document']);
            $this->validator->assert(is_array($document) ? $document : []);
            $next = (int) (Db::one(
                'SELECT COALESCE(MAX(revision_number), 0) + 1 AS n FROM tbl_studio_revisions WHERE page_id = ?',
                [$id]
            )['n'] ?? 1);
            $newId = Uuid::v4();
            $this->insertRevision($id, $newId, $next, $document, $actorId);
            Db::run(
                'UPDATE tbl_studio_pages
                 SET draft_revision_id = ?, title = ?, direction = ?, lock_version = lock_version + 1
                 WHERE id = ? AND lock_version = ?',
                [$newId, mb_substr((string) $document['title'], 0, 200), $document['direction'], $id, $expectedVersion]
            );
            $this->audit->record($actorId, 'page.restored', 'page', $id, $id, [
                'revision' => $next,
                'sourceRevision' => $revisionId,
                'sourceNumber' => (int) $source['revision_number'],
            ]);
            return $this->get($id);
        });
    }


    /**
     * Find a published Studio document using its public path.
     *
     * @return array<string,mixed>|null
     */
    public function publishedForPath(string $fullPath, string $locale): ?array
    {
        $row = Db::one(
            "SELECT r.document
             FROM tbl_studio_pages p
             JOIN tbl_studio_revisions r
               ON r.id = p.published_revision_id
              AND r.page_id = p.id
             WHERE p.full_path = ?
               AND p.locale = ?
               AND p.status = 'published'
             LIMIT 1",
            [$fullPath, $locale]
        );

        if ($row === null) {
            return null;
        }

        $document = Json::decode((string) $row['document']);

        if (
            !is_array($document) ||
            ($document['schemaVersion'] ?? null) !== PageDocument::SCHEMA_VERSION
        ) {
            Db::log('Invalid published document for path: ' . $fullPath);
            return null;
        }

        return $document;
    }

    /**
     * Published document for an existing CMS page code, or null to fall back to
     * legacy rendering. Only an explicitly published revision is served.
     *
     * @return array<string,mixed>|null
     */
    public function publishedForLegacyCode(string $code, string $locale): ?array
    {
        $row = Db::one(
            "SELECT r.document
             FROM tbl_studio_pages p
             JOIN tbl_studio_publications pub ON pub.page_id = p.id AND pub.status = 'published'
             JOIN tbl_studio_revisions r ON r.id = p.published_revision_id
             WHERE p.legacy_code = ? AND p.locale = ? AND p.status = 'published'
             ORDER BY pub.published_at DESC LIMIT 1",
            [$code, $locale]
        );
        if ($row === null) {
            return null;
        }
        $decoded = Json::decode((string) $row['document']);
        if (!is_array($decoded) || ($decoded['schemaVersion'] ?? null) !== PageDocument::SCHEMA_VERSION) {
            Db::log('published document has an unsupported schema version for ' . $code . '/' . $locale);
            return null;
        }
        return $decoded;
    }

    private function insertRevision(string $pageId, string $revisionId, int $number, array $document, string $actorId): void
    {
        Db::run(
            'INSERT INTO tbl_studio_revisions
                (id, page_id, revision_number, schema_version, document, checksum, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $revisionId, $pageId, $number, (int) $document['schemaVersion'],
                Json::encode($document), Json::checksum($document), $actorId,
            ]
        );
        $this->recordMediaUsages($revisionId, $document);
    }

    /** Media references are indexed per revision so orphaned assets are detectable. */
    private function recordMediaUsages(string $revisionId, array $document): void
    {
        foreach ($document['sections'] as $section) {
            foreach ($section['components'] as $component) {
                $refs = [];
                $walk = static function (mixed $value) use (&$walk, &$refs): void {
                    if (is_array($value)) {
                        if (isset($value['id'], $value['alt']) && is_string($value['id'])) {
                            $refs[$value['id']] = 'image';
                        }
                        foreach ($value as $item) {
                            $walk($item);
                        }
                    }
                };
                $walk($component['props']);
                foreach ($refs as $mediaId => $role) {
                    if (!Uuid::isValid((string) $mediaId)) {
                        continue;
                    }
                    Db::run(
                        'INSERT IGNORE INTO tbl_studio_media_usages (revision_id, component_id, media_id, role)
                         VALUES (?, ?, ?, ?)',
                        [$revisionId, $component['id'], $mediaId, $role]
                    );
                }
            }
        }
    }
}
