<?php
declare(strict_types=1);

/**
 * Server-side scheduled publishing worker.
 *
 * Cron (every minute):
 *   * * * * * cd /path/to/expertsacademy/include_site/studio && \
 *     /usr/bin/php bin/publish_scheduled.php >> /path/to/storage/logs/scheduler.log 2>&1
 *
 * The DB claim makes overlapping runs safe; flock additionally prevents
 * duplicate local workers. No browser tab or logged-in user is required.
 */

require __DIR__ . '/../bootstrap.php';

use ExpertsAcademy\Studio\Env;
use ExpertsAcademy\Studio\Publish\PublishScheduler;

$timezone = Env::string('publishing.timezone', 'UTC');
if (!in_array($timezone, DateTimeZone::listIdentifiers(), true)) {
    fwrite(STDERR, "Invalid publishing.timezone: {$timezone}\n");
    exit(2);
}
date_default_timezone_set($timezone);

$limit = 25;
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/\A--limit=(\d+)\z/', $arg, $m) === 1) {
        $limit = max(1, min(100, (int) $m[1]));
    }
}

$lockPath = Env::paths()['cache'] . '/scheduler.lock';
if (!is_dir(dirname($lockPath)) && !mkdir(dirname($lockPath), 0770, true) && !is_dir(dirname($lockPath))) {
    fwrite(STDERR, "Cannot create lock directory.\n");
    exit(2);
}
$lock = fopen($lockPath, 'c');
if ($lock === false) {
    fwrite(STDERR, "Cannot open scheduler lock.\n");
    exit(2);
}
if (!flock($lock, LOCK_EX | LOCK_NB)) {
    fwrite(STDERR, "Another scheduler run is active.\n");
    exit(0);
}

try {
    $scheduler = new PublishScheduler();
    \ExpertsAcademy\Studio\Http\RateLimiter::prune();
    $result = $scheduler->runDue($limit);
    echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    exit($result['failed'] > 0 ? 1 : 0);
} catch (Throwable $error) {
    fwrite(STDERR, 'Scheduler failed: ' . $error->getMessage() . PHP_EOL);
    exit(2);
} finally {
    flock($lock, LOCK_UN);
    fclose($lock);
}
