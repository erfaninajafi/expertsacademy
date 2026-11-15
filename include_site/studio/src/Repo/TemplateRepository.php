<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Contract\Validator;
use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Problem;

/** Printable page templates: read-only catalogue plus per-admin favourites. */
final class TemplateRepository
{
    public function __construct(private readonly Validator $validator = new Validator())
    {
    }

    /** @return list<array<string,mixed>> */
    public function listing(int $adminId): array
    {
        $rows = Db::all(
            'SELECT t.id, t.version, t.name, t.category, t.description, t.status,
                    JSON_LENGTH(t.document, \'$.sections\') AS section_count,
                    (f.admin_id IS NOT NULL) AS favorite
             FROM tbl_studio_templates t
             JOIN (
                SELECT id, MAX(version) AS version FROM tbl_studio_templates
                WHERE status = \'active\' GROUP BY id
             ) latest ON latest.id = t.id AND latest.version = t.version
             LEFT JOIN tbl_studio_template_favorites f
                ON f.template_id = t.id AND f.admin_id = ?
             ORDER BY favorite DESC, t.name',
            [$adminId]
        );

        return array_map(static fn (array $row): array => [
            'id' => (string) $row['id'],
            'version' => (int) $row['version'],
            'name' => (string) $row['name'],
            'category' => (string) $row['category'],
            'description' => (string) $row['description'],
            'sectionCount' => (int) $row['section_count'],
            'favorite' => (bool) $row['favorite'],
            'createdAt' => '',
        ], $rows);
    }

    /** @return array<string,mixed> */
    public function get(string $id, int $adminId = 0, ?int $version = null): array
    {
        $sql = 'SELECT t.id, t.version, t.name, t.category, t.description, t.status, t.document,
                       (f.admin_id IS NOT NULL) AS favorite
                FROM tbl_studio_templates t
                LEFT JOIN tbl_studio_template_favorites f
                    ON f.template_id = t.id AND f.admin_id = ?';
        $params = [$adminId];
        if ($version === null) {
            $sql .= ' WHERE t.id = ? ORDER BY t.version DESC LIMIT 1';
            $params[] = $id;
        } else {
            $sql .= ' WHERE t.id = ? AND t.version = ?';
            $params[] = $id;
            $params[] = $version;
        }
        $row = Db::one($sql, $params);
        if ($row === null || $row['status'] !== 'active') {
            throw new Problem(404, 'Template not found.');
        }
        $document = Json::decode((string) $row['document']);
        if (!is_array($document)) {
            throw new Problem(422, 'This template cannot be read.');
        }
        $this->validator->assert($document);

        return [
            'id' => (string) $row['id'],
            'version' => (int) $row['version'],
            'name' => (string) $row['name'],
            'category' => (string) $row['category'],
            'description' => (string) $row['description'],
            'favorite' => (bool) $row['favorite'],
            'createdAt' => '',
            'document' => $document,
        ];
    }

    /** @return array{favorite:bool} */
    public function toggleFavorite(string $id, int $adminId): array
    {
        if (Db::one('SELECT id FROM tbl_studio_templates WHERE id = ? LIMIT 1', [$id]) === null) {
            throw new Problem(404, 'Template not found.');
        }
        $exists = Db::one(
            'SELECT template_id FROM tbl_studio_template_favorites WHERE template_id = ? AND admin_id = ?',
            [$id, $adminId]
        );
        if ($exists !== null) {
            Db::run('DELETE FROM tbl_studio_template_favorites WHERE template_id = ? AND admin_id = ?', [$id, $adminId]);
            return ['favorite' => false];
        }
        Db::run('INSERT INTO tbl_studio_template_favorites (template_id, admin_id) VALUES (?, ?)', [$id, $adminId]);
        return ['favorite' => true];
    }
}
