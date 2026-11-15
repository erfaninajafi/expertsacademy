<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\Sanitizer;
use PHPUnit\Framework\TestCase;

final class SanitizerTest extends TestCase
{
    public function testRemovesScriptsAndEventHandlers(): void
    {
        $html = '<p onclick="steal()">Hello<script>alert(1)</script><img src="x" onerror="alert(2)"></p>';
        $clean = Sanitizer::richText($html);
        self::assertStringNotContainsString('<script', $clean);
        self::assertStringNotContainsString('onclick', $clean);
        self::assertStringNotContainsString('onerror', $clean);
        self::assertStringContainsString('Hello', $clean);
    }

    public function testRemovesDangerousUrlSchemes(): void
    {
        $clean = Sanitizer::richText('<a href="javascript:alert(1)">link</a><a href="https://example.org/x">ok</a>');
        self::assertStringNotContainsString('javascript:', $clean);
        self::assertStringContainsString('https://example.org/x', $clean);
    }

    public function testKeepsFormattingTags(): void
    {
        $clean = Sanitizer::richText('<h2>Title</h2><p><strong>Bold</strong> and <em>italic</em></p><ul><li>One</li></ul>');
        self::assertStringContainsString('<h2>', $clean);
        self::assertStringContainsString('<strong>', $clean);
        self::assertStringContainsString('<li>', $clean);
    }

    public function testEnforcesLengthLimit(): void
    {
        $clean = Sanitizer::richText('<p>' . str_repeat('a', 25000) . '</p>');
        self::assertLessThanOrEqual(20000, strlen(strip_tags($clean)));
    }

    public function testUrlValidation(): void
    {
        self::assertTrue(Sanitizer::isSafeUrl('/en/courses'));
        self::assertTrue(Sanitizer::isSafeUrl('https://example.org'));
        self::assertTrue(Sanitizer::isSafeUrl('#section'));
        self::assertFalse(Sanitizer::isSafeUrl('javascript:alert(1)'));
        self::assertFalse(Sanitizer::isSafeUrl('data:text/html,<script>alert(1)</script>'));
    }
}
