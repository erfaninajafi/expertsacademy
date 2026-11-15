<?php
declare(strict_types=1);

/** Migration CLI. Usage: php bin/migrate.php <status|up|down|seed> [options] */

require dirname(__DIR__) . '/bootstrap.php';

use ExpertsAcademy\Studio\Database\Migrator;
use ExpertsAcademy\Studio\Database\Seeder;
use ExpertsAcademy\Studio\Env;

$command = $argv[1] ?? 'status';
$options = [];
foreach (array_slice($argv, 2) as $argument) {
    if (str_starts_with($argument, '--')) {
        [$key, $value] = array_pad(explode('=', substr($argument, 2), 2), 2, true);
        $options[$key] = $value;
    }
}

$config = Env::db();
fwrite(STDOUT, "database: {$config['name']} @ {$config['host']}:{$config['port']}\n");
$migrator = new Migrator(dirname(__DIR__) . '/database/migrations');

try {
    switch ($command) {
        case 'status':
            $rows = $migrator->status();
            if ($rows === []) {
                fwrite(STDOUT, "no migrations found\n");
            }
            foreach ($rows as $row) {
                printf("%-32s %-9s %s\n", $row['version'], $row['state'], substr($row['checksum'], 0, 12));
            }
            break;

        case 'up':
            $dry = ($options['dry-run'] ?? false) === true;
            $pending = $migrator->up($dry);
            if ($dry) {
                fwrite(STDOUT, $pending === [] ? "nothing to apply\n" : "pending: " . implode(', ', $pending) . "\n");
            } elseif ($pending === []) {
                fwrite(STDOUT, "nothing to apply\n");
            }
            if (!$dry && ($options['no-seed'] ?? false) !== true) {
                $counts = (new Seeder())->run();
                fwrite(STDOUT, 'seed: ' . json_encode($counts) . "\n");
            }
            break;

        case 'down':
            $to = isset($options['to']) && is_string($options['to']) ? $options['to'] : null;
            $rolled = $migrator->down($to, ($options['force'] ?? false) === true);
            if ($rolled === []) {
                fwrite(STDOUT, "nothing to roll back\n");
            }
            break;

        case 'seed':
            $counts = (new Seeder())->run();
            fwrite(STDOUT, 'seed: ' . json_encode($counts) . "\n");
            break;

        default:
            fwrite(STDERR, "unknown command: {$command}\n");
            fwrite(STDERR, "usage: php bin/migrate.php <status|up|down|seed>\n");
            exit(2);
    }
} catch (Throwable $error) {
    fwrite(STDERR, 'migration error: ' . $error->getMessage() . "\n");
    exit(1);
}
