<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Contract;

use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Uuid;

/** Reproducible documents used by previews, seeds and tests. */
final class Samples
{
    /** @return array<string,mixed> */
    public static function page(string $locale = 'en', ?string $title = null): array
    {
        $document = PageDocument::blank($title ?? ($locale === 'fa' ? 'صفحه نمونه' : 'A page built with the new CMS'), $locale);
        $document['seo']['description'] = 'A page composed from registry components and rendered by PHP.';
        $document['sections'] = [
            self::section('Introduction', [self::component('hero', 'editorial')]),
            self::section('Proof', [self::component('stats', 'inline')]),
            self::section('What we do', [self::component('feature-grid', 'cards')]),
            self::section('Voice', [self::component('quote', 'testimonial')]),
            self::section('Questions', [self::component('accordion', 'faq')]),
            self::section('Next step', [self::component('cta', 'band')]),
        ];
        return $document;
    }

    /** One component in a minimal page, for isolated previews. */
    public static function single(string $type, string $variant, string $locale = 'en'): array
    {
        $definition = ComponentRegistry::get($type);
        if ($definition === null) {
            throw new \InvalidArgumentException('Unknown component type.');
        }
        $document = PageDocument::blank($definition['label'], $locale);
        $document['sections'] = [self::section($definition['label'], [self::component($type, $variant)])];
        return $document;
    }

    /** Demo page containing every registry component and variant. */
    public static function demo(string $locale = 'en'): array
    {
        $document = PageDocument::blank('Component catalogue', $locale);
        $document['sections'] = [];
        foreach (ComponentRegistry::all() as $definition) {
            foreach ($definition['variants'] as $variant) {
                $document['sections'][] = self::section(
                    $definition['label'] . ' / ' . $variant['label'],
                    [self::component($definition['type'], $variant['id'])]
                );
            }
        }
        return $document;
    }

    /**
     * Starter page templates for the template library. Each one is a complete,
     * valid document built from registry components only.
     *
     * @return list<array{id:string,name:string,category:string,description:string,document:array<string,mixed>}>
     */
    public static function templates(): array
    {
        $build = static function (string $title, array $sections): array {
            $document = PageDocument::blank($title, 'en');
            $document['seo']['description'] = 'A starting point composed from registry components.';
            $document['sections'] = $sections;
            return $document;
        };

        return [
            [
                'id' => '44444444-4444-4444-8444-000000000001',
                'name' => 'Course launch',
                'category' => 'marketing',
                'description' => 'Open a cohort: hero, proof, curriculum, instructor and reviews.',
                'document' => $build('Course launch', [
                    self::section('Hero', [self::component('hero', 'split')]),
                    self::section('Proof', [self::component('stats', 'cards')]),
                    self::section('What you learn', [self::component('feature-grid', 'cards')]),
                    self::section('Curriculum', [self::component('curriculum', 'outline')]),
                    self::section('Your instructor', [self::component('instructor', 'card')]),
                    self::section('Reviews', [self::component('reviews', 'cards')]),
                    self::section('Next step', [self::component('cta', 'band')]),
                ]),
            ],
            [
                'id' => '44444444-4444-4444-8444-000000000002',
                'name' => 'Academy landing',
                'category' => 'marketing',
                'description' => 'A conversion-focused landing page with services and FAQ.',
                'document' => $build('Academy landing', [
                    self::section('Intro', [self::component('landing', 'proof')]),
                    self::section('Services', [self::component('services', 'cards')]),
                    self::section('Mission', [self::component('quote', 'statement')]),
                    self::section('Questions', [self::component('faq', 'columns')]),
                    self::section('Enrol', [self::component('cta', 'band')]),
                ]),
            ],
            [
                'id' => '44444444-4444-4444-8444-000000000003',
                'name' => 'About the academy',
                'category' => 'company',
                'description' => 'Story, team and numbers for an institutional about page.',
                'document' => $build('About the academy', [
                    self::section('Story', [self::component('hero', 'editorial')]),
                    self::section('Belief', [self::component('quote', 'statement')]),
                    self::section('People', [self::component('team', 'editorial')]),
                    self::section('Numbers', [self::component('stats', 'inline')]),
                    self::section('Join us', [self::component('cta', 'card')]),
                ]),
            ],
            [
                'id' => '44444444-4444-4444-8444-000000000004',
                'name' => 'Admissions',
                'category' => 'conversion',
                'description' => 'Pricing, requirements and a clear application path.',
                'document' => $build('Admissions', [
                    self::section('Intro', [self::component('hero', 'centered')]),
                    self::section('Plans', [self::component('pricing', 'tiers')]),
                    self::section('Requirements', [self::component('faq', 'divided')]),
                    self::section('Apply', [self::component('cta', 'band')]),
                ]),
            ],
            [
                'id' => '44444444-4444-4444-8444-000000000005',
                'name' => 'Workshop detail',
                'category' => 'event',
                'description' => 'A single workshop page with agenda, instructor and social proof.',
                'document' => $build('Workshop detail', [
                    self::section('Overview', [self::component('landing', 'event')]),
                    self::section('Agenda', [self::component('curriculum', 'modules')]),
                    self::section('Facilitator', [self::component('instructor', 'split')]),
                    self::section('Feedback', [self::component('reviews', 'wall')]),
                    self::section('Book', [self::component('cta', 'band')]),
                ]),
            ],
            [
                'id' => '44444444-4444-4444-8444-000000000006',
                'name' => 'Content hub',
                'category' => 'content',
                'description' => 'An editorial index page for guides and long-form resources.',
                'document' => $build('Content hub', [
                    self::section('Intro', [self::component('hero', 'editorial')]),
                    self::section('Highlights', [self::component('feature-grid', 'compact')]),
                    self::section('Common questions', [self::component('accordion', 'plain')]),
                    self::section('Keep reading', [self::component('cta', 'card')]),
                ]),
            ],
        ];
    }

    /** @return array<string,mixed> */
    public static function section(string $label, array $components): array
    {
        return [
            'id' => Uuid::v4(),
            'label' => $label,
            'layout' => ['width' => 'contained', 'columns' => 12, 'gap' => 24, 'paddingY' => 72, 'background' => 'none'],
            'visibility' => ['devices' => ['desktop', 'tablet', 'mobile']],
            'components' => $components,
        ];
    }

    /** @return array<string,mixed> */
    public static function component(string $type, string $variant): array
    {
        $definition = ComponentRegistry::get($type);
        if ($definition === null) {
            throw new \InvalidArgumentException('Unknown component type: ' . $type);
        }
        $props = $definition['defaults'];
        $extras = match ($type) {
            'gallery' => ['items' => [
                ['image' => ['id' => Uuid::v4(), 'alt' => 'Learners collaborating']],
                ['image' => ['id' => Uuid::v4(), 'alt' => 'A mentoring session']],
            ]],
            'logos' => ['items' => [['name' => 'Northwind'], ['name' => 'Aria Labs'], ['name' => 'Kaar']]],
            'course-list' => ['items' => [
                ['title' => 'Product leadership', 'summary' => 'Twelve weeks with practising mentors.', 'meta' => 'Cohort 07', 'href' => '/en/courses/product-leadership'],
                ['title' => 'Data foundations', 'summary' => 'Build the analytical base for decision-making.', 'meta' => 'Cohort 03', 'href' => '/en/courses/data-foundations'],
            ]],
            'media-text' => ['image' => ['id' => Uuid::v4(), 'alt' => 'A workshop in progress']],
            'hero' => ['image' => ['id' => Uuid::v4(), 'alt' => 'The academy studio']],
            'quote' => ['role' => 'Product manager, Cohort 05'],
            default => [],
        };
        $props = array_replace($props, $extras);
        $component = PageDocument::component($definition, $props);
        $component['variant'] = $variant;
        $component['animation'] = ['preset' => 'rise', 'duration' => 520, 'delay' => 0, 'easing' => 'calm'];
        return $component;
    }
}
