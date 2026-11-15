<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Http;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Problem;

/** Fixed-window rate limiter for sensitive mutations. */
final class RateLimiter
{
    /** Returns false when the bucket is over its limit. */
    public static function attempt(string $action, string $identity, int $limit, int $windowSeconds): bool
    {
        $window = max(10, $windowSeconds);
        $bucket = hash('sha256', $action . '|' . $identity . '|' . intdiv(time(), $window));
        Db::run(
            'INSERT INTO tbl_studio_rate_limits (bucket, window_start, hits) VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE hits = hits + 1',
            [$bucket, intdiv(time(), $window)]
        );
        $row = Db::one('SELECT hits FROM tbl_studio_rate_limits WHERE bucket = ?', [$bucket]);
        return (int) ($row['hits'] ?? 0) <= $limit;
    }

    public static function guard(string $action, string $identity, int $limit, int $windowSeconds = 60): void
    {
        if (!self::attempt($action, $identity, $limit, $windowSeconds)) {
            throw new Problem(429, 'Too many requests. Wait a moment and try again.');
        }
    }

    /** Opportunistic cleanup; safe to call from cron. */
    public static function prune(int $maxAgeSeconds = 86400): void
    {
        Db::run('DELETE FROM tbl_studio_rate_limits WHERE window_start < ?', [time() - $maxAgeSeconds]);
    }
}
