<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Auth\Roles;
use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Problem;

/** CMS user role assignment, enforced by Auth::requirePermission on the backend. */
final class RoleRepository
{
    /** @return list<array<string,mixed>> */
    public function users(): array
    {
        $rows = Db::all(
            "SELECT u.tbl_user_id AS id, u.tbl_user_username AS username, u.tbl_user_status AS status,
                    r.role, r.updated_at
             FROM tbl_user u
             LEFT JOIN tbl_studio_user_roles r ON r.user_id = u.tbl_user_id
             ORDER BY u.tbl_user_username"
        );
        foreach ($rows as &$row) {
            $legacy = match ((int) $row['status']) {
                0 => Roles::ADMINISTRATOR,
                1 => Roles::EDITOR,
                default => Roles::REVIEWER,
            };
            $row['role'] = $row['role'] ?? $legacy;
            $row['permissions'] = Roles::permissions((string) $row['role']);
            unset($row['status']);
        }
        return $rows;
    }

    public function assign(string $userId, string $role, string $actorId): void
    {
        if (!Roles::isValid($role)) {
            throw Problem::invalid(['role' => 'Unknown role.']);
        }
        $user = Db::one('SELECT tbl_user_id FROM tbl_user WHERE tbl_user_id = ?', [$userId]);
        if ($user === null) {
            throw new Problem(404, 'CMS user not found.');
        }
        if ($userId === $actorId && $role !== Roles::ADMINISTRATOR) {
            $admins = Db::one(
                "SELECT COUNT(*) AS n FROM tbl_studio_user_roles WHERE role = 'administrator' AND user_id <> ?",
                [$userId]
            );
            if ((int) ($admins['n'] ?? 0) === 0) {
                throw new Problem(422, 'Keep at least one administrator account.');
            }
        }
        Db::run(
            'INSERT INTO tbl_studio_user_roles (user_id, role, updated_by) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE role = VALUES(role), updated_by = VALUES(updated_by)',
            [$userId, $role, $actorId]
        );
        (new AuditRepository())->record($actorId, 'role.assigned', 'user', $userId, null, ['role' => $role]);
    }
}
