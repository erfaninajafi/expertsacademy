<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\DesignTokens;
use PHPUnit\Framework\TestCase;

final class DesignTokensTest extends TestCase
{
    public function testDefaultsAreValid(): void
    {
        self::assertSame([], DesignTokens::errors(DesignTokens::defaults()));
        self::assertSame([], DesignTokens::errors(DesignTokens::defaults('dark')));
    }

    public function testRejectsCssInjection(): void
    {
        $tokens = DesignTokens::defaults();
        $tokens['colors']['light']['brand'] = '#fff;}</style><script>alert(1)</script>';
        self::assertNotEmpty(DesignTokens::errors($tokens));
    }

    public function testRejectsUnknownTokenGroup(): void
    {
        $tokens = DesignTokens::defaults();
        $tokens['customCss'] = 'body{display:none}';
        self::assertArrayHasKey('tokens.customCss', DesignTokens::errors($tokens));
    }

    public function testCssExposesOnlyGeneratedCustomProperties(): void
    {
        $css = DesignTokens::css(DesignTokens::defaults());
        self::assertStringContainsString('--cms-brand', $css);
        self::assertStringContainsString('--cms-canvas', $css);
        self::assertStringNotContainsString('<script', $css);
        self::assertStringNotContainsString('</style>', $css);
    }
}
