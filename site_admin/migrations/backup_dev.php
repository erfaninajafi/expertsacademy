<?php
/** CLI-only logical backup of the configured development database. */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') exit(2);
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
require dirname(__DIR__, 2) . '/include_site/config.php';
if ($dbname !== 'expertsacademy_dev') {
    fwrite(STDERR, "Refusing to back up a non-development database.\n");
    exit(2);
}
$options = getopt('', ['output:']);
$output = $options['output'] ?? '';
if (!is_string($output) || !str_starts_with($output, '/') || !preg_match('/\.sql$/D', $output)) {
    fwrite(STDERR, "Provide an absolute --output path ending in .sql outside the web root.\n");
    exit(2);
}
$directory = realpath(dirname($output));
if ($directory === false || str_starts_with($directory . '/', '/opt/lampp/htdocs/') || file_exists($output)) {
    fwrite(STDERR, "Backup directory is unavailable, public, or the output already exists.\n");
    exit(2);
}
umask(0077);
$command = ['/opt/lampp/bin/mysqldump', '--single-transaction', '--skip-lock-tables',
    '--default-character-set=utf8mb4', '--host=' . $dbserver, '--user=' . $dbusername, $dbname];
$environment = ['MYSQL_PWD' => $dbpassword, 'PATH' => (string) getenv('PATH')];
$process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['file', $output, 'x'], 2 => ['pipe', 'w']], $pipes, null, $environment);
if (!is_resource($process)) {
    fwrite(STDERR, "Could not launch database backup.\n");
    exit(1);
}
fclose($pipes[0]);
$error = stream_get_contents($pipes[2]);
fclose($pipes[2]);
$exit = proc_close($process);
if ($exit !== 0 || !is_file($output) || filesize($output) < 100000) {
    @unlink($output);
    $reason = stripos($error, 'Access denied') !== false ? 'access denied'
        : (stripos($error, 'socket') !== false ? 'socket error'
        : (stripos($error, 'connect') !== false ? 'connection error' : 'other error'));
    fwrite(STDERR, "Development database backup failed ($reason).\n");
    exit(1);
}
chmod($output, 0600);
echo json_encode(['file' => $output, 'bytes' => filesize($output), 'sha256' => hash_file('sha256', $output)]), PHP_EOL;
