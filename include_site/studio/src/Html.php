<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio;

/** Output encoding helpers. Every dynamic value passes through Html::e(). */
final class Html
{
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** @param array<string,string|int|bool|null> $attributes */
    public static function attrs(array $attributes): string
    {
        $parts = [];
        foreach ($attributes as $name => $value) {
            if (!preg_match('/\A[a-zA-Z][a-zA-Z0-9:_-]*\z/D', $name)) {
                continue;
            }
            if ($value === null || $value === false) {
                continue;
            }
            if ($value === true) {
                $parts[] = $name;
                continue;
            }
            $parts[] = $name . '="' . self::e($value) . '"';
        }
        return $parts === [] ? '' : ' ' . implode(' ', $parts);
    }

    /** Safe CSS class list from validated tokens. */
    public static function classes(string ...$classes): string
    {
        $clean = [];
        foreach ($classes as $class) {
            foreach (preg_split('/\s+/', trim($class)) ?: [] as $part) {
                if ($part !== '' && preg_match('/\A[a-zA-Z0-9_-]+\z/D', $part)) {
                    $clean[] = $part;
                }
            }
        }
        return implode(' ', array_unique($clean));
    }
}
