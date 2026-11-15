<?php
declare(strict_types=1);

/**
 * Studio backup and restore drill.
 *
 *   php bin/backup.php create
 *   php bin/backup.php drill storage/backups/studio-YYYYmmdd-HHMMSS.sql
 *
 * `create` writes a mysqldump of every tbl_studio_* table plus a JSON manifest
 * with row counts and a SHA-256 checksum. `drill` restores that dump into
 * scratch tables (`ea_drill_*`) inside the same database, compares every row
 * count, then removes the scratch tables. It never touches the live tables and
 * does not need CREATE DATABASE.
 */

require dirname(__DIR__) . '/bootstrap.php';

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Env;

$config = Env::db();
$root = dirname(__DIR__, 3);
$directory = $root . '/storage/backups';
$mysqldump = getenv('EA_MYSQLDUMP') ?: '/opt/lampp/bin/mysqldump';
$tar = getenv('EA_TAR') ?: '/bin/tar';
$mediaDirectory = Env::paths()['media'];
$mysql = getenv('EA_MYSQL') ?: '/opt/lampp/bin/mysql';

if (!is_dir($directory) && !mkdir($directory, 0770, true)) {
    fwrite(STDERR, "Cannot create {$directory}\n");
    exit(1);
}

/** @return list<string> */
function studioTables(): array
{
    return array_map(
        static fn (array $row): string => (string) array_values($row)[0],
        Db::all("SHOW TABLES LIKE 'tbl_studio_%'")
    );
}

/** @param list<string> $tables @return array<string,int> */
function rowCounts(array $tables): array
{
    $counts = [];
    foreach ($tables as $table) {
        $row = Db::one('SELECT COUNT(*) AS total FROM `' . $table . '`');
        $counts[$table] = (int) ($row['total'] ?? 0);
    }
    return $counts;
}

/** @param list<string> $command */
function runProcess(array $command, array $stdin, ?string $stdoutFile = null): string
{
    global $config;
    $descriptors = [
        0 => $stdin['type'] === 'file'
            ? ['file', $stdin['value'], 'r']
            : ['pipe', 'r'],
        1 => $stdoutFile !== null ? ['file', $stdoutFile, 'w'] : ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $environment = getenv();
    $environment['MYSQL_PWD'] = $config['pass'];
    $process = proc_open($command, $descriptors, $pipes, null, $environment);
    if (!is_resource($process)) {
        throw new RuntimeException('Could not start ' . $command[0]);
    }
    if ($stdin['type'] === 'pipe') {
        fwrite($pipes[0], $stdin['value']);
        fclose($pipes[0]);
    }
    $output = '';
    if ($stdoutFile === null) {
        $output = (string) stream_get_contents($pipes[1]);
    }
    $errors = (string) stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    if ($stdoutFile === null) {
        fclose($pipes[1]);
    }
    $status = proc_close($process);
    if ($status !== 0) {
        throw new RuntimeException(trim($errors) !== '' ? trim($errors) : $command[0] . ' exited with ' . $status);
    }
    return $output;
}

/** @param list<string> $tables */
function dropScratch(array $tables, string $prefix): void
{
    Db::run('SET FOREIGN_KEY_CHECKS=0');
    foreach ($tables as $table) {
        Db::run('DROP TABLE IF EXISTS `' . $prefix . $table . '`');
    }
    Db::run('SET FOREIGN_KEY_CHECKS=1');
}

$command = $argv[1] ?? 'create';

try {
    if ($command === 'create') {
        $tables = studioTables();
        if ($tables === []) {
            throw new RuntimeException('No tbl_studio_* tables found.');
        }
        $file = $directory . '/studio-' . gmdate('Ymd-His') . '.sql';
        $arguments = [
            $mysqldump,
            '--single-transaction',
            '--skip-lock-tables',
            '--no-tablespaces',
            '--skip-comments',
            '--default-character-set=utf8mb4',
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['user'],
            $config['name'],
            ...$tables,
        ];
        runProcess($arguments, ['type' => 'pipe', 'value' => ''], $file);
        $size = filesize($file) ?: 0;
        if ($size === 0) {
            throw new RuntimeException('The dump is empty.');
        }
        $media = null;
        if (is_dir($mediaDirectory)) {
            $files = 0;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($mediaDirectory, FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    $files++;
                }
            }
            if ($files > 0) {
                $archive = preg_replace('/\.sql$/', '.media.tar.gz', $file);
                runProcess([
                    $tar,
                    '-czf',
                    $archive,
                    '-C',
                    dirname($mediaDirectory),
                    basename($mediaDirectory),
                ], ['type' => 'pipe', 'value' => '']);
                $media = [
                    'file' => basename($archive),
                    'bytes' => filesize($archive) ?: 0,
                    'sha256' => hash_file('sha256', $archive),
                    'files' => $files,
                ];
            }
        }
        $manifest = [
            'database' => $config['name'],
            'created_at' => gmdate('c'),
            'file' => basename($file),
            'bytes' => $size,
            'sha256' => hash_file('sha256', $file),
            'tables' => rowCounts($tables),
            'media' => $media,
        ];
        file_put_contents($file . '.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
        echo json_encode(['ok' => true, 'backup' => $file, 'bytes' => $size, 'sha256' => $manifest['sha256'], 'tables' => count($tables)], JSON_PRETTY_PRINT) . "\n";
        exit(0);
    }

    if ($command === 'drill') {
        $file = $argv[2] ?? '';
        if ($file !== '' && !str_starts_with($file, '/')) {
            $file = $root . '/' . $file;
        }
        if (!is_file($file)) {
            throw new RuntimeException('Backup file not found. Run create first.');
        }
        $manifest = json_decode((string) file_get_contents($file . '.json'), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($manifest) || !isset($manifest['tables'])) {
            throw new RuntimeException('Backup manifest is invalid.');
        }
        $actual = hash_file('sha256', $file);
        if (!hash_equals((string) $manifest['sha256'], $actual)) {
            throw new RuntimeException('Backup checksum does not match its manifest.');
        }

        $tables = array_keys($manifest['tables']);
        $prefix = 'ea_drill_';
        $scratch = array_map(static fn (string $table): string => $prefix . $table, $tables);
        $temporary = $directory . '/drill-' . getmypid() . '.sql';
        dropScratch($tables, $prefix);

        try {
            $sql = (string) file_get_contents($file);
            foreach ($tables as $index => $table) {
                $sql = str_replace('`' . $table . '`', '`' . $scratch[$index] . '`', $sql);
            }
            // InnoDB constraint names are schema-global; rename them so the
            // scratch tables do not collide with the live constraints.
            $sql = (string) preg_replace('/CONSTRAINT `([A-Za-z0-9_]+)`/', 'CONSTRAINT `' . $prefix . '$1`', $sql);
            file_put_contents($temporary, $sql);

            runProcess([
                $mysql,
                '--default-character-set=utf8mb4',
                '--host=' . $config['host'],
                '--port=' . $config['port'],
                '--user=' . $config['user'],
                $config['name'],
            ], ['type' => 'file', 'value' => $temporary]);

            $verified = [];
            foreach ($tables as $index => $table) {
                $row = Db::one('SELECT COUNT(*) AS total FROM `' . $scratch[$index] . '`');
                $count = (int) ($row['total'] ?? 0);
                if ($count !== (int) $manifest['tables'][$table]) {
                    throw new RuntimeException(sprintf(
                        'Row count mismatch for %s: backup %d, restored %d.',
                        $table,
                        $manifest['tables'][$table],
                        $count
                    ));
                }
                $verified[$table] = $count;
            }

            $mediaState = 'none';
            if (isset($manifest['media']['file'])) {
                $archive = $directory . '/' . $manifest['media']['file'];
                if (!is_file($archive) || !hash_equals((string) $manifest['media']['sha256'], (string) hash_file('sha256', $archive))) {
                    throw new RuntimeException('Media archive is missing or its checksum does not match.');
                }
                $mediaState = 'verified';
            }

            echo json_encode([
                'ok' => true,
                'backup' => basename($file),
                'checksum' => 'verified',
                'scratch_tables' => count($scratch),
                'verified_rows' => array_sum($verified),
                'media_archive' => $mediaState,
                'tables' => $verified,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        } finally {
            dropScratch($tables, $prefix);
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
        exit(0);
    }

    fwrite(STDERR, "Usage: php bin/backup.php <create|drill> [dump.sql]\n");
    exit(2);
} catch (Throwable $error) {
    fwrite(STDERR, 'Backup failed: ' . $error->getMessage() . "\n");
    exit(1);
}
