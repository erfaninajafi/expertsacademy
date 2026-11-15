<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Uuid;

/** Append-only audit trail. Records are never updated or deleted by the application. */
final class AuditRepository
{
    /** @param array<string,mixed> $payload */
    public function record(
        string $actorId,
        string $action,
        string $entityType,
        ?string $entityId,
        ?string $pageId,
        array $payload = []
    ): void {
        Db::run(
            'INSERT INTO tbl_studio_events
                (id, page_id, entity_type, entity_id, actor_id, event_type, action, payload, ip_address, user_agent)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                Uuid::v4(), $pageId, $entityType, $entityId, $actorId,
                $action, $action, Json::encode($payload),
                $this->ip(), mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
            ]
        );
    }

    /** @return list<array<string,mixed>> */
    public function forPage(string $pageId, int $limit = 50): array
    {
        $limit = max(1, min(200, $limit));
        return Db::all(
            "SELECT id, actor_id, action, payload, ip_address, created_at
             FROM tbl_studio_events WHERE page_id = ? ORDER BY created_at DESC, id DESC LIMIT {$limit}",
            [$pageId]
        );
    }

    private function ip(): string
    {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
    }
}
