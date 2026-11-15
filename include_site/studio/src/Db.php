<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio;

use PDO;
use PDOException;
use Throwable;

/**
 * One lazily-created PDO connection per request. All SQL uses bound parameters;
 * no caller ever interpolates request data into a statement.
 */
final class Db
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = Env::db();
        if ($config['name'] === '' || $config['user'] === '') {
            throw new Problem(500, 'Database configuration is incomplete.');
        }

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config['host'], $config['port'], $config['name'], $config['charset']);
        try {
            self::$pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]);
        } catch (PDOException $error) {
            self::log('database connection failed: ' . $error->getCode());
            throw new Problem(503, 'The content service is temporarily unavailable.');
        }
        return self::$pdo;
    }

    /** @param array<string|int,mixed> $params @return array<string,mixed>|null */
    public static function one(string $sql, array $params = []): ?array
    {
        $statement = self::pdo()->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();
        return is_array($row) ? $row : null;
    }

    /** @param array<string|int,mixed> $params @return list<array<string,mixed>> */
    public static function all(string $sql, array $params = []): array
    {
        $statement = self::pdo()->prepare($sql);
        $statement->execute($params);
        $rows = $statement->fetchAll();
        return is_array($rows) ? array_values($rows) : [];
    }

    /** @param array<string|int,mixed> $params */
    public static function run(string $sql, array $params = []): int
    {
        $statement = self::pdo()->prepare($sql);
        $statement->execute($params);
        return $statement->rowCount();
    }

    /**
     * Runs a callable inside a transaction and rolls back on any exception.
     *
     * @template T
     * @param callable(PDO):T $work
     * @return T
     */
    public static function transaction(callable $work): mixed
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $result = $work($pdo);
            $pdo->commit();
            return $result;
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }

    public static function log(string $message): void
    {
        $dir = Env::paths()['logs'];
        if (!is_dir($dir)) {
            @mkdir($dir, 0770, true);
        }
        $line = sprintf("[%s] %s\n", gmdate('c'), str_replace(["\r", "\n"], ' ', $message));
        @file_put_contents($dir . '/cms.log', $line, FILE_APPEND | LOCK_EX);
        error_log('cms: ' . $message);
    }
}
