<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Repo;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Env;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Uuid;
use ExpertsAcademy\Studio\Render\MapMediaResolver;

/**
 * Media metadata and safe upload handling.
 *
 * Files are renamed with a random key, validated by MIME sniffing, size and
 * extension, and stored under a dedicated directory that denies script
 * execution. The original filename is kept only as metadata.
 */
final class MediaRepository
{
    private const ALLOWED = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
        'image/svg+xml' => 'svg',
    ];
    private const MAX_BYTES = 8 * 1024 * 1024;

    /** @return list<array<string,mixed>> */
    public function listing(int $limit = 100): array
    {
        $limit = max(1, min(200, $limit));
        $rows = Db::all(
            "SELECT id, storage_key, original_name, mime_type, size_bytes, width, height, alt_text, focal_x, focal_y, created_at
             FROM tbl_studio_media WHERE deleted_at IS NULL
             ORDER BY created_at DESC LIMIT {$limit}"
        );
        $base = rtrim(Env::string('asset_url', '/'), '/');
        foreach ($rows as &$row) {
            $row['url'] = $base . '/upload_site/' . ltrim((string) $row['storage_key'], '/');
            $row['width'] = $row['width'] !== null ? (int) $row['width'] : null;
            $row['height'] = $row['height'] !== null ? (int) $row['height'] : null;
        }
        unset($row);
        return $rows;
    }

    /** @return array<string,mixed> */
    public function register(array $file, string $alt, string $actorId): array
    {
        if (!isset($file['tmp_name'], $file['name'], $file['size'], $file['error'])) {
            throw new Problem(400, 'No file was uploaded.');
        }
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new Problem(400, 'The upload did not complete.');
        }
        if ((int) $file['size'] <= 0 || (int) $file['size'] > self::MAX_BYTES) {
            throw new Problem(413, 'The file must be between 1 byte and 8 MB.');
        }
        if (!is_uploaded_file((string) $file['tmp_name'])) {
            throw new Problem(400, 'Invalid upload.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file((string) $file['tmp_name']);
        if (!isset(self::ALLOWED[$mime])) {
            throw new Problem(415, 'Unsupported file type. Allowed: JPEG, PNG, WebP, AVIF, SVG.');
        }
        $extension = self::ALLOWED[$mime];
        $checksum = hash_file('sha256', (string) $file['tmp_name']);
        $key = 'cms/' . gmdate('Y/m') . '/' . bin2hex(random_bytes(16)) . '.' . $extension;

        $root = rtrim(Env::paths()['media'], '/');
        $target = $root . '/' . substr($key, 4);
        if (!is_dir(dirname($target)) && !mkdir(dirname($target), 0770, true)) {
            throw new Problem(500, 'The media directory is not writable.');
        }
        if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
            throw new Problem(500, 'The upload could not be stored.');
        }
        @chmod($target, 0640);

        $width = null;
        $height = null;
        if ($mime !== 'image/svg+xml') {
            $size = @getimagesize($target);
            if (is_array($size)) {
                $width = (int) $size[0];
                $height = (int) $size[1];
            }
        }

        $id = Uuid::v4();
        Db::run(
            'INSERT INTO tbl_studio_media
                (id, storage_key, original_name, mime_type, size_bytes, checksum, width, height, alt_text, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $id, $key, mb_substr((string) $file['name'], 0, 255), $mime, (int) $file['size'],
                $checksum, $width, $height, mb_substr($alt, 0, 500), $actorId,
            ]
        );
        try {
            (new \ExpertsAcademy\Studio\Media\ImageOptimizer())->generate($id, $key, $mime, $width);
        } catch (\Throwable $error) {
            Db::log('image variants failed for ' . $id . ': ' . $error->getMessage());
        }
        return ['id' => $id, 'storage_key' => $key, 'mime_type' => $mime, 'width' => $width, 'height' => $height, 'alt_text' => $alt];
    }

    /** Update alt text and the focal point (percent, 0-100). */
    public function updateMeta(string $id, string $alt, int $focalX, int $focalY, string $actorId): array
    {
        $row = Db::one('SELECT id FROM tbl_studio_media WHERE id = ? AND deleted_at IS NULL', [$id]);
        if ($row === null) {
            throw new Problem(404, 'Media item not found.');
        }
        if (mb_strlen($alt) > 500) {
            throw Problem::invalid(['alt' => 'Alt text must be at most 500 characters.']);
        }
        $focalX = max(0, min(100, $focalX));
        $focalY = max(0, min(100, $focalY));
        Db::run(
            'UPDATE tbl_studio_media SET alt_text = ?, focal_x = ?, focal_y = ? WHERE id = ?',
            [mb_substr($alt, 0, 500), $focalX, $focalY, $id]
        );
        (new AuditRepository())->record($actorId, 'media.meta', 'media', $id, null, [
            'focalX' => $focalX, 'focalY' => $focalY,
        ]);
        return ['id' => $id, 'alt_text' => $alt, 'focal_x' => $focalX, 'focal_y' => $focalY];
    }

    /** Resolves every media id referenced by a document in one query. */
    public function resolverFor(array $document): MapMediaResolver
    {
        $ids = [];
        $walk = static function (mixed $value) use (&$walk, &$ids): void {
            if (is_array($value)) {
                if (isset($value['id']) && is_string($value['id']) && Uuid::isValid($value['id'])) {
                    $ids[$value['id']] = true;
                }
                foreach ($value as $item) {
                    $walk($item);
                }
            }
        };
        $walk($document);
        if ($ids === []) {
            return new MapMediaResolver([]);
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = Db::all(
            "SELECT id, storage_key, mime_type, width, height, alt_text, focal_x, focal_y FROM tbl_studio_media
             WHERE deleted_at IS NULL AND id IN ({$placeholders})",
            array_keys($ids)
        );
        $variants = [];
        $variantRows = Db::all(
            "SELECT media_id, format, width, height, storage_key FROM tbl_studio_media_variants
             WHERE media_id IN ({$placeholders}) ORDER BY width",
            array_keys($ids)
        );
        foreach ($variantRows as $variant) {
            $variants[(string) $variant['media_id']][] = $variant;
        }
        $map = [];
        $base = rtrim(Env::string('asset_url', '/'), '/');
        foreach ($rows as $row) {
            $id = (string) $row['id'];
            $srcset = [];
            foreach ($variants[$id] ?? [] as $variant) {
                $srcset[] = $base . '/upload_site/' . ltrim((string) $variant['storage_key'], '/')
                    . ' ' . (int) $variant['width'] . 'w';
            }
            $map[$id] = [
                'url' => $base . '/upload_site/' . ltrim((string) $row['storage_key'], '/'),
                'alt' => (string) $row['alt_text'],
                'width' => $row['width'] !== null ? (int) $row['width'] : null,
                'height' => $row['height'] !== null ? (int) $row['height'] : null,
                'srcset' => $srcset,
                'focal' => (int) $row['focal_x'] . '% ' . (int) $row['focal_y'] . '%',
            ];
        }
        return new MapMediaResolver($map);
    }
}
