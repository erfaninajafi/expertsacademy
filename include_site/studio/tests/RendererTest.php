<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Render\Renderer;
use PHPUnit\Framework\TestCase;

final class RendererTest extends TestCase
{
    public function testFragmentRendersDirectionAndSections(): void
    {
        $html = (new Renderer())->fragment(Samples::page('en'));
        self::assertStringContainsString('class="cms-page"', $html);
        self::assertStringContainsString('dir="ltr"', $html);
        self::assertStringContainsString('<section', $html);
        self::assertStringContainsString('cms-hero', $html);
    }

    public function testPersianLocaleRendersRtl(): void
    {
        $html = (new Renderer())->fragment(Samples::page('fa'));
        self::assertStringContainsString('dir="rtl"', $html);
        self::assertStringContainsString('lang="fa"', $html);
    }

    public function testPlainTextPropsAreEscaped(): void
    {
        $document = Samples::page();
        $document['sections'][0]['components'][0]['props']['heading'] = '<script>alert(1)</script>';
        $html = (new Renderer())->fragment($document);
        self::assertStringNotContainsString('<script>alert(1)</script>', $html);
        self::assertStringContainsString('&lt;script&gt;', $html);
    }

    public function testRichTextIsSanitizedAtRenderTime(): void
    {
        $document = Samples::page();
        $document['sections'][1] = Samples::section('Prose', [[
            'id' => \ExpertsAcademy\Studio\Uuid::v4(),
            'type' => 'rich-text',
            'version' => 1,
            'variant' => 'article',
            'props' => ['body' => '<p>Safe</p><script>alert(1)</script><img src=x onerror="alert(2)">'],
            'design' => ['align' => 'start', 'tone' => 'default', 'spacing' => 'md'],
            'responsive' => [],
            'animation' => ['preset' => 'none', 'duration' => 480, 'delay' => 0, 'easing' => 'calm'],
            'visibility' => ['devices' => ['desktop']],
            'a11y' => ['landmark' => 'none', 'label' => '', 'headingLevel' => 2],
        ]]);
        $html = (new Renderer())->fragment($document);
        self::assertStringNotContainsString('<script>alert(1)</script>', $html);
        self::assertStringNotContainsString('onerror', $html);
        self::assertStringContainsString('Safe', $html);
    }

    public function testHiddenDevicesBecomeStaticClassesNotInlineDisplay(): void
    {
        $document = Samples::page();
        $document['sections'][0]['components'][0]['visibility']['devices'] = ['desktop'];
        $html = (new Renderer())->fragment($document);
        self::assertStringContainsString('cms-hide-mobile', $html);
        self::assertStringContainsString('cms-hide-tablet', $html);
    }

    public function testDocumentIncludesCspCompatibleAssets(): void
    {
        $html = (new Renderer())->document(Samples::page(), '/asset_site/css/cms-pages.css', '/asset_site/js/cms-runtime.js');
        self::assertStringContainsString('<html', $html);
        self::assertStringContainsString('/asset_site/css/cms-pages.css', $html);
        self::assertStringContainsString('/asset_site/js/cms-runtime.js', $html);
        self::assertStringContainsString('name="description"', $html);
    }

    public function testCspPolicyBlocksInlineScript(): void
    {
        $policy = Renderer::policy();
        self::assertStringContainsString("default-src 'none'", $policy);
        self::assertStringContainsString("script-src 'self'", $policy);
        self::assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $policy);
    }

    public function testDocumentRendersSeoCanonicalOpenGraphAndStructuredData(): void
    {
        $document = Samples::page('en');
        $document['seo']['title'] = 'SEO title';
        $document['seo']['description'] = 'A useful description of the page.';
        $document['seo']['robots'] = 'noindex,follow';
        $html = (new Renderer())->document(
            $document,
            '/asset_site/css/cms-pages.css',
            '',
            'https://example.test/expertsacademy/en/seo-page'
        );
        self::assertStringContainsString('<link rel="canonical" href="https://example.test/expertsacademy/en/seo-page">', $html);
        self::assertStringContainsString('<meta name="robots" content="noindex,follow">', $html);
        self::assertStringContainsString('property="og:title" content="SEO title"', $html);
        self::assertStringContainsString('application/ld+json', $html);
        self::assertStringContainsString('"@type":"WebPage"', $html);
    }
}
