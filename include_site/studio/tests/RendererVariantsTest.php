<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Tests;

use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Render\Renderer;
use PHPUnit\Framework\TestCase;

final class RendererVariantsTest extends TestCase
{
    public function testEveryVariantRendersMarkupForItsType(): void
    {
        $renderer = new Renderer();
        $checked = 0;
        foreach (ComponentRegistry::all() as $type => $definition) {
            foreach ($definition['variants'] as $variant) {
                $html = $renderer->fragment(Samples::single($type, $variant['id']));
                $label = $type . '/' . $variant['id'];
                self::assertStringContainsString('class="cms-component cms-' . $type, $html, $label . ' wrapper');
                self::assertStringContainsString('cms-variant-' . $variant['id'], $html, $label . ' variant class');
                self::assertGreaterThan(300, strlen($html), $label . ' output is suspiciously small');
                self::assertStringNotContainsString('Fatal error', $html, $label);
                self::assertStringNotContainsString('Warning:', $html, $label);
                $checked++;
            }
        }
        self::assertGreaterThanOrEqual(36, $checked, 'Variant coverage regressed.');
    }

    public function testEveryVariantRendersRightToLeft(): void
    {
        $renderer = new Renderer();
        foreach (ComponentRegistry::all() as $type => $definition) {
            foreach ($definition['variants'] as $variant) {
                $html = $renderer->fragment(Samples::single($type, $variant['id'], 'fa'));
                self::assertStringContainsString('dir="rtl"', $html, $type . '/' . $variant['id']);
                self::assertStringContainsString('lang="fa"', $html, $type . '/' . $variant['id']);
            }
        }
    }

    public function testNewPremiumComponentsExposeTheirStructuralMarkers(): void
    {
        $expectations = [
            'hero-cinematic' => ['cms-hcin--overlay', 'cms-hcin--split', 'cms-hcin__statement'],
            'bento-grid' => ['cms-bento--mosaic', 'cms-bento__tile--hero', 'cms-bento__metric'],
            'pricing' => ['cms-pricing__tiers', 'cms-pricing__table', 'cms-pricing__feature'],
            'timeline' => ['cms-timeline--vertical', 'cms-timeline--alternating', 'cms-timeline__list'],
        ];
        $renderer = new Renderer();
        foreach ($expectations as $type => $markers) {
            $html = '';
            foreach (ComponentRegistry::variantIds($type) as $variant) {
                $html .= $renderer->fragment(Samples::single($type, $variant));
            }
            foreach ($markers as $marker) {
                self::assertStringContainsString($marker, $html, $type . ' should render ' . $marker);
            }
        }
    }

    public function testGalleryDocumentContainsEveryType(): void
    {
        $html = (new Renderer())->document(
            Samples::demo('en'),
            '/expertsacademy/asset_site/css/cms-pages.css',
            '/expertsacademy/asset_site/js/cms-runtime.js'
        );
        foreach (array_keys(ComponentRegistry::all()) as $type) {
            self::assertStringContainsString('cms-' . $type, $html, 'Gallery misses ' . $type);
        }
    }
}
