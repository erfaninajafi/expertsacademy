<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Database;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Env;
use ExpertsAcademy\Studio\Problem;
use PDO;
use RuntimeException;

/**
 * Ordered SQL migration runner.
 *
 * - records version + checksum so edited history is detected;
 * - serialises concurrent runs with a MySQL advisory lock;
 * - supports up, down (to a version) and dry runs;
 * - DDL in MySQL is not transactional; a failed migration leaves its earlier
 *   statements applied and is reported instead of being recorded as complete.
 */
final class Migrator
{
    public function __construct(private readonly string $directory)
    {
        if (!is_dir($directory)) {
            throw new RuntimeException('Migration directory not found: ' . $directory);
        }
    }

    /** @return list<string> ordered versions */
    public function versions(): array
    {
        $versions = [];
        foreach (glob($this->directory . '/*.up.sql') ?: [] as $file) {
            if (preg_match('/(\d{3}_[a-z0-9_]+)\.up\.sql$/', $file, $match) === 1) {
                $versions[] = $match[1];
            }
        }
        sort($versions, SORT_STRING);
        return $versions;
    }

    public function ensureTable(): void
    {
        Db::pdo()->exec("CREATE TABLE IF NOT EXISTS tbl_studio_migrations (
            version varchar(32) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
            checksum char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
            applied_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin");
    }

    /** @return array<string,array{checksum:string,applied_at:string}> */
    public function applied(): array
    {
        $this->ensureTable();
        $rows = Db::all('SELECT version, checksum, applied_at FROM tbl_studio_migrations ORDER BY version');
        $result = [];
        foreach ($rows as $row) {
            $result[(string) $row['version']] = ['checksum' => (string) $row['checksum'], 'applied_at' => (string) $row['applied_at']];
        }
        return $result;
    }

    /** @return list<array{version:string,state:string,checksum:string}> */
    public function status(): array
    {
        $applied = $this->applied();
        $result = [];
        foreach ($this->versions() as $version) {
            $checksum = $this->checksum($version);
            if (!isset($applied[$version])) {
                $state = 'pending';
            } elseif ($applied[$version]['checksum'] !== $checksum) {
                $state = 'modified';
            } else {
                $state = 'applied';
            }
            $result[] = ['version' => $version, 'state' => $state, 'checksum' => $checksum];
        }
        return $result;
    }

    /** @return list<string> versions applied by this call */
    public function up(bool $dryRun = false): array
    {
        $this->ensureTable();
        $applied = $this->applied();
        $pending = [];
        foreach ($this->versions() as $version) {
            if (isset($applied[$version])) {
                if ($applied[$version]['checksum'] !== $this->checksum($version)) {
                    throw new RuntimeException("Applied migration {$version} has been modified. Refusing to continue.");
                }
                continue;
            }
            $pending[] = $version;
        }
        if ($pending === [] || $dryRun) {
            return $pending;
        }

        $this->withLock(function () use ($pending): void {
            foreach ($pending as $version) {
                $this->apply($version);
            }
        });
        return $pending;
    }

    /** @return list<string> versions rolled back */
    public function down(?string $toVersion = null, bool $force = false): array
    {
        if (!$force) {
            throw new RuntimeException('Rollback requires --force. It can remove studio data.');
        }
        $applied = array_keys($this->applied());
        if ($applied === []) {
            return [];
        }
        $targets = [];
        foreach (array_reverse($applied) as $version) {
            if ($toVersion !== null && strcmp($version, $toVersion) <= 0) {
                break;
            }
            $targets[] = $version;
        }
        if ($targets === []) {
            return [];
        }
        $this->withLock(function () use ($targets): void {
            foreach ($targets as $version) {
                $this->rollback($version);
            }
        });
        return $targets;
    }

    private function apply(string $version): void
    {
        $sql = $this->read($version, 'up');
        foreach ($this->split($sql) as $statement) {
            Db::pdo()->exec($statement);
        }
        Db::run('INSERT INTO tbl_studio_migrations (version, checksum) VALUES (?, ?)', [$version, $this->checksum($version)]);
        fwrite(STDOUT, "applied  {$version}\n");
    }

    private function rollback(string $version): void
    {
        $file = $this->directory . '/' . $version . '.down.sql';
        if (!is_file($file)) {
            throw new RuntimeException("Missing rollback file for {$version}.");
        }
        foreach ($this->split((string) file_get_contents($file)) as $statement) {
            Db::pdo()->exec($statement);
        }
        Db::run('DELETE FROM tbl_studio_migrations WHERE version = ?', [$version]);
        fwrite(STDOUT, "reverted {$version}\n");
    }

    private function read(string $version, string $direction): string
    {
        $file = $this->directory . '/' . $version . '.' . $direction . '.sql';
        if (!is_file($file)) {
            throw new RuntimeException("Missing migration file: {$file}");
        }
        return (string) file_get_contents($file);
    }

    public function checksum(string $version): string
    {
        return hash('sha256', (string) file_get_contents($this->directory . '/' . $version . '.up.sql'));
    }

    /** @param callable():void $work */
    private function withLock(callable $work): void
    {
        $pdo = Db::pdo();
        $statement = $pdo->prepare('SELECT GET_LOCK(?, 15)');
        $statement->execute(['expertsacademy_studio_migrations']);
        if ((int) $statement->fetchColumn() !== 1) {
            throw new RuntimeException('Another migration is running (could not acquire lock).');
        }
        try {
            $work();
        } finally {
            $pdo->prepare('SELECT RELEASE_LOCK(?)')->execute(['expertsacademy_studio_migrations']);
        }
    }

    /**
     * Splits a SQL file into statements while respecting quotes and comments.
     *
     * @return list<string>
     */
    public function split(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $length = strlen($sql);
        $quote = null;
        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $i + 1 < $length ? $sql[$i + 1] : '';

            if ($quote !== null) {
                $buffer .= $char;
                if ($char === '\\' && $quote !== '`') {
                    if ($next !== '') {
                        $buffer .= $next;
                        $i++;
                    }
                    continue;
                }
                if ($char === $quote) {
                    if ($next === $quote) {
                        $buffer .= $next;
                        $i++;
                        continue;
                    }
                    $quote = null;
                }
                continue;
            }

            if ($char === '-' && $next === '-') {
                while ($i < $length && $sql[$i] !== "\n") {
                    $i++;
                }
                $buffer .= "\n";
                continue;
            }
            if ($char === '/' && $next === '*') {
                $i += 2;
                while ($i + 1 < $length && !($sql[$i] === '*' && $sql[$i + 1] === '/')) {
                    $i++;
                }
                $i++;
                continue;
            }
            if ($char === "'" || $char === '"' || $char === '`') {
                $quote = $char;
                $buffer .= $char;
                continue;
            }
            if ($char === ';') {
                $trimmed = trim($buffer);
                if ($trimmed !== '') {
                    $statements[] = $trimmed;
                }
                $buffer = '';
                continue;
            }
            $buffer .= $char;
        }
        $trimmed = trim($buffer);
        if ($trimmed !== '') {
            $statements[] = $trimmed;
        }
        return $statements;
    }
}
