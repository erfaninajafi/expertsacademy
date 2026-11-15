<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Auth;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Env;
use ExpertsAcademy\Studio\Problem;

/**
 * Session and capability foundation for the CMS module.
 *
 * Identity is the existing CMS administrator session (siteadmin_login). The
 * module never implements a second login form and never trusts a client-sent
 * user id. Capabilities are derived server-side from tbl_user_status:
 *   0 -> read, edit, publish   (CMS owner)
 *   1 -> read, edit
 *   * -> read
 */
final class Auth
{
    public const CAPABILITIES = ['read', 'edit', 'publish'];

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        // Share the existing CMS session. A custom name here would hide the
        // administrator login performed by site_admin/login.php.
        $name = (string) Env::security('session_name', '');
        if ($name !== '') {
            session_name($name);
        }
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => (bool) Env::security('cookie_httponly', true),
            'secure'   => (bool) Env::security('cookie_secure', false),
            'samesite' => (string) Env::security('cookie_samesite', 'Lax'),
        ]);
        session_start();
    }

    /** Development-only helper for loopback browser tests. Never enabled in production. */
    public static function maybeDevLogin(): void
    {
        if (Env::isProduction() || !Env::security('e2e_dev_login', false)) {
            return;
        }
        if (isset($_SESSION['siteadmin_login'])) {
            return;
        }
        if (!self::isLoopback()) {
            return;
        }
        if (($_GET['e2e_login'] ?? '') !== '1') {
            return;
        }
        $user = Db::one('SELECT tbl_user_id FROM tbl_user WHERE tbl_user_status = 0 ORDER BY tbl_user_id LIMIT 1');
        if ($user !== null) {
            session_regenerate_id(true);
            $_SESSION['siteadmin_login'] = (string) $user['tbl_user_id'];
        }
    }

    /** @return array{id:string,name:string,capabilities:list<string>} */
    public static function actor(): array
    {
        self::startSession();
        self::maybeDevLogin();

        $id = $_SESSION['siteadmin_login'] ?? null;
        if (!is_string($id) && !is_int($id)) {
            throw new Problem(401, 'Sign in to the CMS to use the page studio.');
        }
        $row = Db::one(
            'SELECT tbl_user_id, tbl_user_username, tbl_user_status FROM tbl_user WHERE tbl_user_id = ? LIMIT 1',
            [(string) $id]
        );
        if ($row === null) {
            unset($_SESSION['siteadmin_login']);
            throw new Problem(401, 'Your session is no longer valid. Sign in again.');
        }
        $level = (int) $row['tbl_user_status'];
        $base = match (true) {
            $level === 0 => ['read', 'edit', 'publish'],
            $level === 1 => ['read', 'edit'],
            default => ['read'],
        };
        $actor = [
            'id' => (string) $row['tbl_user_id'],
            'name' => (string) $row['tbl_user_username'],
            'capabilities' => $base,
        ];
        $role = Roles::roleFor($actor);
        return [
            'id' => (string) $row['tbl_user_id'],
            'name' => (string) $row['tbl_user_username'],
            'role' => $role,
            'permissions' => Roles::permissions($role),
            'capabilities' => Roles::capabilities($actor),
        ];
    }

    /** @param array{capabilities:list<string>} $actor */
    public static function require(array $actor, string $capability): void
    {
        if (!in_array($capability, self::CAPABILITIES, true)) {
            throw new Problem(500, 'Unknown capability.');
        }
        if (!in_array($capability, $actor['capabilities'], true)) {
            throw new Problem(403, 'Your account does not have the "' . $capability . '" capability.');
        }
    }

    /** @param array<string,mixed> $actor */
    public static function requirePermission(array $actor, string $permission): void
    {
        if (!in_array($permission, Roles::PERMISSIONS, true)) {
            throw new Problem(500, 'Unknown permission.');
        }
        if (!Roles::can($actor, $permission)) {
            throw new Problem(403, 'Your role does not allow "' . $permission . '".');
        }
    }

    /** @param array<string,mixed> $actor */
    public static function can(array $actor, string $permission): bool
    {
        return Roles::can($actor, $permission);
    }

    public static function token(): string
    {
        self::startSession();
        if (!isset($_SESSION['studio_csrf']) || !is_string($_SESSION['studio_csrf'])) {
            $_SESSION['studio_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['studio_csrf'];
    }

    public static function csrf(?string $provided): void
    {
        $expected = self::token();
        if (!is_string($provided) || $provided === '' || !hash_equals($expected, $provided)) {
            throw new Problem(419, 'The security token is missing or expired. Reload the editor.');
        }
        if (!self::sameOrigin()) {
            throw new Problem(403, 'Cross-origin requests are not allowed.');
        }
    }

    private static function sameOrigin(): bool
    {
        $origin = (string) ($_SERVER['HTTP_ORIGIN'] ?? '');
        if ($origin === '') {
            // Same-origin fetch may omit Origin for GET, but mutations carry it.
            return true;
        }
        $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
        if ($host === '') {
            return false;
        }
        $originHost = parse_url($origin, PHP_URL_HOST);
        $originPort = parse_url($origin, PHP_URL_PORT);
        $expectedHost = $host;
        if ($originPort !== null && !str_contains($host, ':')) {
            $expectedHost .= ':' . $originPort;
        }
        return is_string($originHost) && strcasecmp($originHost, (string) parse_url('http://' . $expectedHost, PHP_URL_HOST)) === 0;
    }

    private static function isLoopback(): bool
    {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
        return in_array($ip, ['127.0.0.1', '::1'], true);
    }
}
