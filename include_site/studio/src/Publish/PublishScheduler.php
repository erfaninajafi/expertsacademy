<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Publish;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Repo\AuditRepository;
use ExpertsAcademy\Studio\Repo\PageRepository;
use Throwable;

/**
 * Server-side scheduled publishing.
 *
 * A schedule row is claimed atomically (pending -> processing) before the
 * publication runs, so two overlapping cron invocations can never publish the
 * same schedule twice. Failed attempts are retried with a short backoff until
 * MAX_ATTEMPTS, then surfaced as failed with the error text for manual retry.
 */
final class PublishScheduler
{
    public const MAX_ATTEMPTS = 3;

    public function __construct(
        private readonly PageRepository $pages = new PageRepository(),
        private readonly AuditRepository $audit = new AuditRepository()
    ) {}

    /**
     * Publish every due schedule. Safe to call from cron every minute.
     *
     * @return array{claimed:int,published:int,failed:int,results:list<array<string,mixed>>}
     */
    public function runDue(int $limit = 25): array
    {
        $limit = max(1, min(100, $limit));
        $this->recoverStale();

        $rows = Db::all(
            "SELECT id, page_id, revision_id, attempts, created_by
             FROM tbl_studio_schedules
             WHERE status = 'pending' AND publish_at <= UTC_TIMESTAMP()
             ORDER BY publish_at ASC LIMIT {$limit}"
        );

        $published = 0;
        $failed = 0;
        $results = [];
        foreach ($rows as $row) {
            $claim = Db::run(
                "UPDATE tbl_studio_schedules
                 SET status = 'processing', attempts = attempts + 1, processed_at = UTC_TIMESTAMP()
                 WHERE id = ? AND status = 'pending'",
                [(string) $row['id']]
            );
            if ($claim !== 1) {
                continue;
            }
            $result = $this->publishClaimed((string) $row['id']);
            $results[] = $result;
            if ($result['status'] === 'published') {
                $published++;
            } elseif ($result['status'] === 'failed') {
                $failed++;
            }
        }

        return ['claimed' => count($results), 'published' => $published, 'failed' => $failed, 'results' => $results];
    }

    /**
     * Publish one already-claimed schedule. Public so the CLI can run a single
     * schedule in tests; normal callers use runDue().
     *
     * @return array<string,mixed>
     */
    public function publishClaimed(string $scheduleId): array
    {
        $schedule = Db::one('SELECT * FROM tbl_studio_schedules WHERE id = ?', [$scheduleId]);
        if ($schedule === null) {
            throw new Problem(404, 'Schedule not found.');
        }
        if ((string) $schedule['status'] !== 'processing') {
            return [
                'scheduleId' => $scheduleId,
                'status' => (string) $schedule['status'],
                'skipped' => true,
            ];
        }

        try {
            $page = Db::one(
                'SELECT lock_version, status, published_revision_id FROM tbl_studio_pages WHERE id = ?',
                [(string) $schedule['page_id']]
            );
            if ($page === null) {
                throw new Problem(422, 'The scheduled page no longer exists.');
            }
            // Idempotent completion: a previous attempt may have published and
            // then crashed before marking the schedule.
            if (
                (string) $page['status'] === 'published'
                && (string) $page['published_revision_id'] === (string) $schedule['revision_id']
            ) {
                $this->markPublished($scheduleId);
                return ['scheduleId' => $scheduleId, 'status' => 'published', 'idempotent' => true];
            }
            $this->pages->publish(
                (string) $schedule['page_id'],
                (int) $page['lock_version'],
                'scheduler:' . (string) $schedule['created_by'],
                (string) $schedule['revision_id']
            );
            $this->markPublished($scheduleId);
            $this->audit->record('scheduler', 'schedule.published', 'schedule', $scheduleId, (string) $schedule['page_id'], [
                'attempts' => (int) $schedule['attempts'],
            ]);
            return ['scheduleId' => $scheduleId, 'status' => 'published'];
        } catch (Throwable $error) {
            $this->markFailure($scheduleId, (int) $schedule['attempts'], $error->getMessage());
            return [
                'scheduleId' => $scheduleId,
                'status' => 'failed',
                'error' => $error->getMessage(),
                'attempts' => (int) $schedule['attempts'],
            ];
        }
    }

    /** Manually requeue a failed schedule. */
    public function retry(string $scheduleId, string $actorId): void
    {
        $schedule = Db::one(
            'SELECT id, status, page_id FROM tbl_studio_schedules WHERE id = ?',
            [$scheduleId]
        );
        if ($schedule === null) {
            throw new Problem(404, 'Schedule not found.');
        }
        if ((string) $schedule['status'] !== 'failed') {
            throw new Problem(422, 'Only failed schedules can be retried.');
        }
        Db::run(
            "UPDATE tbl_studio_schedules
             SET status = 'pending', attempts = 0, last_error = NULL, publish_at = UTC_TIMESTAMP(), processed_at = NULL
             WHERE id = ? AND status = 'failed'",
            [$scheduleId]
        );
        Db::run(
            "UPDATE tbl_studio_pages SET status = 'scheduled', scheduled_at = UTC_TIMESTAMP()
             WHERE id = ? AND status <> 'published'",
            [(string) $schedule['page_id']]
        );
        $this->audit->record($actorId, 'schedule.retried', 'schedule', $scheduleId, (string) $schedule['page_id']);
    }

    /** @return list<array<string,mixed>> */
    public function recent(int $limit = 50): array
    {
        $limit = max(1, min(200, $limit));
        return Db::all(
            "SELECT s.id, s.page_id, p.title, s.revision_id, s.publish_at, s.timezone, s.status,
                    s.attempts, s.last_error, s.created_by, s.created_at, s.processed_at
             FROM tbl_studio_schedules s
             JOIN tbl_studio_pages p ON p.id = s.page_id
             ORDER BY s.created_at DESC LIMIT {$limit}"
        );
    }

    private function markPublished(string $scheduleId): void
    {
        Db::run(
            "UPDATE tbl_studio_schedules SET status = 'published', processed_at = UTC_TIMESTAMP(), last_error = NULL
             WHERE id = ?",
            [$scheduleId]
        );
    }

    private function markFailure(string $scheduleId, int $attempts, string $error): void
    {
        $message = mb_substr($error, 0, 2000);
        if ($attempts >= self::MAX_ATTEMPTS) {
            Db::run(
                "UPDATE tbl_studio_schedules SET status = 'failed', last_error = ?, processed_at = UTC_TIMESTAMP()
                 WHERE id = ?",
                [$message, $scheduleId]
            );
            Db::run(
                "UPDATE tbl_studio_pages SET status = 'draft', scheduled_at = NULL
                 WHERE id = (SELECT page_id FROM tbl_studio_schedules WHERE id = ?) AND status = 'scheduled'",
                [$scheduleId]
            );
            return;
        }
        // Retry after a short backoff. publish_at moving forward keeps the job
        // idempotent and prevents a hot loop on a permanently broken page.
        Db::run(
            "UPDATE tbl_studio_schedules
             SET status = 'pending', last_error = ?, publish_at = DATE_ADD(UTC_TIMESTAMP(), INTERVAL ? MINUTE),
                 processed_at = NULL
             WHERE id = ?",
            [$message, $attempts * 2, $scheduleId]
        );
    }

    /** Recover rows left in processing by a killed worker. */
    private function recoverStale(): void
    {
        Db::run(
            "UPDATE tbl_studio_schedules
             SET status = 'failed', last_error = 'Publishing timed out; retry manually.'
             WHERE status = 'processing' AND attempts >= " . self::MAX_ATTEMPTS . "
               AND processed_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 15 MINUTE)"
        );
        Db::run(
            "UPDATE tbl_studio_schedules
             SET status = 'pending', processed_at = NULL
             WHERE status = 'processing' AND attempts < " . self::MAX_ATTEMPTS . "
               AND processed_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 15 MINUTE)"
        );
    }
}
