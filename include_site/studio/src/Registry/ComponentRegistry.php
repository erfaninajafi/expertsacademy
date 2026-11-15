<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Registry;

use ExpertsAcademy\Studio\Json;

/**
 * Code-owned component registry.
 *
 * A component type, its property schema, its variants and its template live in
 * this file only. Neither a request nor a database row can select a PHP file:
 * the renderer maps a validated type key to a fixed path in templates/.
 *
 * All visual implementations are new. No template from the legacy builder is
 * referenced by this registry.
 */
final class ComponentRegistry
{
    public const SCHEMA_VERSION = 1;

    /** @var array<string,array<string,mixed>>|null */
    private static ?array $cache = null;

    /** @return array<string,array<string,mixed>> */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $text = static fn (int $max, bool $required = false): array => ['type' => 'string', 'max' => $max, 'required' => $required];
        $rich = static fn (int $max, bool $required = false): array => ['type' => 'richtext', 'max' => $max, 'required' => $required];
        $media = static fn (bool $required = false): array => ['type' => 'media', 'required' => $required];
        $link = static fn (bool $required = false): array => [
            'type' => 'object', 'required' => $required,
            'fields' => ['label' => $text(60, true), 'href' => ['type' => 'url', 'required' => true]],
        ];

        $definitions = [
            'hero' => [
                'label' => 'Hero', 'category' => 'content', 'description' => 'Opening statement with optional media and calls to action.',
                'variants' => ['editorial' => 'Editorial', 'split' => 'Split', 'centered' => 'Centered'],
                'container' => true,
                'props' => [
                    'eyebrow' => $text(80),
                    'heading' => $text(200, true),
                    'lead' => ['type' => 'text', 'max' => 600],
                    'primaryCta' => $link(),
                    'secondaryCta' => $link(),
                    'image' => $media(),
                ],
                'defaults' => [
                    'eyebrow' => 'Experts Academy',
                    'heading' => 'A heading that states the promise',
                    'lead' => 'One or two sentences of supporting context.',
                    'primaryCta' => ['label' => 'Start learning', 'href' => '/'],
                ],
            ],
            'rich-text' => [
                'label' => 'Rich text', 'category' => 'content', 'description' => 'Long-form editorial copy with sanitized markup.',
                'variants' => ['prose' => 'Prose', 'two-column' => 'Two column'],
                'container' => true,
                'props' => ['heading' => $text(200), 'body' => $rich(12000, true)],
                'defaults' => ['heading' => '', 'body' => '<p>Write the section content here.</p>'],
            ],
            'feature-grid' => [
                'label' => 'Feature grid', 'category' => 'content', 'description' => 'A set of short value propositions.',
                'variants' => ['cards' => 'Cards', 'compact' => 'Compact'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'items' => ['type' => 'list', 'min' => 2, 'max' => 8, 'item' => [
                        'title' => $text(80, true),
                        'body' => ['type' => 'text', 'max' => 300],
                    ]],
                ],
                'defaults' => ['heading' => 'What makes this different', 'items' => [
                    ['title' => 'Mentor-led', 'body' => 'Learn with practitioners.'],
                    ['title' => 'Applied', 'body' => 'Ship real work, not exercises.'],
                ]],
            ],
            'stats' => [
                'label' => 'Statistics', 'category' => 'social-proof', 'description' => 'A short row of verified numbers.',
                'variants' => ['inline' => 'Inline', 'cards' => 'Cards'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'items' => ['type' => 'list', 'min' => 2, 'max' => 6, 'item' => [
                        'value' => $text(20, true), 'label' => $text(80, true),
                    ]],
                ],
                'defaults' => ['items' => [['value' => '120+', 'label' => 'Mentors'], ['value' => '4,800', 'label' => 'Learners']]],
            ],
            'media-text' => [
                'label' => 'Media and text', 'category' => 'media', 'description' => 'Image paired with editorial copy.',
                'variants' => ['image-start' => 'Image first', 'image-end' => 'Text first'],
                'container' => true,
                'props' => [
                    'heading' => $text(200, true),
                    'body' => $rich(8000),
                    'image' => $media(true),
                    'cta' => $link(),
                ],
                'defaults' => ['heading' => 'A story worth telling', 'body' => '<p>Describe the programme, cohort or outcome.</p>'],
            ],
            'quote' => [
                'label' => 'Quote', 'category' => 'social-proof', 'description' => 'A statement or participant testimonial.',
                'variants' => ['statement' => 'Statement', 'testimonial' => 'Testimonial'],
                'container' => true,
                'props' => [
                    'quote' => ['type' => 'text', 'max' => 1000, 'required' => true],
                    'attribution' => $text(120),
                    'role' => $text(120),
                    'avatar' => $media(),
                ],
                'defaults' => ['quote' => 'The programme changed how I approach my work.', 'attribution' => 'Participant name'],
            ],
            'accordion' => [
                'label' => 'Accordion', 'category' => 'content', 'description' => 'Progressively disclosed question and answer pairs.',
                'variants' => ['faq' => 'FAQ', 'plain' => 'Plain'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 12, 'item' => [
                        'question' => $text(200, true),
                        'answer' => $rich(4000, true),
                    ]],
                ],
                'defaults' => ['heading' => 'Frequently asked questions', 'items' => [
                    ['question' => 'Who is this for?', 'answer' => '<p>Practitioners who want structured growth.</p>'],
                ]],
            ],
            'cta' => [
                'label' => 'Call to action', 'category' => 'conversion', 'description' => 'A focused conversion band.',
                'variants' => ['band' => 'Band', 'card' => 'Card'],
                'container' => true,
                'props' => [
                    'heading' => $text(200, true),
                    'body' => ['type' => 'text', 'max' => 500],
                    'primaryCta' => $link(true),
                ],
                'defaults' => ['heading' => 'Ready to begin?', 'primaryCta' => ['label' => 'Apply now', 'href' => '/']],
            ],
            'gallery' => [
                'label' => 'Gallery', 'category' => 'media', 'description' => 'A captioned image set.',
                'variants' => ['grid' => 'Grid', 'strip' => 'Strip'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'items' => ['type' => 'list', 'min' => 2, 'max' => 12, 'item' => [
                        'image' => $media(true), 'caption' => $text(160),
                    ]],
                ],
                'defaults' => ['items' => []],
            ],
            'logos' => [
                'label' => 'Logo row', 'category' => 'social-proof', 'description' => 'Partner or employer marks.',
                'variants' => ['row' => 'Row'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'items' => ['type' => 'list', 'min' => 2, 'max' => 10, 'item' => [
                        'name' => $text(80, true), 'image' => $media(),
                    ]],
                ],
                'defaults' => ['heading' => 'Where our people work', 'items' => []],
            ],
            'course-list' => [
                'label' => 'Course list', 'category' => 'conversion', 'description' => 'Programme cards with editorial metadata.',
                'variants' => ['grid' => 'Grid', 'list' => 'List'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 12, 'item' => [
                        'title' => $text(120, true),
                        'summary' => ['type' => 'text', 'max' => 300],
                        'meta' => $text(60),
                        'href' => ['type' => 'url'],
                        'image' => $media(),
                    ]],
                ],
                'defaults' => ['heading' => 'Programmes', 'items' => []],
            ],
            'hero-cinematic' => [
                'label' => 'Cinematic hero', 'category' => 'hero',
                'description' => 'A high-impact opening with layered media, editorial type and a proof rail.',
                'variants' => ['overlay' => 'Overlay', 'split' => 'Split', 'minimal' => 'Minimal'],
                'container' => true,
                'props' => [
                    'eyebrow' => $text(80),
                    'heading' => $text(200, true),
                    'lead' => ['type' => 'text', 'max' => 600],
                    'badge' => $text(80),
                    'primaryCta' => $link(),
                    'secondaryCta' => $link(),
                    'image' => $media(),
                    'stats' => ['type' => 'list', 'min' => 0, 'max' => 4, 'item' => [
                        'value' => $text(20, true), 'label' => $text(60, true),
                    ]],
                ],
                'defaults' => [
                    'eyebrow' => 'Experts Academy',
                    'badge' => 'Cohort 08 · Applications open',
                    'heading' => 'Build capability that compounds',
                    'lead' => 'Mentor-led programmes for people who ship real work.',
                    'primaryCta' => ['label' => 'Explore programmes', 'href' => '/'],
                    'secondaryCta' => ['label' => 'Talk to an advisor', 'href' => '/contact'],
                    'stats' => [['value' => '96%', 'label' => 'Completion'], ['value' => '4.8/5', 'label' => 'Mentor rating']],
                ],
            ],
            'bento-grid' => [
                'label' => 'Bento grid', 'category' => 'bento',
                'description' => 'A modular mosaic that mixes narrative, proof and media tiles.',
                'variants' => ['mosaic' => 'Mosaic', 'editorial' => 'Editorial', 'metrics' => 'Metrics'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'items' => ['type' => 'list', 'min' => 2, 'max' => 6, 'item' => [
                        'title' => $text(80, true),
                        'body' => ['type' => 'text', 'max' => 240],
                        'tag' => $text(40),
                        'size' => ['type' => 'enum', 'values' => ['standard', 'wide', 'tall', 'hero']],
                        'href' => ['type' => 'url'],
                        'image' => $media(),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'A modular view of the academy',
                    'intro' => 'Programmes, proof and practice composed as one system.',
                    'items' => [
                        ['title' => 'Mentor-led cohorts', 'body' => 'Small groups with practitioners who review your work.', 'tag' => 'Programmes', 'size' => 'hero'],
                        ['title' => '96% completion', 'body' => 'Structured accountability beats passive video.', 'tag' => 'Outcomes', 'size' => 'standard'],
                        ['title' => 'Applied projects', 'body' => 'Ship a portfolio piece every module.', 'tag' => 'Practice', 'size' => 'wide'],
                        ['title' => 'Career studio', 'body' => 'Portfolio, narrative and interview practice.', 'tag' => 'Support', 'size' => 'standard'],
                    ],
                ],
            ],
            'pricing' => [
                'label' => 'Pricing', 'category' => 'comparison',
                'description' => 'Plan selection with a clear featured option and comparison table.',
                'variants' => ['tiers' => 'Tiers', 'comparison' => 'Comparison', 'spotlight' => 'Spotlight'],
                'container' => true,
                'props' => [
                    'heading' => $text(200, true),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'note' => $text(200),
                    'plans' => ['type' => 'list', 'min' => 1, 'max' => 4, 'item' => [
                        'name' => $text(60, true),
                        'price' => $text(20, true),
                        'period' => $text(30),
                        'summary' => ['type' => 'text', 'max' => 200],
                        'features' => ['type' => 'text', 'max' => 600],
                        'cta' => $link(),
                        'featured' => ['type' => 'enum', 'values' => ['yes', 'no']],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Choose the room that fits',
                    'intro' => 'Every plan includes mentor reviews and the project library.',
                    'note' => 'Prices in USD. Cohort start dates apply.',
                    'plans' => [
                        ['name' => 'Self-paced', 'price' => '$490', 'period' => 'one cohort', 'summary' => 'The full curriculum with async mentor feedback.', 'features' => "Project library\nMonthly group review\nCommunity access", 'featured' => 'no', 'cta' => ['label' => 'Start self-paced', 'href' => '/']],
                        ['name' => 'Mentor-led', 'price' => '$1,900', 'period' => '12 weeks', 'summary' => 'Small cohort, weekly live reviews and a capstone.', 'features' => "Weekly live reviews\n1:1 mentor sessions\nCapstone review\nCareer studio", 'featured' => 'yes', 'cta' => ['label' => 'Apply for the cohort', 'href' => '/']],
                        ['name' => 'Team', 'price' => 'Custom', 'period' => 'per team', 'summary' => 'Private cohort with tailored outcomes.', 'features' => "Private cohort\nTailored curriculum\nExecutive sponsor\nOutcome reporting", 'featured' => 'no', 'cta' => ['label' => 'Talk to us', 'href' => '/contact']],
                    ],
                ],
            ],
            'timeline' => [
                'label' => 'Timeline', 'category' => 'timeline',
                'description' => 'A sequenced journey with milestones, methods and proof.',
                'variants' => ['vertical' => 'Vertical', 'alternating' => 'Alternating', 'horizontal' => 'Horizontal'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'items' => ['type' => 'list', 'min' => 2, 'max' => 8, 'item' => [
                        'marker' => $text(20),
                        'title' => $text(100, true),
                        'body' => ['type' => 'text', 'max' => 300],
                        'meta' => $text(60),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'How the programme unfolds',
                    'intro' => 'Twelve weeks, built around applied work.',
                    'items' => [
                        ['marker' => '01', 'title' => 'Diagnose', 'body' => 'Map the capability gap and agree the outcomes.', 'meta' => 'Week 1'],
                        ['marker' => '02', 'title' => 'Practice', 'body' => 'Weekly applied sprints with mentor critique.', 'meta' => 'Weeks 2–8'],
                        ['marker' => '03', 'title' => 'Capstone', 'body' => 'Ship a real project and defend the decisions.', 'meta' => 'Weeks 9–11'],
                        ['marker' => '04', 'title' => 'Translate', 'body' => 'Turn the work into a portfolio and a plan.', 'meta' => 'Week 12'],
                    ],
                ],
            ],
            'navbar' => [
                'label' => 'Navigation bar', 'category' => 'navigation',
                'description' => 'Primary site navigation with brand, links and conversion actions.',
                'variants' => ['classic' => 'Classic', 'centered' => 'Centered', 'split' => 'Split', 'minimal' => 'Minimal'],
                'container' => true,
                'props' => [
                    'brand' => $text(80),
                    'tagline' => $text(120),
                    'logo' => $media(),
                    'links' => ['type' => 'list', 'min' => 1, 'max' => 7, 'item' => [
                        'label' => $text(60, true), 'href' => ['type' => 'url', 'required' => true],
                    ]],
                    'primaryCta' => $link(),
                    'secondaryCta' => $link(),
                ],
                'defaults' => [
                    'brand' => 'Experts Academy',
                    'tagline' => 'Mentor-led capability building',
                    'links' => [
                        ['label' => 'Programmes', 'href' => '/programmes'],
                        ['label' => 'Mentors', 'href' => '/mentors'],
                        ['label' => 'Case studies', 'href' => '/case-studies'],
                    ],
                    'primaryCta' => ['label' => 'Apply now', 'href' => '/apply'],
                ],
            ],
            'breadcrumbs' => [
                'label' => 'Breadcrumbs', 'category' => 'navigation',
                'description' => 'Orientation trail for deep pages and editorial articles.',
                'variants' => ['inline' => 'Inline', 'eyebrow' => 'Eyebrow'],
                'container' => true,
                'props' => [
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 6, 'item' => [
                        'label' => $text(60, true), 'href' => ['type' => 'url', 'required' => true],
                    ]],
                    'current' => $text(120, true),
                ],
                'defaults' => [
                    'items' => [
                        ['label' => 'Home', 'href' => '/'],
                        ['label' => 'Programmes', 'href' => '/programmes'],
                    ],
                    'current' => 'Applied leadership',
                ],
            ],
            'footer' => [
                'label' => 'Footer', 'category' => 'navigation',
                'description' => 'Closing navigation, contact context and legal line.',
                'variants' => ['columns' => 'Columns', 'compact' => 'Compact', 'editorial' => 'Editorial'],
                'container' => true,
                'props' => [
                    'brand' => $text(80),
                    'blurb' => ['type' => 'text', 'max' => 400],
                    'columns' => ['type' => 'list', 'min' => 1, 'max' => 4, 'item' => [
                        'title' => $text(60, true),
                        'links' => ['type' => 'text', 'max' => 600],
                    ]],
                    'social' => ['type' => 'list', 'min' => 0, 'max' => 5, 'item' => [
                        'label' => $text(40, true), 'href' => ['type' => 'url', 'required' => true],
                    ]],
                    'legal' => $text(200),
                ],
                'defaults' => [
                    'brand' => 'Experts Academy',
                    'blurb' => 'Programmes, mentors and applied practice for people who want their work to compound.',
                    'columns' => [
                        ['title' => 'Academy', 'links' => "Programmes|/programmes\nMentors|/mentors\nLearning pathways|/pathways"],
                        ['title' => 'Company', 'links' => "About|/about\nCase studies|/case-studies\nContact|/contact"],
                    ],
                    'social' => [
                        ['label' => 'LinkedIn', 'href' => 'https://linkedin.com'],
                        ['label' => 'Instagram', 'href' => 'https://instagram.com'],
                    ],
                    'legal' => '© Experts Academy. All rights reserved.',
                ],
            ],
            'tabs' => [
                'label' => 'Tabs', 'category' => 'content',
                'description' => 'Switch between programme, method and outcome narratives.',
                'variants' => ['underline' => 'Underline', 'pills' => 'Pills', 'vertical' => 'Vertical'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'items' => ['type' => 'list', 'min' => 2, 'max' => 6, 'item' => [
                        'label' => $text(40, true),
                        'title' => $text(120, true),
                        'body' => $rich(3000, true),
                        'cta' => $link(),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'One academy, three ways in',
                    'items' => [
                        ['label' => 'Cohorts', 'title' => 'Mentor-led cohorts', 'body' => '<p>Twelve weeks of applied sprints with weekly critique.</p>'],
                        ['label' => 'Self-paced', 'title' => 'Self-paced tracks', 'body' => '<p>Build the same portfolio on your own schedule.</p>'],
                        ['label' => 'Teams', 'title' => 'Private team rooms', 'body' => '<p>A tailored curriculum for your team’s real brief.</p>'],
                    ],
                ],
            ],
            'team' => [
                'label' => 'Team', 'category' => 'social-proof',
                'description' => 'Mentors and operators presented as people, not a logo wall.',
                'variants' => ['grid' => 'Grid', 'list' => 'List', 'spotlight' => 'Spotlight', 'editorial' => 'Editorial'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'members' => ['type' => 'list', 'min' => 1, 'max' => 8, 'item' => [
                        'name' => $text(80, true),
                        'role' => $text(100),
                        'bio' => ['type' => 'text', 'max' => 320],
                        'image' => $media(),
                        'href' => ['type' => 'url'],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Practitioners, not lecturers',
                    'intro' => 'Every mentor still does the work they teach.',
                    'members' => [
                        ['name' => 'Maya Rahimi', 'role' => 'Programme director', 'bio' => 'Former product lead; designs the applied curriculum.'],
                        ['name' => 'Daniel Okafor', 'role' => 'Mentor, strategy', 'bio' => 'Advises founders on positioning and growth.'],
                        ['name' => 'Sara Lindqvist', 'role' => 'Mentor, design', 'bio' => 'Design systems lead and portfolio reviewer.'],
                    ],
                ],
            ],
            'services' => [
                'label' => 'Services', 'category' => 'content',
                'description' => 'A clear index of what the academy offers and who it is for.',
                'variants' => ['cards' => 'Cards', 'numbered' => 'Numbered', 'list' => 'List'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 8, 'item' => [
                        'title' => $text(100, true),
                        'body' => ['type' => 'text', 'max' => 300],
                        'meta' => $text(80),
                        'href' => ['type' => 'url'],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Where we can help',
                    'items' => [
                        ['title' => 'Leadership programmes', 'body' => 'Cohorts for managers moving from delivery to direction.', 'meta' => '12 weeks'],
                        ['title' => 'Team capability', 'body' => 'Private rooms built around a live business brief.', 'meta' => 'Custom'],
                        ['title' => 'Career studio', 'body' => 'Portfolio, narrative and interview practice.', 'meta' => 'Ongoing'],
                    ],
                ],
            ],
            'case-studies' => [
                'label' => 'Case studies', 'category' => 'content',
                'description' => 'Proof of change with client context and measured outcomes.',
                'variants' => ['grid' => 'Grid', 'featured' => 'Featured', 'list' => 'List'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 6, 'item' => [
                        'client' => $text(80, true),
                        'title' => $text(120, true),
                        'summary' => ['type' => 'text', 'max' => 300],
                        'result' => $text(80),
                        'meta' => $text(60),
                        'image' => $media(),
                        'href' => ['type' => 'url'],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Evidence, not adjectives',
                    'items' => [
                        ['client' => 'Northline', 'title' => 'A product team that started shipping weekly', 'summary' => 'Six squads moved from quarterly releases to weekly increments.', 'result' => '8× faster delivery', 'meta' => 'Product'],
                        ['client' => 'Aster Health', 'title' => 'Managers who could hold a hard conversation', 'summary' => 'A cohort of 40 leads practised live feedback with actors.', 'result' => '92% confidence lift', 'meta' => 'Leadership'],
                    ],
                ],
            ],
            'pathways' => [
                'label' => 'Learning pathways', 'category' => 'timeline',
                'description' => 'A progression from foundation to advanced practice.',
                'variants' => ['steps' => 'Steps', 'tracks' => 'Tracks', 'roadmap' => 'Roadmap'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'items' => ['type' => 'list', 'min' => 2, 'max' => 6, 'item' => [
                        'title' => $text(100, true),
                        'body' => ['type' => 'text', 'max' => 300],
                        'meta' => $text(60),
                        'duration' => $text(40),
                        'level' => ['type' => 'enum', 'values' => ['foundation', 'practitioner', 'advanced']],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Choose your progression',
                    'items' => [
                        ['title' => 'Foundation', 'body' => 'Core concepts, one applied project, mentor feedback.', 'meta' => 'Start here', 'duration' => '4 weeks', 'level' => 'foundation'],
                        ['title' => 'Practitioner', 'body' => 'A full capstone with weekly critique and peer review.', 'meta' => 'Most popular', 'duration' => '12 weeks', 'level' => 'practitioner'],
                        ['title' => 'Advanced', 'body' => 'Lead a real brief and present to a senior panel.', 'meta' => 'By application', 'duration' => '8 weeks', 'level' => 'advanced'],
                    ],
                ],
            ],
            'contact-form' => [
                'label' => 'Contact form', 'category' => 'forms',
                'description' => 'A no-JavaScript enquiry form with clear contact context.',
                'variants' => ['simple' => 'Simple', 'split' => 'Split', 'card' => 'Card'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'email' => $text(120),
                    'phone' => $text(40),
                    'address' => ['type' => 'text', 'max' => 300],
                    'submitLabel' => $text(40),
                    'note' => $text(240),
                ],
                'defaults' => [
                    'heading' => 'Tell us what you are working on',
                    'intro' => 'A programme advisor replies within two working days.',
                    'email' => 'hello@expertsacademy.example',
                    'phone' => '+44 20 0000 0000',
                    'address' => '18 Clerkenwell Road, London',
                    'submitLabel' => 'Send enquiry',
                    'note' => 'We only use your details to answer this enquiry.',
                ],
            ],
            'faq' => [
                'label' => 'FAQ', 'category' => 'content',
                'description' => 'Common questions laid out for scanning, not hiding.',
                'variants' => ['columns' => 'Columns', 'divided' => 'Divided', 'cards' => 'Cards'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 12, 'item' => [
                        'question' => $text(200, true),
                        'answer' => $rich(3000, true),
                    ]],
                    'note' => $text(200),
                ],
                'defaults' => [
                    'heading' => 'Questions before you apply',
                    'items' => [
                        ['question' => 'Do I need to take time off work?', 'answer' => '<p>No. Live sessions run twice weekly and are recorded.</p>'],
                        ['question' => 'What does the capstone involve?', 'answer' => '<p>A real brief, weekly critique and a final panel review.</p>'],
                        ['question' => 'Can my employer sponsor me?', 'answer' => '<p>Yes. We can invoice your organisation directly.</p>'],
                    ],
                ],
            ],
            'video' => [
                'label' => 'Video feature', 'category' => 'media',
                'description' => 'A poster-led video entry point without embedding untrusted players.',
                'variants' => ['cinematic' => 'Cinematic', 'split' => 'Split'],
                'container' => true,
                'props' => [
                    'heading' => $text(200, true),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'poster' => $media(),
                    'duration' => $text(20),
                    'caption' => $text(160),
                    'cta' => $link(),
                ],
                'defaults' => [
                    'heading' => 'Inside the studio',
                    'intro' => 'A three-minute look at how a cohort actually works.',
                    'duration' => '3:12',
                    'caption' => 'Filmed during the spring cohort.',
                    'cta' => ['label' => 'Watch the film', 'href' => '/stories/studio'],
                ],
            ],
            'author' => [
                'label' => 'Author', 'category' => 'social-proof',
                'description' => 'Byline and credibility for editorial pages.',
                'variants' => ['inline' => 'Inline', 'card' => 'Card'],
                'container' => true,
                'props' => [
                    'name' => $text(80, true),
                    'role' => $text(100),
                    'bio' => ['type' => 'text', 'max' => 500],
                    'image' => $media(),
                    'href' => ['type' => 'url'],
                ],
                'defaults' => [
                    'name' => 'Maya Rahimi',
                    'role' => 'Programme director',
                    'bio' => 'Writes about applied learning, mentoring and the craft of making work visible.',
                ],
            ],
            'certificates' => [
                'label' => 'Certificates', 'category' => 'social-proof',
                'description' => 'Accreditation and credential marks with issuer context.',
                'variants' => ['grid' => 'Grid', 'list' => 'List', 'ribbon' => 'Ribbon'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 8, 'item' => [
                        'title' => $text(120, true),
                        'issuer' => $text(120),
                        'year' => $text(10),
                        'image' => $media(),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Accredited where it matters',
                    'items' => [
                        ['title' => 'Certified Practitioner', 'issuer' => 'Institute of Applied Learning', 'year' => '2025'],
                        ['title' => 'Mentoring Standard', 'issuer' => 'European Mentoring Council', 'year' => '2024'],
                        ['title' => 'Assessment Quality', 'issuer' => 'Open Awards', 'year' => '2025'],
                    ],
                ],
            ],
            'reviews' => [
                'label' => 'Reviews', 'category' => 'social-proof',
                'description' => 'Ratings and participant voices with aggregate context.',
                'variants' => ['summary' => 'Summary', 'cards' => 'Cards', 'wall' => 'Wall'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'average' => $text(10),
                    'count' => $text(20),
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 9, 'item' => [
                        'quote' => ['type' => 'text', 'max' => 400, 'required' => true],
                        'name' => $text(80),
                        'role' => $text(100),
                        'rating' => ['type' => 'enum', 'values' => ['5', '4', '3', '2', '1']],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'What participants say',
                    'average' => '4.8',
                    'count' => '216 reviews',
                    'items' => [
                        ['quote' => 'The critique was direct and useful from week one.', 'name' => 'Lena Fischer', 'role' => 'Product lead', 'rating' => '5'],
                        ['quote' => 'I finally have a portfolio that explains my decisions.', 'name' => 'Omar Haddad', 'role' => 'Designer', 'rating' => '5'],
                        ['quote' => 'Structured, demanding and still human.', 'name' => 'Priya Nair', 'role' => 'Founder', 'rating' => '5'],
                    ],
                ],
            ],
            'spec-table' => [
                'label' => 'Specification table', 'category' => 'comparison',
                'description' => 'Dense factual detail presented for scanning.',
                'variants' => ['table' => 'Table', 'rows' => 'Rows'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 14, 'item' => [
                        'label' => $text(80, true),
                        'value' => $text(160, true),
                        'note' => ['type' => 'text', 'max' => 240],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Programme at a glance',
                    'items' => [
                        ['label' => 'Format', 'value' => 'Live online cohort', 'note' => 'Two sessions each week, recorded.'],
                        ['label' => 'Duration', 'value' => '12 weeks', 'note' => 'Plus a two-week capstone review window.'],
                        ['label' => 'Cohort size', 'value' => '18 participants', 'note' => 'Grouped into mentor pods of six.'],
                        ['label' => 'Commitment', 'value' => '6 hours per week', 'note' => 'Live sessions plus applied work.'],
                    ],
                ],
            ],
            'announcement' => [
                'label' => 'Announcement', 'category' => 'conversion',
                'description' => 'A single timely message with one action.',
                'variants' => ['bar' => 'Bar', 'stacked' => 'Stacked'],
                'container' => true,
                'props' => [
                    'eyebrow' => $text(40),
                    'message' => $text(200, true),
                    'detail' => ['type' => 'text', 'max' => 300],
                    'cta' => $link(),
                ],
                'defaults' => [
                    'eyebrow' => 'Cohort 08',
                    'message' => 'Applications close on 30 November.',
                    'detail' => 'Eighteen places, mentor-led, starting in January.',
                    'cta' => ['label' => 'Apply now', 'href' => '/apply'],
                ],
            ],
            'newsletter' => [
                'label' => 'Newsletter', 'category' => 'conversion',
                'description' => 'An email capture block with a clear promise.',
                'variants' => ['band' => 'Band', 'split' => 'Split', 'card' => 'Card'],
                'container' => true,
                'props' => [
                    'heading' => $text(200, true),
                    'body' => ['type' => 'text', 'max' => 300],
                    'placeholder' => $text(60),
                    'submitLabel' => $text(40),
                    'note' => $text(200),
                ],
                'defaults' => [
                    'heading' => 'One useful letter a month',
                    'body' => 'Programme notes, mentor essays and open cohort dates.',
                    'placeholder' => 'you@example.com',
                    'submitLabel' => 'Subscribe',
                    'note' => 'No more than one email a month. Unsubscribe anytime.',
                ],
            ],
            'checklist' => [
                'label' => 'Checklist', 'category' => 'content',
                'description' => 'What is included, stated plainly.',
                'variants' => ['inline' => 'Inline', 'cards' => 'Cards'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 12, 'item' => [
                        'text' => $text(160, true),
                        'detail' => ['type' => 'text', 'max' => 240],
                        'done' => ['type' => 'enum', 'values' => ['yes', 'no']],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'What every place includes',
                    'items' => [
                        ['text' => 'Weekly live critique', 'detail' => 'Small pods with a practising mentor.', 'done' => 'yes'],
                        ['text' => 'Portfolio review', 'detail' => 'Two structured reviews per cohort.', 'done' => 'yes'],
                        ['text' => 'Career studio', 'detail' => 'Narrative, portfolio and interview practice.', 'done' => 'yes'],
                        ['text' => 'Private recording archive', 'detail' => 'Sessions stay available for twelve months.', 'done' => 'yes'],
                    ],
                ],
            ],
            'related-content' => [
                'label' => 'Related content', 'category' => 'content',
                'description' => 'Onward journeys from an article or programme page.',
                'variants' => ['cards' => 'Cards', 'list' => 'List', 'compact' => 'Compact'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 6, 'item' => [
                        'title' => $text(120, true),
                        'summary' => ['type' => 'text', 'max' => 240],
                        'meta' => $text(60),
                        'href' => ['type' => 'url', 'required' => true],
                        'image' => $media(),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Keep reading',
                    'items' => [
                        ['title' => 'How cohorts actually run', 'summary' => 'A week-by-week view of the programme.', 'meta' => 'Guide', 'href' => '/guides/cohorts'],
                        ['title' => 'What mentors look for', 'summary' => 'The signals that matter in a portfolio review.', 'meta' => 'Essay', 'href' => '/essays/mentors'],
                        ['title' => 'Building a useful case study', 'summary' => 'Turn a project into evidence of judgement.', 'meta' => 'Template', 'href' => '/resources/case-study'],
                    ],
                ],
            ],
            'location' => [
                'label' => 'Location', 'category' => 'content',
                'description' => 'Studio address, hours and practical arrival notes.',
                'variants' => ['panel' => 'Panel', 'split' => 'Split'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'address' => ['type' => 'text', 'max' => 300],
                    'hours' => ['type' => 'text', 'max' => 300],
                    'email' => $text(120),
                    'phone' => $text(40),
                    'note' => ['type' => 'text', 'max' => 240],
                ],
                'defaults' => [
                    'heading' => 'Visit the studio',
                    'address' => '18 Clerkenwell Road\nLondon EC1M 5PQ',
                    'hours' => 'Monday–Thursday, 09:00–18:00\nFriday, 09:00–13:00',
                    'email' => 'studio@expertsacademy.example',
                    'phone' => '+44 20 0000 0000',
                    'note' => 'Please book a visit before travelling; the studio is not a walk-in space.',
                ],
            ],
            'curriculum' => [
                'label' => 'Curriculum', 'category' => 'content',
                'description' => 'Module-by-module syllabus with scope and time commitment.',
                'variants' => ['outline' => 'Outline', 'modules' => 'Modules', 'table' => 'Table'],
                'container' => true,
                'props' => [
                    'heading' => $text(200, true),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'modules' => ['type' => 'list', 'min' => 1, 'max' => 12, 'item' => [
                        'title' => $text(120, true),
                        'summary' => ['type' => 'text', 'max' => 300],
                        'topics' => ['type' => 'text', 'max' => 800],
                        'duration' => $text(40),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'What the twelve weeks cover',
                    'intro' => 'Each module ends with a reviewed piece of applied work.',
                    'modules' => [
                        ['title' => 'Diagnose the work', 'summary' => 'Frame the problem and the evidence that matters.', 'topics' => "Stakeholder mapping\nProblem statements\nSuccess measures", 'duration' => 'Weeks 1–2'],
                        ['title' => 'Make the argument', 'summary' => 'Turn research into a decision others can follow.', 'topics' => "Narrative structure\nEvidence selection\nDecision memos", 'duration' => 'Weeks 3–5'],
                        ['title' => 'Ship and review', 'summary' => 'Deliver a real increment and defend the trade-offs.', 'topics' => "Scope control\nCritique protocol\nRetrospectives", 'duration' => 'Weeks 6–9'],
                        ['title' => 'Translate the outcome', 'summary' => 'Convert the work into portfolio and career evidence.', 'topics' => "Case study writing\nPortfolio review\nInterview practice", 'duration' => 'Weeks 10–12'],
                    ],
                ],
            ],
            'schedule' => [
                'label' => 'Schedule', 'category' => 'timeline',
                'description' => 'Session timetable with speakers and tracks.',
                'variants' => ['agenda' => 'Agenda', 'days' => 'Days', 'table' => 'Table'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 16, 'item' => [
                        'day' => $text(40, true),
                        'time' => $text(40),
                        'title' => $text(120, true),
                        'speaker' => $text(100),
                        'track' => $text(40),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Cohort timetable',
                    'intro' => 'Live sessions run Tuesdays and Thursdays, 18:00–20:00 GMT.',
                    'items' => [
                        ['day' => 'Week 1 · Tue', 'time' => '18:00', 'title' => 'Opening studio', 'speaker' => 'Maya Rahimi', 'track' => 'Core'],
                        ['day' => 'Week 1 · Thu', 'time' => '18:00', 'title' => 'Diagnosis workshop', 'speaker' => 'Daniel Okafor', 'track' => 'Practice'],
                        ['day' => 'Week 2 · Tue', 'time' => '18:00', 'title' => 'Evidence review', 'speaker' => 'Sara Lindqvist', 'track' => 'Critique'],
                    ],
                ],
            ],
            'application-form' => [
                'label' => 'Application form', 'category' => 'forms',
                'description' => 'A structured programme application with goals and background.',
                'variants' => ['simple' => 'Simple', 'split' => 'Split', 'steps' => 'Steps'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'deadline' => $text(80),
                    'submitLabel' => $text(40),
                    'note' => $text(240),
                ],
                'defaults' => [
                    'heading' => 'Apply for cohort 08',
                    'intro' => 'Tell us where you are and what you want to change.',
                    'deadline' => 'Applications close 30 November',
                    'submitLabel' => 'Submit application',
                    'note' => 'We reply to every application within five working days.',
                ],
            ],
            'download-gate' => [
                'label' => 'Download', 'category' => 'conversion',
                'description' => 'A brochure or guide offered in exchange for an email.',
                'variants' => ['inline' => 'Inline', 'card' => 'Card'],
                'container' => true,
                'props' => [
                    'heading' => $text(200, true),
                    'body' => ['type' => 'text', 'max' => 300],
                    'fileLabel' => $text(80),
                    'fileHref' => ['type' => 'url'],
                    'submitLabel' => $text(40),
                    'note' => $text(200),
                ],
                'defaults' => [
                    'heading' => 'The programme guide',
                    'body' => 'Outcomes, timetable and the full syllabus in one PDF.',
                    'fileLabel' => 'PDF · 1.8 MB',
                    'fileHref' => '/downloads/programme-guide.pdf',
                    'submitLabel' => 'Send the guide',
                    'note' => 'One email with the download. No newsletter unless you ask.',
                ],
            ],
            'values' => [
                'label' => 'Values', 'category' => 'content',
                'description' => 'The principles that shape how the academy works.',
                'variants' => ['grid' => 'Grid', 'list' => 'List', 'manifesto' => 'Manifesto'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 400],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 6, 'item' => [
                        'title' => $text(80, true),
                        'body' => ['type' => 'text', 'max' => 300],
                        'marker' => $text(20),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'What we hold to',
                    'items' => [
                        ['title' => 'Practice over performance', 'body' => 'Work is judged by what it changes, not how it presents.', 'marker' => '01'],
                        ['title' => 'Direct, generous critique', 'body' => 'Feedback is specific, timely and always about the work.', 'marker' => '02'],
                        ['title' => 'Evidence over adjectives', 'body' => 'Claims need proof; proof needs a clear method.', 'marker' => '03'],
                    ],
                ],
            ],
            'process' => [
                'label' => 'Process', 'category' => 'timeline',
                'description' => 'How an engagement or cohort progresses, step by step.',
                'variants' => ['steps' => 'Steps', 'numbered' => 'Numbered', 'cards' => 'Cards'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 2, 'max' => 8, 'item' => [
                        'title' => $text(100, true),
                        'body' => ['type' => 'text', 'max' => 280],
                        'duration' => $text(40),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'From application to outcome',
                    'items' => [
                        ['title' => 'Conversation', 'body' => 'A short call to test fit and clarify the outcome.', 'duration' => '30 min'],
                        ['title' => 'Diagnosis', 'body' => 'We map the gap and agree what success looks like.', 'duration' => 'Week 1'],
                        ['title' => 'Applied work', 'body' => 'Weekly sprints with critique and a visible artefact.', 'duration' => 'Weeks 2–10'],
                        ['title' => 'Review', 'body' => 'A panel reviews the capstone and the evidence behind it.', 'duration' => 'Week 12'],
                    ],
                ],
            ],
            'jobs' => [
                'label' => 'Open roles', 'category' => 'content',
                'description' => 'Current openings with location, type and a direct link.',
                'variants' => ['list' => 'List', 'cards' => 'Cards', 'table' => 'Table'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 10, 'item' => [
                        'title' => $text(120, true),
                        'location' => $text(80),
                        'type' => $text(40),
                        'summary' => ['type' => 'text', 'max' => 240],
                        'href' => ['type' => 'url'],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Work with us',
                    'items' => [
                        ['title' => 'Programme producer', 'location' => 'London / hybrid', 'type' => 'Full time', 'summary' => 'Run cohorts end to end, from schedule to mentor care.', 'href' => '/jobs/producer'],
                        ['title' => 'Mentor, product', 'location' => 'Remote', 'type' => 'Contract', 'summary' => 'Review applied work and lead fortnightly critique.', 'href' => '/jobs/mentor-product'],
                    ],
                ],
            ],
            'awards' => [
                'label' => 'Awards', 'category' => 'social-proof',
                'description' => 'Recognition and shortlists with issuer context.',
                'variants' => ['grid' => 'Grid', 'list' => 'List'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 8, 'item' => [
                        'title' => $text(120, true),
                        'issuer' => $text(80),
                        'year' => $text(10),
                        'note' => ['type' => 'text', 'max' => 200],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Recognition',
                    'items' => [
                        ['title' => 'Learning Programme of the Year', 'issuer' => 'Applied Learning Awards', 'year' => '2025', 'note' => 'Shortlisted for cohort design.'],
                        ['title' => 'Mentoring Excellence', 'issuer' => 'European Mentoring Council', 'year' => '2024', 'note' => 'Awarded to the mentor faculty.'],
                    ],
                ],
            ],
            'resources' => [
                'label' => 'Resources', 'category' => 'content',
                'description' => 'A curated library of guides, templates and essays.',
                'variants' => ['cards' => 'Cards', 'list' => 'List', 'rows' => 'Rows'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 8, 'item' => [
                        'title' => $text(120, true),
                        'kind' => $text(40),
                        'summary' => ['type' => 'text', 'max' => 240],
                        'href' => ['type' => 'url', 'required' => true],
                        'image' => $media(),
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Tools worth keeping',
                    'items' => [
                        ['title' => 'Cohort week planner', 'kind' => 'Template', 'summary' => 'A one-page plan for a twelve-week cohort.', 'href' => '/resources/week-planner'],
                        ['title' => 'Portfolio review rubric', 'kind' => 'Guide', 'summary' => 'The criteria mentors use in a review.', 'href' => '/resources/review-rubric'],
                        ['title' => 'Writing a useful case study', 'kind' => 'Essay', 'summary' => 'Turn a project into evidence of judgement.', 'href' => '/resources/case-study'],
                    ],
                ],
            ],
            'glossary' => [
                'label' => 'Glossary', 'category' => 'content',
                'description' => 'Shared vocabulary for programmes and practice.',
                'variants' => ['list' => 'List', 'columns' => 'Columns'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 16, 'item' => [
                        'term' => $text(80, true),
                        'definition' => ['type' => 'text', 'max' => 400, 'required' => true],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Working vocabulary',
                    'items' => [
                        ['term' => 'Capstone', 'definition' => 'The final applied project defended in front of a panel.'],
                        ['term' => 'Critique', 'definition' => 'A structured review of work against agreed criteria.'],
                        ['term' => 'Pod', 'definition' => 'A group of six participants with one mentor.'],
                    ],
                ],
            ],
            'comparison-matrix' => [
                'label' => 'Comparison matrix', 'category' => 'comparison',
                'description' => 'Options compared across the criteria that decide the choice.',
                'variants' => ['table' => 'Table', 'cards' => 'Cards'],
                'container' => true,
                'props' => [
                    'heading' => $text(200, true),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'options' => ['type' => 'list', 'min' => 1, 'max' => 4, 'item' => [
                        'name' => $text(60, true),
                        'summary' => ['type' => 'text', 'max' => 200],
                        'highlighted' => ['type' => 'enum', 'values' => ['yes', 'no']],
                    ]],
                    'features' => ['type' => 'list', 'min' => 1, 'max' => 12, 'item' => [
                        'feature' => $text(80, true),
                        'values' => ['type' => 'text', 'max' => 200, 'required' => true],
                    ]],
                    'note' => $text(200),
                ],
                'defaults' => [
                    'heading' => 'Compare the formats',
                    'options' => [
                        ['name' => 'Cohort', 'summary' => 'Live, fixed pace', 'highlighted' => 'yes'],
                        ['name' => 'Self-paced', 'summary' => 'Flexible, async', 'highlighted' => 'no'],
                        ['name' => 'Team room', 'summary' => 'Private group', 'highlighted' => 'no'],
                    ],
                    'features' => [
                        ['feature' => 'Live critique', 'values' => 'Weekly|Monthly|Weekly'],
                        ['feature' => 'Mentor 1:1', 'values' => 'Yes|No|Yes'],
                        ['feature' => 'Capstone review', 'values' => 'Yes|Optional|Yes'],
                        ['feature' => 'Career studio', 'values' => 'Yes|Self-serve|Yes'],
                    ],
                    'note' => 'All formats include the project library and recording archive.',
                ],
            ],
            'campaign' => [
                'label' => 'Campaign banner', 'category' => 'conversion',
                'description' => 'A time-bound campaign message with proof and one action.',
                'variants' => ['spotlight' => 'Spotlight', 'split' => 'Split', 'banner' => 'Banner'],
                'container' => true,
                'props' => [
                    'eyebrow' => $text(60),
                    'heading' => $text(200, true),
                    'lead' => ['type' => 'text', 'max' => 400],
                    'proof' => $text(120),
                    'image' => $media(),
                    'cta' => $link(true),
                ],
                'defaults' => [
                    'eyebrow' => 'Cohort 08 · January',
                    'heading' => 'Eighteen places. One honest critique culture.',
                    'lead' => 'Applications close at the end of the month.',
                    'proof' => '4.8/5 from 216 participant reviews',
                    'cta' => ['label' => 'Start an application', 'href' => '/apply'],
                ],
            ],
            'landing' => [
                'label' => 'Landing block', 'category' => 'hero',
                'description' => 'A complete opening block for campaigns and programme pages.',
                'variants' => ['product' => 'Product', 'event' => 'Event', 'waitlist' => 'Waitlist', 'split-form' => 'Split form', 'minimal' => 'Minimal', 'overlay' => 'Overlay', 'form-first' => 'Form first', 'proof' => 'Proof'],
                'container' => true,
                'props' => [
                    'eyebrow' => $text(80),
                    'heading' => $text(200, true),
                    'lead' => ['type' => 'text', 'max' => 500],
                    'primaryCta' => $link(),
                    'secondaryCta' => $link(),
                    'image' => $media(),
                    'bullets' => ['type' => 'list', 'min' => 0, 'max' => 4, 'item' => ['text' => $text(120, true)]],
                ],
                'defaults' => [
                    'eyebrow' => 'Cohort 08',
                    'heading' => 'A programme built around your actual work',
                    'lead' => 'Twelve weeks, one capstone, weekly critique from practitioners.',
                    'primaryCta' => ['label' => 'Apply now', 'href' => '/apply'],
                    'secondaryCta' => ['label' => 'See the syllabus', 'href' => '/programmes'],
                    'bullets' => [['text' => '18 places per cohort'], ['text' => 'Weekly live critique'], ['text' => 'Portfolio-ready capstone']],
                ],
            ],
            'feature-showcase' => [
                'label' => 'Feature showcase', 'category' => 'content',
                'description' => 'Deep feature stories with media, proof and onward links.',
                'variants' => ['alternating' => 'Alternating', 'sticky' => 'Sticky media', 'mosaic' => 'Mosaic', 'numbered' => 'Numbered', 'compact' => 'Compact', 'list' => 'Text list', 'cards' => 'Cards', 'split' => 'Two column'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 6, 'item' => [
                        'title' => $text(100, true),
                        'body' => ['type' => 'text', 'max' => 300],
                        'meta' => $text(60),
                        'image' => $media(),
                        'href' => ['type' => 'url'],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'How the work actually happens',
                    'items' => [
                        ['title' => 'Diagnose with evidence', 'body' => 'Map the problem before proposing a solution.', 'meta' => 'Method'],
                        ['title' => 'Build in public', 'body' => 'Weekly artefacts reviewed by mentors and peers.', 'meta' => 'Practice'],
                        ['title' => 'Defend the decision', 'body' => 'A panel reviews the capstone and its trade-offs.', 'meta' => 'Outcome'],
                    ],
                ],
            ],
            'instructor' => [
                'label' => 'Instructor profile', 'category' => 'social-proof',
                'description' => 'A single mentor or faculty profile with credentials.',
                'variants' => ['card' => 'Card', 'split' => 'Split', 'credentials' => 'Credentials', 'interview' => 'Interview', 'compact' => 'Compact', 'quote-card' => 'Quote card'],
                'container' => true,
                'props' => [
                    'name' => $text(80, true),
                    'role' => $text(100),
                    'bio' => $rich(3000),
                    'image' => $media(),
                    'credentials' => ['type' => 'list', 'min' => 0, 'max' => 6, 'item' => ['label' => $text(120, true)]],
                    'quote' => ['type' => 'text', 'max' => 400],
                    'cta' => $link(),
                ],
                'defaults' => [
                    'name' => 'Sara Lindqvist',
                    'role' => 'Faculty lead, design',
                    'bio' => '<p>Sara has led design systems for regulated products and now reviews portfolio work for the cohort.</p>',
                    'credentials' => [['label' => 'Design systems lead, 12 years'], ['label' => 'Guest lecturer, Central Saint Martins'], ['label' => 'Mentor since 2021']],
                    'quote' => 'Good work is an argument, not a decoration.',
                ],
            ],
            'booking-form' => [
                'label' => 'Booking form', 'category' => 'forms',
                'description' => 'Book an advising call with visible time slots.',
                'variants' => ['simple' => 'Simple', 'split' => 'Split', 'slots' => 'Slots', 'card' => 'Card', 'inline' => 'Inline', 'minimal' => 'Minimal'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'slots' => ['type' => 'list', 'min' => 0, 'max' => 8, 'item' => [
                        'label' => $text(60, true), 'detail' => $text(80),
                    ]],
                    'submitLabel' => $text(40),
                    'note' => $text(200),
                ],
                'defaults' => [
                    'heading' => 'Book a 20-minute advising call',
                    'intro' => 'Choose a time and tell us what you want to solve.',
                    'slots' => [
                        ['label' => 'Tue 10:00', 'detail' => '20 min · Maya'],
                        ['label' => 'Wed 14:30', 'detail' => '20 min · Daniel'],
                        ['label' => 'Thu 09:00', 'detail' => '20 min · Sara'],
                    ],
                    'submitLabel' => 'Request this time',
                    'note' => 'We confirm by email within one working day.',
                ],
            ],
            'team-request-form' => [
                'label' => 'Team request form', 'category' => 'forms',
                'description' => 'Enquiry form for private cohorts and team programmes.',
                'variants' => ['simple' => 'Simple', 'split' => 'Split', 'steps' => 'Steps', 'inline' => 'Inline', 'card' => 'Card', 'minimal' => 'Minimal'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'submitLabel' => $text(40),
                    'note' => $text(200),
                ],
                'defaults' => [
                    'heading' => 'Build a programme for your team',
                    'intro' => 'Tell us the brief, the team size and the outcome you need.',
                    'submitLabel' => 'Request a proposal',
                    'note' => 'We reply within two working days with a scoping call.',
                ],
            ],
            'mega-menu' => [
                'label' => 'Mega menu', 'category' => 'navigation',
                'description' => 'A structured navigation panel with grouped links and a feature.',
                'variants' => ['columns' => 'Columns', 'categories' => 'Categories', 'featured' => 'Featured', 'compact' => 'Compact', 'list' => 'List', 'drawer' => 'Drawer'],
                'container' => true,
                'props' => [
                    'heading' => $text(80),
                    'columns' => ['type' => 'list', 'min' => 1, 'max' => 4, 'item' => [
                        'title' => $text(60, true),
                        'links' => ['type' => 'text', 'max' => 600],
                    ]],
                    'featureTitle' => $text(120),
                    'featureBody' => ['type' => 'text', 'max' => 240],
                    'featureCta' => $link(),
                ],
                'defaults' => [
                    'heading' => 'Explore the academy',
                    'columns' => [
                        ['title' => 'Programmes', 'links' => "Cohorts|/programmes/cohorts\nSelf-paced|/programmes/self-paced\nTeam rooms|/programmes/teams"],
                        ['title' => 'Practice', 'links' => "Case studies|/case-studies\nMentors|/mentors\nResources|/resources"],
                    ],
                    'featureTitle' => 'Cohort 08 is open',
                    'featureBody' => 'Eighteen places, starting in January.',
                    'featureCta' => ['label' => 'See the cohort', 'href' => '/programmes/cohorts'],
                ],
            ],
            'press' => [
                'label' => 'Press mentions', 'category' => 'social-proof',
                'description' => 'External coverage with publication and date.',
                'variants' => ['grid' => 'Grid', 'list' => 'List', 'featured' => 'Featured', 'table' => 'Table', 'quotes' => 'Quotes', 'compact' => 'Compact'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 10, 'item' => [
                        'outlet' => $text(80, true),
                        'title' => $text(160, true),
                        'date' => $text(40),
                        'quote' => ['type' => 'text', 'max' => 240],
                        'href' => ['type' => 'url'],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'In the press',
                    'items' => [
                        ['outlet' => 'Financial Times', 'title' => 'The cohort model comes to mid-career learning', 'date' => 'May 2025', 'quote' => 'A rare programme that treats critique as the product.'] ,
                        ['outlet' => 'Monocle', 'title' => 'Inside a mentoring studio', 'date' => 'March 2025'],
                    ],
                ],
            ],
            'partners' => [
                'label' => 'Partners', 'category' => 'social-proof',
                'description' => 'Partner organisations with role and relationship context.',
                'variants' => ['grid' => 'Grid', 'tiers' => 'Tiers', 'strip' => 'Strip', 'list' => 'List', 'mosaic' => 'Mosaic', 'badges' => 'Badges'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 10, 'item' => [
                        'name' => $text(80, true),
                        'description' => ['type' => 'text', 'max' => 200],
                        'tier' => ['type' => 'enum', 'values' => ['strategic', 'programme', 'community']],
                        'image' => $media(),
                        'href' => ['type' => 'url'],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Who we build with',
                    'items' => [
                        ['name' => 'Northline', 'description' => 'Product teams and applied briefs.', 'tier' => 'strategic'],
                        ['name' => 'Aster Health', 'description' => 'Leadership cohorts for clinical managers.', 'tier' => 'programme'],
                        ['name' => 'Open Awards', 'description' => 'Assessment and accreditation.', 'tier' => 'community'],
                    ],
                ],
            ],
            'content-index' => [
                'label' => 'Content index', 'category' => 'content',
                'description' => 'An A–Z or category index for essays, guides and resources.',
                'variants' => ['az' => 'A–Z', 'categories' => 'Categories', 'list' => 'List', 'cards' => 'Cards', 'compact' => 'Compact', 'letters' => 'Letter bar'],
                'container' => true,
                'props' => [
                    'heading' => $text(200),
                    'intro' => ['type' => 'text', 'max' => 300],
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 20, 'item' => [
                        'title' => $text(120, true),
                        'letter' => $text(2),
                        'category' => $text(60),
                        'summary' => ['type' => 'text', 'max' => 200],
                        'href' => ['type' => 'url', 'required' => true],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'All resources',
                    'items' => [
                        ['title' => 'Applied critique', 'letter' => 'A', 'category' => 'Practice', 'summary' => 'How to run a useful review.', 'href' => '/resources/critique'],
                        ['title' => 'Capstone brief', 'letter' => 'C', 'category' => 'Assessment', 'summary' => 'The shape of a defensible final project.', 'href' => '/resources/capstone'],
                        ['title' => 'Mentor rubric', 'letter' => 'M', 'category' => 'Practice', 'summary' => 'What mentors look for in work.', 'href' => '/resources/rubric'],
                    ],
                ],
            ],
            'section-header' => [
                'label' => 'Section header', 'category' => 'layout',
                'description' => 'A typographic header that introduces the section below.',
                'variants' => ['eyebrow' => 'Eyebrow', 'centered' => 'Centered', 'split' => 'Split', 'rule' => 'Rule', 'numbered' => 'Numbered', 'label' => 'Label', 'actions' => 'Actions'],
                'container' => true,
                'props' => [
                    'eyebrow' => $text(80),
                    'heading' => $text(200, true),
                    'lead' => ['type' => 'text', 'max' => 400],
                    'cta' => $link(),
                    'meta' => $text(60),
                ],
                'defaults' => [
                    'eyebrow' => 'The method',
                    'heading' => 'Structure that makes progress visible',
                    'lead' => 'Every week produces an artefact, a critique and a decision.',
                    'meta' => '04 principles',
                ],
            ],
            'social-links' => [
                'label' => 'Social links', 'category' => 'navigation',
                'description' => 'A compact set of external profiles and handles.',
                'variants' => ['row' => 'Row', 'cards' => 'Cards', 'buttons' => 'Buttons', 'grid' => 'Grid', 'inline' => 'Inline', 'stacked' => 'Stacked'],
                'container' => true,
                'props' => [
                    'heading' => $text(120),
                    'items' => ['type' => 'list', 'min' => 1, 'max' => 8, 'item' => [
                        'label' => $text(40, true),
                        'handle' => $text(60),
                        'href' => ['type' => 'url', 'required' => true],
                    ]],
                ],
                'defaults' => [
                    'heading' => 'Follow the studio',
                    'items' => [
                        ['label' => 'LinkedIn', 'handle' => '@expertsacademy', 'href' => 'https://linkedin.com'],
                        ['label' => 'Instagram', 'handle' => '@experts.academy', 'href' => 'https://instagram.com'],
                        ['label' => 'YouTube', 'handle' => '@expertsacademy', 'href' => 'https://youtube.com'],
                    ],
                ],
            ],
            'cookie-consent' => [
                'label' => 'Cookie notice', 'category' => 'layout',
                'description' => 'A consent notice with accept, settings and policy links.',
                'variants' => ['bar' => 'Bar', 'card' => 'Card', 'corner' => 'Corner', 'inline' => 'Inline', 'banner' => 'Banner', 'compact' => 'Compact'],
                'container' => true,
                'props' => [
                    'message' => ['type' => 'text', 'max' => 400, 'required' => true],
                    'acceptLabel' => $text(40),
                    'settingsLabel' => $text(40),
                    'policyHref' => ['type' => 'url'],
                ],
                'defaults' => [
                    'message' => 'We use essential cookies to run this site and optional analytics to improve it.',
                    'acceptLabel' => 'Accept analytics',
                    'settingsLabel' => 'Settings',
                    'policyHref' => '/privacy',
                ],
            ],
            'divider' => [
                'label' => 'Divider', 'category' => 'layout', 'description' => 'Vertical rhythm between sections.',
                'variants' => ['line' => 'Line', 'space' => 'Space'],
                'container' => false,
                'props' => ['size' => ['type' => 'enum', 'values' => ['small', 'medium', 'large']]],
                'defaults' => ['size' => 'medium'],
            ],
        ];

        $registry = [];
        foreach ($definitions as $key => $definition) {
            $registry[$key] = [
                'key' => $key,
                'type' => $key,
                'version' => 1,
                'label' => $definition['label'],
                'category' => $definition['category'],
                'description' => $definition['description'],
                'variants' => array_map(
                    static fn (string $id, string $label): array => ['id' => $id, 'label' => $label],
                    array_keys($definition['variants']),
                    array_values($definition['variants'])
                ),
                'props' => $definition['props'],
                'defaults' => $definition['defaults'],
                'template' => $key . '.php',
                'container' => $definition['container'],
                'status' => 'stable',
                'schemaVersion' => self::SCHEMA_VERSION,
            ];
        }
        return self::$cache = $registry;
    }

    /** @return array<string,mixed>|null */
    public static function get(string $type): ?array
    {
        return self::all()[$type] ?? null;
    }

    public static function has(string $type): bool
    {
        return self::get($type) !== null;
    }

    /** @return list<string> */
    public static function variantIds(string $type): array
    {
        $definition = self::get($type);
        if ($definition === null) {
            return [];
        }
        return array_map(static fn (array $variant): string => $variant['id'], $definition['variants']);
    }

    /** @return list<array{id:string,label:string}> */
    public static function categories(): array
    {
        return [
            ['id' => 'navigation', 'label' => 'Navigation & footer'],
            ['id' => 'hero', 'label' => 'Hero & landing'],
            ['id' => 'bento', 'label' => 'Bento layouts'],
            ['id' => 'content', 'label' => 'Editorial & features'],
            ['id' => 'media', 'label' => 'Media & galleries'],
            ['id' => 'social-proof', 'label' => 'Testimonials & logos'],
            ['id' => 'stats', 'label' => 'Statistics'],
            ['id' => 'timeline', 'label' => 'Timelines & process'],
            ['id' => 'comparison', 'label' => 'Comparison & pricing'],
            ['id' => 'conversion', 'label' => 'Conversion'],
            ['id' => 'forms', 'label' => 'Forms'],
            ['id' => 'layout', 'label' => 'Layout & utility'],
        ];
    }

    /** Editor-facing catalogue (no template paths). @return list<array<string,mixed>> */
    public static function catalog(): array
    {
        $catalog = [];
        foreach (self::all() as $definition) {
            $catalog[] = [
                'type' => $definition['type'],
                'version' => $definition['version'],
                'label' => $definition['label'],
                'category' => $definition['category'],
                'description' => $definition['description'],
                'variants' => $definition['variants'],
                'schema' => $definition['props'],
                'defaultProps' => $definition['defaults'],
                'status' => $definition['status'],
                'container' => $definition['container'],
            ];
        }
        return $catalog;
    }

    /** Stable fingerprint of the registry, used to detect code/schema drift. */
    public static function fingerprint(): string
    {
        return Json::checksum(self::all());
    }
}
