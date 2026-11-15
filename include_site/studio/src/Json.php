<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio;

use JsonException;
use stdClass;

final class Json
{
    public const FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR;

    /** @throws JsonException */
    public static function decode(string $json): mixed
    {
        $value = json_decode($json, false, 128, JSON_THROW_ON_ERROR);
        return self::toArrayShape($value);
    }

    private static function toArrayShape(mixed $value): mixed
    {
        if ($value instanceof stdClass) {
            $value = get_object_vars($value);
        }
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = self::toArrayShape($item);
            }
        }
        return $value;
    }

    public static function encode(mixed $value): string
    {
        return json_encode($value, self::FLAGS);
    }

    /** Canonical encoding used for checksums: sorted keys, no whitespace. */
    public static function canonical(mixed $value): string
    {
        if (is_array($value)) {
            if (!array_is_list($value)) {
                ksort($value);
            }
            foreach ($value as $key => $item) {
                $value[$key] = self::canonicalValue($item);
            }
        }
        return json_encode($value, self::FLAGS);
    }

    private static function canonicalValue(mixed $value): mixed
    {
        if (is_array($value)) {
            if (!array_is_list($value)) {
                ksort($value);
            }
            foreach ($value as $key => $item) {
                $value[$key] = self::canonicalValue($item);
            }
        }
        return $value;
    }

    public static function checksum(mixed $value): string
    {
        return hash('sha256', self::canonical($value));
    }
}
