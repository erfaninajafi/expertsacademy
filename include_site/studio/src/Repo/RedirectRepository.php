<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Uuid;

/** Managed 301/302 redirects for public routes. */
final class RedirectRepository
{
    /** @return list<array<string,mixed>> */
    public function all(): array
    {
        return Db::all(
            'SELECT id, from_path, to_path, locale, status_code, active, created_by, created_at
             FROM tbl_studio_redirects ORDER BY created_at DESC LIMIT 500'
        );
    }

    /** @return array<string,mixed>|null */
    public function match(string $requested, string $locale): ?array
    {
        $requested = trim($requested);
        if ($requested === '') {
            return null;
        }
        $candidates = array_values(array_unique([
            $requested,
            '/' . ltrim($requested, '/'),
            ltrim($requested, '/'),
        ]));
        foreach ($candidates as $candidate) {
            $row = Db::one(
                'SELECT id, from_path, to_path, status_code
                 FROM tbl_studio_redirects WHERE locale = ? AND from_path = ? AND active = 1 LIMIT 1',
                [$locale, $candidate]
            );
            if ($row !== null) {
                return $row;
            }
        }
        return null;
    }

    /** @return array<string,mixed> */
    public function create(string $from, string $to, string $locale, int $code, string $actorId): array
    {
        [$from, $to] = $this->validate($from, $to, $locale, $code);
        $existing = Db::one(
            'SELECT id FROM tbl_studio_redirects WHERE locale = ? AND from_path = ?',
            [$locale, $from]
        );
        if ($existing !== null) {
            throw Problem::invalid(['fromPath' => 'A redirect already exists for this path.']);
        }
        $id = Uuid::v4();
        Db::run(
            'INSERT INTO tbl_studio_redirects (id, from_path, to_path, locale, status_code, active, created_by)
             VALUES (?, ?, ?, ?, ?, 1, ?)',
            [$id, $from, $to, $locale, $code, $actorId]
        );
        (new AuditRepository())->record($actorId, 'redirect.created', 'redirect', $id, null, [
            'from' => $from, 'to' => $to, 'code' => $code,
        ]);
        return Db::one('SELECT * FROM tbl_studio_redirects WHERE id = ?', [$id]) ?? [];
    }

    public function delete(string $id, string $actorId): void
    {
        $row = Db::one('SELECT id, from_path, to_path FROM tbl_studio_redirects WHERE id = ?', [$id]);
        if ($row === null) {
            throw new Problem(404, 'Redirect not found.');
        }
        Db::run('DELETE FROM tbl_studio_redirects WHERE id = ?', [$id]);
        (new AuditRepository())->record($actorId, 'redirect.deleted', 'redirect', $id, null, $row);
    }

    /** @return array{0:string,1:string} */
    private function validate(string $from, string $to, string $locale, int $code): array
    {
        $from = trim($from);
        $to = trim($to);
        $errors = [];
        if ($from === '' || preg_match('#[\r\n]#', $from) === 1 || (!str_starts_with($from, '/') && !preg_match('/\A[A-Za-z0-9_?=&%.-]+\z/', $from))) {
            $errors['fromPath'] = 'Use a relative path such as /old-course or old-course.';
        }
        if ($to === '' || preg_match('#[\r\n]#', $to) === 1) {
            $errors['toPath'] = 'Enter a destination path or URL.';
        } elseif (!str_starts_with($to, '/') && filter_var($to, FILTER_VALIDATE_URL) === false) {
            $errors['toPath'] = 'Destination must start with / or be an absolute http(s) URL.';
        }
        if (!in_array($locale, ['fa', 'en'], true)) {
            $errors['locale'] = 'Locale must be fa or en.';
        }
        if (!in_array($code, [301, 302, 307, 308], true)) {
            $errors['statusCode'] = 'Status code must be 301, 302, 307 or 308.';
        }
        if ($errors !== []) {
            throw Problem::invalid($errors);
        }
        return [str_starts_with($from, '/') ? $from : '/' . $from, $to];
    }
}
