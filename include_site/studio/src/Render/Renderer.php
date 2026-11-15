<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Render;

use ExpertsAcademy\Studio\DesignTokens;
use ExpertsAcademy\Studio\Html;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Sanitizer;
use ExpertsAcademy\Studio\Seo\SeoRenderer;
use Throwable;

/**
 * Deterministic server-side renderer for the page contract.
 *
 * Security model:
 *  - the component type must exist in the code-owned registry;
 *  - the template path comes from the registry, never from the document;
 *  - every scalar is escaped with Html::e();
 *  - rich text is sanitized through an allowlist before output;
 *  - CSS values are generated from validated enums/integers only.
 *
 * The renderer never requires Node.js, Vite or a browser runtime.
 */
final class Renderer
{
    private const TEMPLATE_DIR = __DIR__ . '/templates/';

    public function __construct(private readonly MediaResolver $media = new NullMediaResolver()) {}

    public static function policy(): string
    {
        return "default-src 'none'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; "
            . "script-src 'self'; font-src 'self'; connect-src 'self'; base-uri 'none'; "
            . "form-action 'self'; frame-ancestors 'self'";
    }

    public function document(array $document, string $cssHref, string $runtimeHref = '', string $pageUrl = ''): string
    {
        $tokens = $document['theme']['tokens'] ?? DesignTokens::defaults();
        $direction = $document['direction'] === 'rtl' ? 'rtl' : 'ltr';
        $title = (string) ($document['seo']['title'] ?: $document['title']);
        $description = (string) ($document['seo']['description'] ?? '');
        $keywords = implode(', ', array_map('strval', $document['seo']['keywords'] ?? []));
        $css = DesignTokens::css($tokens) . $this->layoutCss($document);

        $head = '<!doctype html><html lang="' . Html::e($document['locale']) . '" dir="' . $direction . '"><head>'
            . '<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>' . Html::e($title) . '</title>'
            . '<meta name="description" content="' . Html::e($description) . '">'
            . ($keywords !== '' ? '<meta name="keywords" content="' . Html::e($keywords) . '">' : '')
            . ($pageUrl !== '' ? SeoRenderer::head($document, $this->media, $pageUrl) : '')
            . '<link rel="stylesheet" href="' . Html::e($cssHref) . '">'
            . '<style>' . $css . '</style></head><body class="cms-preview-body">';

        $needsRuntime = $this->hasMotion($document);
        $script = $needsRuntime && $runtimeHref !== ''
            ? '<script src="' . Html::e($runtimeHref) . '" defer></script>'
            : '';

        return $head . $this->fragment($document) . $script . '</body></html>';
    }

    public function needsRuntime(array $document): bool
    {
        return $this->hasMotion($document);
    }

    public function fragment(array $document): string
    {
        $tokens = $document['theme']['tokens'] ?? DesignTokens::defaults();
        $direction = $document['direction'] === 'rtl' ? 'rtl' : 'ltr';
        $html = '<div class="cms-page" dir="' . $direction . '" lang="' . Html::e($document['locale']) . '">'
            . '<style>' . DesignTokens::css($tokens) . $this->layoutCss($document) . '</style>';

        foreach ($document['sections'] as $sectionIndex => $section) {
            $html .= $this->section($section, (int) $sectionIndex);
        }
        return $html . '</div>';
    }

    private function section(array $section, int $index): string
    {
        $layout = $section['layout'];
        $classes = Html::classes(
            'cms-section',
            'cms-section--bg-' . $layout['background'],
            'cms-section--' . $layout['width'],
            $this->visibilityClasses($section['visibility']['devices'])
        );
        $style = sprintf(
            '--cms-section-gap:%dpx;--cms-section-pad:%dpx;--cms-cols:%d;',
            (int) $layout['gap'],
            (int) $layout['paddingY'],
            (int) $layout['columns']
        );
        $attributes = Html::attrs([
            'id' => 'section-' . $index,
            'class' => $classes,
            'style' => $style,
            'data-cms-section' => (string) $index,
            'aria-label' => $section['label'] !== '' ? $section['label'] : null,
        ]);
        $inner = '';
        foreach ($section['components'] as $component) {
            $inner .= $this->component($component, (int) $layout['columns']);
        }
        $grid = '<div class="cms-grid">' . $inner . '</div>';

        $container = $layout['width'] === 'contained'
            ? '<div class="cms-container">' . $grid . '</div>'
            : '<div class="cms-container cms-container--full">' . $grid . '</div>';

        return '<section' . $attributes . '>' . $container . '</section>';
    }

    private function component(array $component, int $sectionColumns): string
    {
        $definition = ComponentRegistry::get((string) $component['type']);
        if ($definition === null) {
            // A stored document can only contain registry types; if the registry
            // changed under a published revision, fail closed and visible.
            return '';
        }
        $variantIds = ComponentRegistry::variantIds((string) $component['type']);
        $variant = in_array($component['variant'], $variantIds, true) ? $component['variant'] : ($variantIds[0] ?? 'default');

        $headingLevel = max(2, min(4, (int) ($component['a11y']['headingLevel'] ?? 2)));
        $uid = substr(hash('sha256', (string) $component['id']), 0, 10);
        $ctx = [
            'component'    => $component,
            'props'        => $component['props'],
            'variant'      => $variant,
            'headingLevel' => $headingLevel,
            'uid'          => $uid,
            'e'            => static fn (mixed $value): string => Html::e($value),
            'rich'         => static fn (string $html): string => Sanitizer::richText($html),
            'media'        => fn (?string $id): ?array => $this->media->resolve($id),
            'url'          => static fn (string $path): string => Html::e($path),
        ];

        $template = self::TEMPLATE_DIR . $definition['template'];
        if (!is_file($template)) {
            return '';
        }

        ob_start();
        try {
            (static function (array $ctx, string $__template): void {
                require $__template;
            })($ctx, $template);
        } catch (Throwable $error) {
            ob_end_clean();
            \ExpertsAcademy\Studio\Db::log('render failed for ' . $component['type'] . ': ' . $error->getMessage());
            return '';
        }
        $inner = (string) ob_get_clean();
        if (trim($inner) === '') {
            return '';
        }

        $design = $component['design'] ?? ['align' => 'start', 'tone' => 'default', 'spacing' => 'md'];
        $classes = Html::classes(
            'cms-component',
            'cms-' . $component['type'],
            'cms-tone-' . $design['tone'],
            'cms-align-' . $design['align'],
            'cms-space-' . $design['spacing'],
            'cms-variant-' . $variant,
            $this->visibilityClasses($component['visibility']['devices'])
        );
        $style = $this->componentCss((string) $component['id'], $component, $sectionColumns);
        $animation = $component['animation'] ?? [];
        $motion = $animation['preset'] !== 'none'
            ? Html::attrs([
                'data-cms-motion' => $animation['preset'],
                'data-cms-duration' => (string) $animation['duration'],
                'data-cms-delay' => (string) $animation['delay'],
                'data-cms-easing' => $animation['easing'],
            ])
            : '';

        $landmark = $component['a11y']['landmark'] ?? 'none';
        $role = $landmark === 'none' ? '' : ' role="' . Html::e($landmark) . '"';
        $label = ($component['a11y']['label'] ?? '') !== ''
            ? ' aria-label="' . Html::e($component['a11y']['label']) . '"'
            : '';

        return '<div class="' . $classes . '" data-cms-id="' . Html::e($component['id']) . '"'
            . ' data-cms-type="' . Html::e($component['type']) . '"' . $role . $label . $motion . '>'
            . ($style !== '' ? '<style>' . $style . '</style>' : '')
            . $inner . '</div>';
    }

    /** Per-instance responsive span/hide rules, generated from validated integers/enums only. */
    private function componentCss(string $id, array $component, int $sectionColumns): string
    {
        $selector = '[data-cms-id="' . $id . '"]';
        $rules = [];
        $overrides = $component['responsive'] ?? [];
        $breakpoints = ['desktop' => '1024px', 'tablet' => '768px', 'mobile' => '0px'];

        $span = $sectionColumns;
        foreach (['mobile', 'tablet', 'desktop'] as $breakpoint) {
            if (isset($overrides[$breakpoint]['columns'])) {
                $span = max(1, min($sectionColumns, (int) $overrides[$breakpoint]['columns']));
            }
            $declarations = 'grid-column:span ' . $span . ';';
            if (isset($overrides[$breakpoint]['align'])) {
                $declarations .= '--cms-component-align:' . match ($overrides[$breakpoint]['align']) {
                    'center' => 'center', 'end' => 'flex-end', default => 'flex-start'
                } . ';';
            }
            if (isset($overrides[$breakpoint]['size'])) {
                $declarations .= '--cms-component-size:' . $overrides[$breakpoint]['size'] . ';';
            }
            if (($overrides[$breakpoint]['hide'] ?? false) === true) {
                $declarations .= 'display:none!important;';
            }
            if ($breakpoint === 'desktop') {
                $rules[] = $selector . '{' . $declarations . '}';
            } elseif ($breakpoint === 'tablet') {
                $rules[] = '@media (max-width:' . $breakpoints['desktop'] . '){' . $selector . '{' . $declarations . '}}';
            } else {
                $rules[] = '@media (max-width:' . $breakpoints['tablet'] . '){' . $selector . '{' . $declarations . '}}';
            }
        }
        return implode('', $rules);
    }

    private function layoutCss(array $document): string
    {
        $container = (int) ($document['settings']['container'] ?? 1280);
        $background = match ($document['settings']['background'] ?? 'canvas') {
            'surface' => 'var(--cms-surface)',
            'muted' => 'var(--cms-canvas)',
            default => 'var(--cms-canvas)',
        };
        return '.cms-page{--cms-page-container:' . $container . 'px;background:' . $background . ';}';
    }

    /** @param list<string> $devices */
    private function visibilityClasses(array $devices): string
    {
        if ($devices === ['desktop', 'tablet', 'mobile']) {
            return '';
        }
        $hidden = array_values(array_diff(['desktop', 'tablet', 'mobile'], $devices));
        $classes = [];
        foreach ($hidden as $device) {
            $classes[] = 'cms-hide-' . $device;
        }
        return implode(' ', $classes);
    }

    private function hasMotion(array $document): bool
    {
        foreach ($document['sections'] as $section) {
            foreach ($section['components'] as $component) {
                if (($component['animation']['preset'] ?? 'none') !== 'none') {
                    return true;
                }
            }
        }
        return false;
    }
}
