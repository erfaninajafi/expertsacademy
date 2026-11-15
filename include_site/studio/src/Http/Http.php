<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Http;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Problem;
use Throwable;

/** JSON transport helpers with strict input limits and safe error mapping. */
final class Http
{
    private const MAX_BODY_BYTES = 2 * 1024 * 1024;

    public static function headers(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, max-age=0');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: same-origin');
        header('X-Frame-Options: SAMEORIGIN');
    }

    /** @return array<string,mixed> */
    public static function body(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return [];
        }
        if (strlen($raw) > self::MAX_BODY_BYTES) {
            throw new Problem(413, 'The request body is too large.');
        }
        try {
            $decoded = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new Problem(400, 'The request body must be valid JSON.');
        }
        if (!is_array($decoded)) {
            throw new Problem(400, 'The request body must be a JSON object.');
        }
        return $decoded;
    }

    /** Rejects unexpected top-level keys (mass-assignment guard). @param list<string> $allowed */
    public static function only(array $body, array $allowed): void
    {
        foreach (array_keys($body) as $key) {
            if (!in_array($key, $allowed, true)) {
                throw new Problem(400, 'Unexpected field: ' . $key);
            }
        }
    }

    /** @return array<string,mixed> */
    public static function document(array $body): array
    {
        $document = $body['document'] ?? null;
        if (!is_array($document)) {
            throw new Problem(400, 'A page document is required.');
        }
        return $document;
    }

    public static function version(array $body): int
    {
        $value = $body['expectedVersion'] ?? null;
        if (!is_int($value) || $value < 1) {
            throw new Problem(400, 'expectedVersion must be a positive integer.');
        }
        return $value;
    }

    /** @param array<string,mixed> $payload */
    public static function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        echo Json::encode($payload);
        exit;
    }

    public static function error(Throwable $error): never
    {
        if ($error instanceof Problem) {
            $payload = ['ok' => false, 'error' => $error->getMessage()];
            if ($error->errors !== []) {
                $payload['errors'] = $error->errors;
            }
            self::json($payload, $error->status);
        }
        Db::log('unhandled: ' . get_class($error) . ': ' . $error->getMessage());
        self::json(['ok' => false, 'error' => 'An unexpected error occurred.'], 500);
    }

    public static function requireMethod(string ...$allowed): string
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        if (!in_array($method, $allowed, true)) {
            header('Allow: ' . implode(', ', $allowed));
            throw new Problem(405, 'Method not allowed.');
        }
        return $method;
    }
}
