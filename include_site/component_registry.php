<?php
/**
 * CMS component definitions. The marker is a legacy data key, never a PHP path.
 * Keep renderer paths here so neither a request nor a database row can select a file.
 */
function exacdComponentRegistry()
{
    static $registry = null;
    if ($registry !== null) {
        return $registry;
    }

    $field = static function ($label, $type = 'text', $required = false) {
        return ['label' => $label, 'type' => $type, 'required' => $required];
    };
    $text = static function ($key, $label, $marker, $template, $fields, $category = 'content') {
        return [
            'key' => $key, 'label' => $label, 'category' => $category,
            'marker' => $marker, 'storage' => 'text', 'template' => $template,
            'variant' => 'default', 'variants' => ['default' => $marker],
            'fields' => $fields, 'multiple' => false, 'defaults' => [],
            'schema_version' => 1, 'legacy_editor' => 'section.php',
            'hidden' => false, 'builder_hidden' => false,
        ];
    };
    $content = static function ($key, $label, $marker, $template, $fields, $category = 'content') {
        return [
            'key' => $key, 'label' => $label, 'category' => $category,
            'marker' => $marker, 'storage' => 'content', 'template' => $template,
            'variant' => 'default', 'variants' => ['default' => $marker],
            'fields' => $fields, 'multiple' => true, 'defaults' => [],
            'schema_version' => 1, 'legacy_editor' => 'misctwo.php',
            'hidden' => false, 'builder_hidden' => false,
        ];
    };
    $titleFields = [
        'title' => $field('عنوان', 'richtext'),
        'undertitle' => $field('زیرعنوان', 'richtext'),
    ];
    $customHeadingFields = [
        'title' => $field('عنوان', 'richtext'),
        'undertitle' => $field('زیرعنوان', 'richtext'),
        'img' => $field('تصویر و فایل‌ها', 'image'),
        'fld1' => $field('عنوان فایل اول'),
        'fld2' => $field('عنوان فایل دوم'),
        'fld3' => $field('توضیح فایل اول'),
        'fld4' => $field('توضیح فایل دوم'),
    ];
    $mediaFields = [
        'title' => $field('عنوان'),
        'text' => $field('محتوا', 'richtext'),
        'img' => $field('تصویر', 'image'),
        'file' => $field('فایل'),
        'pageurl' => $field('لینک', 'url'),
    ];

    $registry = [
        'raw_page' => $text('raw_page', 'صفحه خام', 'صفحه خام', 'components_site/page.php', [
            'fld4' => $field('متن صفحه', 'richtext'),
        ]),
        'top_image' => $text('top_image', 'تصویر بالای صفحه', 'تصویر بالای صفحه', 'components_site/top-image.php', [
            'title' => $field('لینک تصویر', 'url'), 'img' => $field('تصویر', 'image'),
        ], 'media'),
        'footer_cta' => $text('footer_cta', 'شعار بالای فوتر', 'شعار بالای فوتر', 'components_site/footer-title.php', [
            'title' => $field('عنوان', 'richtext'), 'fld1' => $field('متن', 'richtext'),
            'fld2' => $field('متن دکمه'), 'fld3' => $field('لینک دکمه', 'url'),
        ], 'actions'),
        'map' => $text('map', 'نقشه', 'مدل نقشه', 'components_site/map.php', [
            'title' => $field('عنوان', 'richtext'), 'undertitle' => $field('زیرعنوان', 'richtext'),
            'fld1' => $field('توضیح', 'richtext'), 'fld2' => $field('متن لینک'),
            'fld3' => $field('لینک', 'url'),
        ], 'media'),
        'title' => $text('title', 'عنوان', 'تایتل مدل یک', 'components_site/title-model-one.php', $titleFields),

        'slider' => $content('slider', 'اسلایدر', 'اسلایدر', 'components_site/slider.php', $mediaFields, 'media'),
        'logo_tooltip' => $content('logo_tooltip', 'لوگو با توضیح', 'لوگو تولتیپ', 'components_site/logo-tooltip.php', [
            'title' => $field('عنوان'), 'text' => $field('متن راهنما', 'textarea'),
            'img' => $field('لوگو', 'image'), 'pageurl' => $field('لینک', 'url'),
        ], 'media'),
        'vertical_list' => $content('vertical_list', 'فهرست عمودی', 'لیست زیر هم', 'components_site/list-zire-ham.php', $mediaFields + [
            'color' => $field('رنگ', 'color'),
        ], 'lists'),
        'horizontal_list' => $content('horizontal_list', 'فهرست افقی', 'لیست کنار هم', 'components_site/list-kenar-ham.php', [
            'title' => $field('عنوان'), 'text' => $field('متن', 'richtext'),
            'pageurl' => $field('لینک', 'url'),
        ], 'lists'),
        'custom_one' => $content('custom_one', 'فهرست ترکیبی یک', 'مدل کاستومایز یک لیست', 'components_site/custom-one.php', [
            'title' => $field('عنوان آیتم'), 'url' => $field('شناسه صفحه'),
        ], 'composites'),
        'custom_three' => $content('custom_three', 'فهرست ترکیبی سه', 'مدل کاستومایز سوم لیست', 'components_site/custom-three.php', [
            'title' => $field('عنوان آیتم'), 'url' => $field('شناسه صفحه'),
        ], 'composites'),
        'image_slider' => $content('image_slider', 'اسلایدر تصویر', 'اسلایدر تصویر', 'components_site/image-slider.php', [
            'title' => $field('عنوان'), 'img' => $field('تصویر', 'image'),
            'pageurl' => $field('لینک', 'url'),
        ], 'media'),
        'content_slider' => $content('content_slider', 'اسلایدر محتوا', 'اسلایدر محتوی', 'components_site/slider-content.php', $mediaFields, 'media'),
        'horizontal_list_alt' => $content('horizontal_list_alt', 'فهرست افقی دوم', 'لیست کنار هم دوم', 'components_site/section-side.php', [
            'title' => $field('عنوان'), 'text' => $field('متن', 'richtext'),
            'pageurl' => $field('لینک', 'url'),
        ], 'lists'),
        'single_content' => $content('single_content', 'محتوای تکی', 'محتوی تکی', 'components_site/single-content.php', [
            'title' => $field('عنوان'), 'text' => $field('متن', 'richtext'),
            'img' => $field('تصویر', 'image'),
        ]),
        'split_content' => $content('split_content', 'متن و تصویر', 'محتوی چپ و راست', 'components_site/left-right-content.php', $mediaFields + [
            'color' => $field('رنگ', 'color'),
        ]),
        'tabs' => $content('tabs', 'زبانه‌ها', 'محتوی تب', 'components_site/tab-content.php', [
            'title' => $field('عنوان زبانه'), 'text' => $field('متن', 'richtext'),
            'img' => $field('تصویر', 'image'),
        ], 'composites'),
        'split_content_alt' => $content('split_content_alt', 'متن و تصویر دوم', 'محتوی چپ و راست دوم', 'components_site/left-right-content2.php', $mediaFields + [
            'color' => $field('رنگ', 'color'),
        ]),
        'circle_slider' => $content('circle_slider', 'اسلایدر دایره‌ای', 'اسلایدر دایره', 'components_site/circle-slider.php', $mediaFields, 'media'),
        'custom_two' => $content('custom_two', 'فهرست ترکیبی دو', 'مدل کاستومایز دوم لیست', 'components_site/custom-two.php', [
            'title' => $field('عنوان آیتم'), 'url' => $field('شناسه صفحه'),
        ], 'composites'),
        'custom_four' => $content('custom_four', 'فهرست ترکیبی چهار', 'مدل کاستومایز چهارم لیست', 'components_site/custom-four.php', [
            'title' => $field('عنوان آیتم'), 'url' => $field('شناسه صفحه'),
        ], 'composites'),
        'middle_box' => $content('middle_box', 'باکس میانی', 'باکس میانی', 'components_site/middle-box.php', $mediaFields),
        'middle_box_alt' => $content('middle_box_alt', 'باکس میانی دو', 'باکس میانی دو', 'components_site/middle-box2.php', $mediaFields),
        'middle_box_third' => $content('middle_box_third', 'باکس میانی سه', 'باکس میانی سه', 'components_site/middle-box3.php', $mediaFields),
        'slider_box' => $content('slider_box', 'اسلایدر باکس', 'اسلایدر باکس', 'components_site/slider-box.php', [
            'title' => $field('عنوان'), 'img' => $field('تصویر', 'image'),
            'pageurl' => $field('لینک', 'url'),
        ], 'media'),
        'slider_box_link' => $content('slider_box_link', 'اسلایدر باکس لینکی', 'اسلایدر باکس لینکی', 'components_site/slider-box-link.php', [
            'title' => $field('عنوان'), 'img' => $field('تصویر', 'image'),
            'pageurl' => $field('لینک', 'url'),
        ], 'media'),
        'logo_plain' => $content('logo_plain', 'لوگو', 'لوگو بدون تولتیپ', 'components_site/logo.php', [
            'title' => $field('عنوان'), 'img' => $field('لوگو', 'image'),
            'pageurl' => $field('لینک', 'url'),
        ], 'media'),
        'faq' => $content('faq', 'سوالات متداول', 'سوالات متداول', 'components_site/faq.php', [
            'title' => $field('سوال', 'text', true), 'text' => $field('پاسخ', 'richtext', true),
        ], 'composites'),
        'join_us' => $content('join_us', 'پیوستن به ما', 'پیوستن به ما', 'components_site/job.php', [
            'title' => $field('عنوان'), 'text' => $field('متن', 'richtext'),
            'img' => $field('تصویر', 'image'),
        ], 'actions'),
        'faq_alt' => $content('faq_alt', 'سوالات متداول دوم', 'سوالات متداول دوم', 'components_site/aca.php', [
            'title' => $field('سوال', 'text', true), 'text' => $field('پاسخ', 'richtext', true),
        ], 'composites'),
    ];

    $registry['title']['variants'] = [
        'one' => 'تایتل مدل یک', 'two' => 'تایتل مدل دو',
        'three' => 'تایتل مدل سه', 'four' => 'تایتل مدل چهار',
    ];
    $registry['title']['variant'] = 'one';

    $form = $content('faq_form', 'فرم همکاری سوالات', 'فرم همکاری سوالات', 'components_site/faq_form.php', [], 'forms');
    $form['storage'] = 'none';
    $form['multiple'] = false;
    $registry['faq_form'] = $form;
    $form = $content('contact_form', 'فرم تماس با ما', 'فرم تماس با ما', 'components_site/contact.php', [], 'forms');
    $form['storage'] = 'none';
    $form['multiple'] = false;
    $registry['contact_form'] = $form;

    foreach (['one' => 'یک', 'two' => 'دوم', 'three' => 'سوم', 'four' => 'چهارم'] as $suffix => $number) {
        $key = 'custom_' . $suffix;
        $marker = 'مدل کاستومایز ' . $number . ' عنوان';
        $registry[$key]['companion_marker'] = $marker;
        $registry[$key]['companion_fields'] = $customHeadingFields;
        $companion = $text($key . '_heading', 'عنوان فهرست ترکیبی ' . $number, $marker, null, $customHeadingFields, 'composites');
        $companion['hidden'] = true;
        $companion['builder_hidden'] = true;
        $registry[$key . '_heading'] = $companion;
    }

    return $registry;
}

function exacdComponentDefinitionByMarker($marker)
{
    foreach (exacdComponentRegistry() as $definition) {
        foreach ($definition['variants'] as $variant => $allowedMarker) {
            if ($allowedMarker === $marker) {
                $definition['marker'] = $allowedMarker;
                $definition['variant'] = $variant;
                return $definition;
            }
        }
    }
    return null;
}

function exacdComponentDefinitionByTypeAndMarker($typeKey, $marker)
{
    $registry = exacdComponentRegistry();
    if (!isset($registry[$typeKey]) || !empty($registry[$typeKey]['hidden'])) {
        return null;
    }
    foreach ($registry[$typeKey]['variants'] as $variant => $allowedMarker) {
        if ($allowedMarker === $marker) {
            $definition = $registry[$typeKey];
            $definition['marker'] = $allowedMarker;
            $definition['variant'] = $variant;
            return $definition;
        }
    }
    return null;
}
