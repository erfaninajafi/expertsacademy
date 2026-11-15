<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Render;

use ExpertsAcademy\Studio\Html;

/** Small shared markup builders used by component templates. */
final class Partial
{
    /** @param array{url:string,alt:string,width:int|null,height:int|null,srcset?:list<string>,focal?:string}|null $image */
    public static function figure(?array $image, string $alt, string $class = '', bool $eager = false): string
    {
        if ($image === null) {
            return '<div class="cms-figure cms-figure--placeholder ' . Html::e($class) . '" role="img" aria-label="'
                . Html::e($alt !== '' ? $alt : 'Image placeholder') . '"><span aria-hidden="true">✦</span></div>';
        }
        $srcset = $image['srcset'] ?? [];
        $attributes = Html::attrs([
            'class'         => 'cms-figure__img ' . $class,
            'src'           => $image['url'],
            'srcset'        => $srcset !== [] ? implode(', ', $srcset) : null,
            'sizes'         => $srcset !== [] ? '(max-width: 834px) 100vw, 50vw' : null,
            'alt'           => $alt !== '' ? $alt : $image['alt'],
            'width'         => $image['width'] ?? null,
            'height'        => $image['height'] ?? null,
            'loading'       => $eager ? 'eager' : 'lazy',
            'fetchpriority' => $eager ? 'high' : null,
            'decoding'      => 'async',
            'style'         => isset($image['focal']) && $image['focal'] !== '50% 50%' ? 'object-position:' . $image['focal'] : null,
        ]);
        return '<figure class="cms-figure"><img' . $attributes . '></figure>';
    }

    /** @param array{label:string,href:string} $cta */
    public static function button(array $cta, string $tone = 'primary'): string
    {
        return '<a class="cms-button cms-button--' . Html::e($tone) . '" href="' . Html::e($cta['href']) . '">'
            . Html::e($cta['label']) . '</a>';
    }

    public static function heading(int $level, string $text, string $class = 'cms-heading'): string
    {
        $level = max(2, min(4, $level));
        return '<h' . $level . ' class="' . Html::e($class) . '">' . Html::e($text) . '</h' . $level . '>';
    }
}
