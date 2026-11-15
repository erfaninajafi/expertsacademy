<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio;

use RuntimeException;

/**
 * A failure that is safe to translate into an HTTP response.
 * Messages are written for operators and never contain secrets or SQL.
 */
final class Problem extends RuntimeException
{
    /** @param array<string,string> $errors */
    public function __construct(
        public readonly int $status,
        string $message,
        public readonly array $errors = []
    ) {
        parent::__construct($message);
    }

    /** @param array<string,string> $errors */
    public static function invalid(array $errors, string $message = 'The request is invalid.'): self
    {
        return new self(422, $message, $errors);
    }
}
