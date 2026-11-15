<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio;

use RuntimeException;

/**
 * Environment-based configuration.
 *
 * Resolution order:
 *   1. EA_ENV_FILE (explicit file path)
 *   2. ~/.config/expertsacademy/studio.php (local default, outside web root)
 *   3. EA_* environment variables only
 *   4. The CMS database settings in include_site/config.php (fallback)
 *
 * No localhost URL, filesystem path or credential is hardcoded in module logic.
 */
final class Env
{
    /** @var array<string,mixed>|null */
    private static ?array $config = null;

    public static function boot(): void
    {
        self::config();
    }

    /** @return array<string,mixed> */
    public static function config(): array
    {
        if (self::$config !== null) {
            return self::$config;
        }

        $file = getenv('EA_ENV_FILE') ?: '';
        if ($file === '') {
            $home = getenv('HOME') ?: '';
            $candidate = $home !== '' ? $home . '/.config/expertsacademy/studio.php' : '';
            $file = $candidate !== '' && is_file($candidate) ? $candidate : '';
        }
        if ($file !== '' && !is_file($file)) {
            throw new RuntimeException('EA_ENV_FILE does not exist.');
        }

        $config = [];
        if ($file !== '') {
            $loaded = require $file;
            if (!is_array($loaded)) {
                throw new RuntimeException('The environment file must return an array.');
            }
            $config = $loaded;
        }

        $config = self::applyEnvOverrides($config);
        $config = self::applyLegacyDbFallback($config);
        return self::$config = $config;
    }

    /**
     * When no external Studio config is readable, reuse the CMS database
     * settings. This matters under Apache, whose service user cannot read a
     * configuration file stored in the administrator's home directory.
     *
     * @param array<string,mixed> $config
     * @return array<string,mixed>
     */
    private static function applyLegacyDbFallback(array $config): array
    {
        $db = isset($config['db']) && is_array($config['db']) ? $config['db'] : [];
        if (($db['name'] ?? '') !== '' && ($db['user'] ?? '') !== '') {
            return $config;
        }

        $file = dirname(__DIR__, 3) . '/include_site/config.php';
        if (!is_file($file)) {
            return $config;
        }

        $serverHost = $_SERVER['HTTP_HOST'] ?? null;
        if ($serverHost === null) {
            $_SERVER['HTTP_HOST'] = 'localhost';
        }
        $cms = (static function (string $path): array {
            require $path;
            return [
                'host' => $dbserver ?? '',
                'name' => $dbname ?? '',
                'user' => $dbusername ?? '',
                'pass' => $dbpassword ?? '',
            ];
        })($file);
        if ($serverHost === null) {
            unset($_SERVER['HTTP_HOST']);
        }

        foreach (['host', 'name', 'user', 'pass'] as $key) {
            if (($db[$key] ?? '') === '' && ($cms[$key] ?? '') !== '') {
                $db[$key] = $cms[$key];
            }
        }
        $config['db'] = $db;
        return $config;
    }

    /** @param array<string,mixed> $config @return array<string,mixed> */
    private static function applyEnvOverrides(array $config): array
    {
        $map = [
            'EA_APP_ENV'   => ['app_env', 'string'],
            'EA_DEBUG'     => ['debug', 'bool'],
            'EA_BASE_URL'  => ['base_url', 'string'],
            'EA_ASSET_URL' => ['asset_url', 'string'],
            'EA_DB_HOST'   => ['db.host', 'string'],
            'EA_DB_PORT'   => ['db.port', 'int'],
            'EA_DB_NAME'   => ['db.name', 'string'],
            'EA_DB_USER'   => ['db.user', 'string'],
            'EA_DB_PASS'   => ['db.pass', 'string'],
            'EA_DB_CHARSET'=> ['db.charset', 'string'],
            'EA_MEDIA_DIR' => ['paths.media', 'string'],
            'EA_CACHE_DIR' => ['paths.cache', 'string'],
            'EA_LOG_DIR'   => ['paths.logs', 'string'],
            'EA_PUBLISH_TIMEZONE' => ['publishing.timezone', 'string'],
        ];

        foreach ($map as $env => [$path, $type]) {
            $value = getenv($env);
            if ($value === false || $value === '') {
                continue;
            }
            $value = match ($type) {
                'bool' => in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true),
                'int'  => (int) $value,
                default => $value,
            };
            $keys = explode('.', $path);
            $cursor = &$config;
            foreach ($keys as $key) {
                if (!isset($cursor[$key]) || !is_array($cursor[$key])) {
                    $cursor[$key] = [];
                }
                $cursor = &$cursor[$key];
            }
            $cursor = $value;
            unset($cursor);
        }

        return $config;
    }

    public static function get(string $path, mixed $default = null): mixed
    {
        $cursor = self::config();
        foreach (explode('.', $path) as $key) {
            if (!is_array($cursor) || !array_key_exists($key, $cursor)) {
                return $default;
            }
            $cursor = $cursor[$key];
        }
        return $cursor;
    }

    public static function string(string $path, string $default = ''): string
    {
        $value = self::get($path, $default);
        return is_string($value) ? $value : $default;
    }

    public static function int(string $path, int $default = 0): int
    {
        $value = self::get($path, $default);
        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : $default);
    }

    public static function bool(string $path, bool $default = false): bool
    {
        $value = self::get($path, $default);
        return is_bool($value) ? $value : (bool) $value;
    }

    /** @return array{host:string,port:int,name:string,user:string,pass:string,charset:string} */
    public static function db(): array
    {
        $db = self::get('db', []);
        if (!is_array($db)) {
            $db = [];
        }
        return [
            'host'    => (string) ($db['host'] ?? '127.0.0.1'),
            'port'    => (int) ($db['port'] ?? 3306),
            'name'    => (string) ($db['name'] ?? ''),
            'user'    => (string) ($db['user'] ?? ''),
            'pass'    => (string) ($db['pass'] ?? ''),
            'charset' => (string) ($db['charset'] ?? 'utf8mb4'),
        ];
    }

    /** @return array{media:string,cache:string,logs:string} */
    public static function paths(): array
    {
        $paths = self::get('paths', []);
        if (!is_array($paths)) {
            $paths = [];
        }
        $root = dirname(__DIR__, 3);
        return [
            'media' => (string) ($paths['media'] ?? $root . '/upload_site/cms'),
            'cache' => (string) ($paths['cache'] ?? $root . '/storage/cache'),
            'logs'  => (string) ($paths['logs'] ?? $root . '/storage/logs'),
        ];
    }

    public static function timezone(): string
    {
        $zone = self::string('publishing.timezone', 'UTC');
        return in_array($zone, \DateTimeZone::listIdentifiers(), true) ? $zone : 'UTC';
    }

    public static function isProduction(): bool
    {
        return self::string('app_env', 'production') === 'production';
    }

    public static function security(string $key, mixed $default = null): mixed
    {
        return self::get('security.' . $key, $default);
    }
}
