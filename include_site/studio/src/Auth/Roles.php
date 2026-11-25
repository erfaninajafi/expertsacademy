<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Auth;

use ExpertsAcademy\Studio\Db;

/**
 * Studio roles and the permissions they grant.
 *
 * Roles are stored per CMS user in tbl_studio_user_roles. Users without a row
 * fall back to the legacy tbl_user_status mapping so existing installations keep
 * working: status 0 -> administrator, 1 -> editor, anything else -> reviewer.
 */
final class Roles
{
    public const ADMINISTRATOR = 'administrator';
    public const DEVELOPER = 'developer';
    public const DESIGNER = 'designer';
    public const EDITOR = 'editor';
    public const REVIEWER = 'reviewer';
    public const PUBLISHER = 'publisher';

    /** @var list<string> */
    public const ALL = [
        self::ADMINISTRATOR,
        self::DEVELOPER,
        self::DESIGNER,
        self::EDITOR,
        self::REVIEWER,
        self::PUBLISHER,
    ];

    /** @var list<string> */
    public const PERMISSIONS = [
        'pages.create',
        'pages.edit',
        'pages.restore',
        'pages.route',
        'pages.publish',
        'pages.unpublish',
        'pages.archive',
        'design.edit',
        'components.manage',
        'templates.manage',
        'media.upload',
        'media.manage',
        'preview.draft',
        'seo.manage',
        'redirects.manage',
        'users.manage',
        'audit.read',
    ];

    /** @return array<string,list<string>> */
    public static function matrix(): array
    {
        return [
            self::ADMINISTRATOR => self::PERMISSIONS,
            self::DEVELOPER => [
                'pages.create', 'pages.edit', 'pages.restore', 'pages.route',
                'pages.publish', 'pages.unpublish', 'pages.archive',
                'design.edit', 'components.manage', 'templates.manage',
                'media.upload', 'media.manage', 'preview.draft', 'seo.manage',
                'redirects.manage', 'audit.read',
            ],
            self::DESIGNER => [
                'pages.edit', 'design.edit', 'components.manage', 'media.upload',
                'media.manage', 'preview.draft',
            ],
            self::EDITOR => [
                'pages.create', 'pages.edit', 'pages.restore', 'pages.route',
                'media.upload', 'preview.draft', 'seo.manage',
            ],
            self::REVIEWER => ['preview.draft', 'audit.read'],
            self::PUBLISHER => [
                'pages.publish', 'pages.unpublish', 'pages.archive',
                'pages.restore', 'preview.draft', 'audit.read',
            ],
        ];
    }

    /** @return list<string> */
    public static function permissions(string $role): array
    {
        return self::matrix()[$role] ?? [];
    }

    public static function isValid(string $role): bool
    {
        return in_array($role, self::ALL, true);
    }

    /** @param array{id:string,capabilities?:list<string>} $actor */
    public static function roleFor(array $actor): string
    {
        try {
            $row = Db::one('SELECT role FROM tbl_studio_user_roles WHERE user_id = ?', [$actor['id']]);
        } catch (\Throwable) {
            $row = null;
        }
        if (is_array($row) && self::isValid((string) $row['role'])) {
            return (string) $row['role'];
        }
        $capabilities = $actor['capabilities'] ?? [];
        if (in_array('publish', $capabilities, true)) {
            return self::ADMINISTRATOR;
        }
        if (in_array('edit', $capabilities, true)) {
            return self::EDITOR;
        }
        return self::REVIEWER;
    }

    /** @param array<string,mixed> $actor @return list<string> */
    public static function forActor(array $actor): array
    {
        return self::permissions(self::roleFor($actor));
    }

    public static function can(array $actor, string $permission): bool
    {
        return in_array($permission, self::forActor($actor), true);
    }

    /** Legacy capability derived from the granular permission set. @return list<string> */
    public static function capabilities(array $actor): array
    {
        $permissions = self::forActor($actor);
        $capabilities = ['read'];
        $edit = [
            'pages.create', 'pages.edit', 'pages.restore', 'pages.route',
            'design.edit', 'components.manage', 'templates.manage',
            'media.upload', 'media.manage', 'seo.manage', 'redirects.manage',
            'users.manage',
        ];
        $publish = ['pages.publish', 'pages.unpublish', 'pages.archive'];
        if (array_intersect($edit, $permissions) !== []) {
            $capabilities[] = 'edit';
        }
        if (array_intersect($publish, $permissions) !== []) {
            $capabilities[] = 'publish';
        }
        return $capabilities;
    }
}
