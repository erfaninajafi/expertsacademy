<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Contract;

use ExpertsAcademy\Studio\DesignTokens;
use ExpertsAcademy\Studio\Uuid;

/** Builders for the page contract (schemaVersion 1). */
final class PageDocument
{
    public const SCHEMA_VERSION = 1;
    /** Raised with the Phase 2 catalogue so a full component sample still validates. */
    public const MAX_SECTIONS = 220;
    public const MAX_COMPONENTS_PER_SECTION = 24;

    /** @return array<string,mixed> */
    public static function blank(string $title, string $locale, ?array $tokens = null): array
    {
        $tokens ??= DesignTokens::defaults();
        $direction = $locale === 'fa' ? 'rtl' : 'ltr';
        return [
            'schemaVersion' => self::SCHEMA_VERSION,
            'title' => $title,
            'locale' => $locale,
            'direction' => $direction,
            'seo' => ['title' => $title, 'description' => '', 'keywords' => [], 'canonical' => '', 'ogImage' => null, 'robots' => 'index,follow'],
            'theme' => ['id' => null, 'version' => null, 'tokens' => $tokens],
            'settings' => ['container' => $tokens['layout']['container'], 'background' => 'canvas'],
            'sections' => [[
                'id' => Uuid::v4(),
                'label' => 'Section',
                'layout' => ['width' => 'contained', 'columns' => 12, 'gap' => 24, 'paddingY' => 64, 'background' => 'none'],
                'visibility' => ['devices' => ['desktop', 'tablet', 'mobile']],
                'components' => [],
            ]],
        ];
    }

    /** @param array<string,mixed> $definition */
    public static function component(array $definition, ?array $props = null): array
    {
        $variants = $definition['variants'];
        return [
            'id' => Uuid::v4(),
            'type' => $definition['type'],
            'version' => $definition['version'],
            'variant' => $variants[0]['id'] ?? 'default',
            'props' => $props ?? $definition['defaults'],
            'design' => ['align' => 'start', 'tone' => 'default', 'spacing' => 'md'],
            'responsive' => [],
            'animation' => ['preset' => 'none', 'duration' => 480, 'delay' => 0, 'easing' => 'calm'],
            'visibility' => ['devices' => ['desktop', 'tablet', 'mobile']],
            'a11y' => ['landmark' => 'none', 'label' => '', 'headingLevel' => 2],
        ];
    }

    public static function checksum(array $document): string
    {
        return \ExpertsAcademy\Studio\Json::checksum($document);
    }
}
