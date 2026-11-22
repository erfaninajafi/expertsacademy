<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Routing;

use ExpertsAcademy\Studio\Problem;

final class PageRoute
{
    private const RESERVED_SEGMENTS = [
        'site_admin',
        'include_site',
        'upload_site',
        'uploadfile_site',
        'asset_site',
        'components_site',
        'sys_panel2',
        'sys_panel3',
        'api',
    ];

    /**
     * Validate and normalize a Studio public path.
     *
     * Examples:
     *   courses/math     -> /fa/courses/math
     *   /fa/courses/math -> /fa/courses/math
     */
    public static function normalize(
        string $input,
        string $locale
    ): string {
        if (!in_array($locale, ['fa', 'en'], true)) {
            throw Problem::invalid([
                'locale' => 'Unsupported locale.',
            ]);
        }

        $path = trim($input);

        if ($path === '') {
            throw Problem::invalid([
                'fullPath' => 'Public path is required.',
            ]);
        }

        if (preg_match('~\A(?:fa|en)/~', $path) === 1) {
            $path = '/' . $path;
        } elseif (!str_starts_with($path, '/')) {
            $path = '/' . $locale . '/' . $path;
        }

        $pattern = '~\A/(fa|en)/'
            . '[a-z0-9]+(?:-[a-z0-9]+)*'
            . '(?:/[a-z0-9]+(?:-[a-z0-9]+)*)*'
            . '\z~';

        if (
            strlen($path) > 255 ||
            preg_match($pattern, $path, $matches) !== 1 ||
            $matches[1] !== $locale
        ) {
            throw Problem::invalid([
                'fullPath' => 'Invalid public path.',
            ]);
        }

        $segments = explode('/', trim($path, '/'));
        $firstSegment = $segments[1] ?? '';

        if (in_array($firstSegment, self::RESERVED_SEGMENTS, true)) {
            throw Problem::invalid([
                'fullPath' => 'Reserved system path.',
            ]);
        }

        return $path;
    }
}
