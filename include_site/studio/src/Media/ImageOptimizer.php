<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Media;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Env;
use ExpertsAcademy\Studio\Uuid;

/**
 * Generates responsive derivatives without touching the uploaded original.
 *
 * WebP/AVIF are produced only when the PHP build supports them; the source
 * format is always kept so the site still works on a minimal GD build. SVG is
 * never rasterized.
 */
final class ImageOptimizer
{
    private const WIDTHS = [480, 960, 1440];

    /** @return list<array<string,mixed>> */
    public function generate(string $mediaId, string $storageKey, string $mime, ?int $sourceWidth): array
    {
        if ($sourceWidth === null || $sourceWidth < 240 || !in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return [];
        }
        $root = rtrim(Env::paths()['media'], '/');
        $source = $root . '/' . substr($storageKey, 4);
        if (!is_file($source)) {
            return [];
        }
        $image = $this->load($source, $mime);
        if ($image === null) {
            return [];
        }
        $width = imagesx($image);
        $height = imagesy($image);
        $variants = [];
        foreach (self::WIDTHS as $targetWidth) {
            if ($targetWidth >= $width) {
                continue;
            }
            $targetHeight = (int) max(1, round($height * ($targetWidth / $width)));
            $resized = imagecreatetruecolor($targetWidth, $targetHeight);
            if ($resized === false) {
                continue;
            }
            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagefill($resized, 0, 0, imagecolorallocatealpha($resized, 0, 0, 0, 127));
            }
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

            $format = $this->format($mime);
            $variantKey = $this->variantKey($storageKey, $targetWidth, $format['ext']);
            $target = $root . '/' . substr($variantKey, 4);
            if (!is_dir(dirname($target))) {
                @mkdir(dirname($target), 0770, true);
            }
            $ok = $format['write']($resized, $target);
            imagedestroy($resized);
            if (!$ok || !is_file($target)) {
                continue;
            }
            @chmod($target, 0640);
            $bytes = (int) filesize($target);
            Db::run(
                'INSERT INTO tbl_studio_media_variants (id, media_id, format, width, height, storage_key, bytes)
                 VALUES (?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE bytes = VALUES(bytes), height = VALUES(height)',
                [Uuid::v4(), $mediaId, $format['ext'], $targetWidth, $targetHeight, $variantKey, $bytes]
            );
            $variants[] = ['format' => $format['ext'], 'width' => $targetWidth, 'height' => $targetHeight, 'storage_key' => $variantKey];
        }
        imagedestroy($image);
        return $variants;
    }

    private function load(string $path, string $mime): ?\GdImage
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
        return $image instanceof \GdImage ? $image : null;
    }

    /** @return array{ext:string,write:callable} */
    private function format(string $mime): array
    {
        // Prefer a modern format only when this PHP build can encode it.
        if (function_exists('imageavif')) {
            return ['ext' => 'avif', 'write' => static fn (\GdImage $i, string $p): bool => imageavif($i, $p, 60)];
        }
        if (function_exists('imagewebp')) {
            return ['ext' => 'webp', 'write' => static fn (\GdImage $i, string $p): bool => imagewebp($i, $p, 82)];
        }
        return match ($mime) {
            'image/png' => ['ext' => 'png', 'write' => static fn (\GdImage $i, string $p): bool => imagepng($i, $p, 9)],
            default => ['ext' => 'jpg', 'write' => static fn (\GdImage $i, string $p): bool => imagejpeg($i, $p, 82)],
        };
    }

    private function variantKey(string $storageKey, int $width, string $extension): string
    {
        $dir = dirname($storageKey);
        $base = pathinfo($storageKey, PATHINFO_FILENAME);
        return $dir . '/' . $base . '-' . $width . 'w.' . $extension;
    }
}
