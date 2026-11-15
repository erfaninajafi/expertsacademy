<?php
declare(strict_types=1);

/**
 * Experts Academy CMS module bootstrap.
 *
 * Loads the Composer autoloader (required by the public integration in
 * index.php) and a defensive fallback autoloader for CLI tooling.
 */
$autoload = __DIR__ . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'ExpertsAcademy\\Studio\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }
        $path = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($path)) {
            require_once $path;
        }
    });
}

require_once __DIR__ . '/src/Env.php';
\ExpertsAcademy\Studio\Env::boot();
