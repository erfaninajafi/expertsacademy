<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Contract;

use ExpertsAcademy\Studio\DesignTokens;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Sanitizer;
use ExpertsAcademy\Studio\Uuid;

/**
 * Strict validator for the versioned page document.
 *
 * Deterministic: the same document always produces the same error map.
 * Unknown keys are rejected so a typo cannot be silently stored and later
 * ignored by the renderer. Validation happens on every save and on publish.
 */
final class Validator
{
    /** @return array<string,string> path => message */
    public function errors(mixed $document): array
    {
        $errors = [];
        if (!is_array($document)) {
            return ['document' => 'A page document object is required.'];
        }

        $this->keys($errors, $document, 'document', ['schemaVersion','title','locale','direction','seo','theme','settings','sections']);
        if (($document['schemaVersion'] ?? null) !== PageDocument::SCHEMA_VERSION) {
            $errors['document.schemaVersion'] = 'Only schemaVersion 1 is supported.';
        }

        $this->string($errors, $document, 'title', 'document.title', 1, 200, true);
        $this->enum($errors, $document, 'locale', 'document.locale', ['fa','en'], true);
        $this->enum($errors, $document, 'direction', 'document.direction', ['rtl','ltr'], true);

        $this->seo($errors, $document['seo'] ?? null);
        $this->theme($errors, $document['theme'] ?? null);
        $this->settings($errors, $document['settings'] ?? null);
        $this->sections($errors, $document['sections'] ?? null);

        return $errors;
    }

    public function assert(mixed $document): void
    {
        $errors = $this->errors($document);
        if ($errors !== []) {
            throw Problem::invalid($errors, 'The page document is invalid.');
        }
    }

    private function seo(array &$errors, mixed $seo): void
    {
        if (!is_array($seo)) {
            $errors['document.seo'] = 'SEO settings are required.';
            return;
        }
        $this->keys($errors, $seo, 'document.seo', ['title','description','keywords','canonical','ogImage','robots']);
        $this->string($errors, $seo, 'title', 'document.seo.title', 0, 200);
        $this->string($errors, $seo, 'description', 'document.seo.description', 0, 320);
        $keywords = $seo['keywords'] ?? null;
        if (!is_array($keywords) || !array_is_list($keywords)) {
            $errors['document.seo.keywords'] = 'Keywords must be a list.';
        } else {
            if (count($keywords) > 20) {
                $errors['document.seo.keywords'] = 'At most 20 keywords are allowed.';
            }
            foreach ($keywords as $index => $keyword) {
                if (!is_string($keyword) || mb_strlen($keyword) > 60) {
                    $errors["document.seo.keywords[$index]"] = 'Each keyword must be a string of at most 60 characters.';
                }
            }
        }
        if (array_key_exists('robots', $seo)) {
            $robots = $seo['robots'];
            if (!is_string($robots) || !in_array($robots, ['index,follow','noindex,follow','index,nofollow','noindex,nofollow'], true)) {
                $errors['document.seo.robots'] = 'Robots must be one of index,follow / noindex,follow / index,nofollow / noindex,nofollow.';
            }
        }
        $canonical = $seo['canonical'] ?? '';
        if (!is_string($canonical) || ($canonical !== '' && !Sanitizer::isSafeUrl($canonical))) {
            $errors['document.seo.canonical'] = 'Canonical URL must be empty or an http(s) URL.';
        }
        if (array_key_exists('ogImage', $seo) && $seo['ogImage'] !== null) {
            $this->media($errors, $seo['ogImage'], 'document.seo.ogImage', false);
        }
    }

    private function theme(array &$errors, mixed $theme): void
    {
        if (!is_array($theme)) {
            $errors['document.theme'] = 'Theme settings are required.';
            return;
        }
        $this->keys($errors, $theme, 'document.theme', ['id','version','tokens']);
        if (($theme['id'] ?? null) !== null && !Uuid::isValid($theme['id'])) {
            $errors['document.theme.id'] = 'Theme identifier must be a UUID or null.';
        }
        if (($theme['version'] ?? null) !== null && (!is_int($theme['version']) || $theme['version'] < 1)) {
            $errors['document.theme.version'] = 'Theme version must be a positive integer or null.';
        }
        foreach (DesignTokens::errors($theme['tokens'] ?? null) as $path => $message) {
            $errors['document.theme.' . $path] = $message;
        }
    }

    private function settings(array &$errors, mixed $settings): void
    {
        if (!is_array($settings)) {
            $errors['document.settings'] = 'Layout settings are required.';
            return;
        }
        $this->keys($errors, $settings, 'document.settings', ['container','background']);
        $container = $settings['container'] ?? null;
        if (!is_int($container) || $container < 960 || $container > 1600) {
            $errors['document.settings.container'] = 'Container width must be an integer between 960 and 1600.';
        }
        $this->enum($errors, $settings, 'background', 'document.settings.background', ['canvas','surface','muted'], true);
    }

    private function sections(array &$errors, mixed $sections): void
    {
        if (!is_array($sections) || !array_is_list($sections)) {
            $errors['document.sections'] = 'Sections must be a list.';
            return;
        }
        if (count($sections) < 1) {
            $errors['document.sections'] = 'At least one section is required.';
        }
        if (count($sections) > PageDocument::MAX_SECTIONS) {
            $errors['document.sections'] = 'At most ' . PageDocument::MAX_SECTIONS . ' sections are allowed.';
        }
        $ids = [];
        foreach ($sections as $index => $section) {
            $path = "document.sections[$index]";
            if (!is_array($section)) {
                $errors[$path] = 'Each section must be an object.';
                continue;
            }
            $this->keys($errors, $section, $path, ['id','label','layout','visibility','components']);
            $id = $section['id'] ?? null;
            if (!Uuid::isValid($id)) {
                $errors[$path . '.id'] = 'Section id must be a UUID.';
            } elseif (isset($ids[$id])) {
                $errors[$path . '.id'] = 'Section ids must be unique.';
            } else {
                $ids[$id] = true;
            }
            $this->string($errors, $section, 'label', $path . '.label', 0, 120);

            $layout = $section['layout'] ?? null;
            if (!is_array($layout)) {
                $errors[$path . '.layout'] = 'Section layout is required.';
            } else {
                $this->keys($errors, $layout, $path . '.layout', ['width','columns','gap','paddingY','background']);
                $this->enum($errors, $layout, 'width', $path . '.layout.width', ['contained','full'], true);
                $this->intRange($errors, $layout, 'columns', $path . '.layout.columns', 1, 12, true);
                $this->intRange($errors, $layout, 'gap', $path . '.layout.gap', 0, 64, true);
                $this->intRange($errors, $layout, 'paddingY', $path . '.layout.paddingY', 0, 200, true);
                $this->enum($errors, $layout, 'background', $path . '.layout.background', ['none','canvas','surface','accent'], true);
            }

            $this->visibility($errors, $section['visibility'] ?? null, $path . '.visibility');
            $this->components($errors, $section['components'] ?? null, $path . '.components');
        }
    }

    private function components(array &$errors, mixed $components, string $path): void
    {
        if (!is_array($components) || !array_is_list($components)) {
            $errors[$path] = 'Components must be a list.';
            return;
        }
        if (count($components) > PageDocument::MAX_COMPONENTS_PER_SECTION) {
            $errors[$path] = 'At most ' . PageDocument::MAX_COMPONENTS_PER_SECTION . ' components are allowed per section.';
        }
        $ids = [];
        foreach ($components as $index => $component) {
            $componentPath = "{$path}[$index]";
            if (!is_array($component)) {
                $errors[$componentPath] = 'Each component must be an object.';
                continue;
            }
            $this->keys($errors, $component, $componentPath, ['id','type','version','variant','props','design','responsive','animation','visibility','a11y']);
            $id = $component['id'] ?? null;
            if (!Uuid::isValid($id)) {
                $errors[$componentPath . '.id'] = 'Component id must be a UUID.';
            } elseif (isset($ids[$id])) {
                $errors[$componentPath . '.id'] = 'Component ids must be unique.';
            } else {
                $ids[$id] = true;
            }

            $type = $component['type'] ?? null;
            if (!is_string($type) || ComponentRegistry::get($type) === null) {
                $errors[$componentPath . '.type'] = 'Unknown component type.';
                continue;
            }
            $definition = ComponentRegistry::get($type);
            if (!is_int($component['version'] ?? null) || $component['version'] !== $definition['version']) {
                $errors[$componentPath . '.version'] = 'Component version must be ' . $definition['version'] . '.';
            }
            $variant = $component['variant'] ?? null;
            if (!is_string($variant) || !in_array($variant, ComponentRegistry::variantIds($type), true)) {
                $errors[$componentPath . '.variant'] = 'Unknown variant for this component.';
            }

            $this->props($errors, $component['props'] ?? null, $definition['props'], $componentPath . '.props');
            $this->design($errors, $component['design'] ?? null, $componentPath . '.design');
            $this->responsive($errors, $component['responsive'] ?? null, $componentPath . '.responsive');
            $this->animation($errors, $component['animation'] ?? null, $componentPath . '.animation');
            $this->visibility($errors, $component['visibility'] ?? null, $componentPath . '.visibility');
            $this->a11y($errors, $component['a11y'] ?? null, $componentPath . '.a11y');
        }
    }

    /** @param array<string,mixed> $schema Fields that are not in the schema are rejected. */
    private function props(array &$errors, mixed $props, array $schema, string $path): void
    {
        if (!is_array($props)) {
            $errors[$path] = 'Component properties must be an object.';
            return;
        }
        foreach (array_keys($props) as $key) {
            if (!array_key_exists($key, $schema)) {
                $errors["$path.$key"] = 'Unknown property.';
            }
        }
        foreach ($schema as $key => $rules) {
            $value = $props[$key] ?? null;
            $required = (bool) ($rules['required'] ?? false);
            if ($value === null || $value === '') {
                if ($required) {
                    $errors["$path.$key"] = 'This property is required.';
                }
                continue;
            }
            $this->propValue($errors, $value, $rules, "$path.$key");
        }
    }

    /** @param array<string,mixed> $rules */
    private function propValue(array &$errors, mixed $value, array $rules, string $path): void
    {
        $type = $rules['type'];
        if ($type === 'string' || $type === 'text') {
            if (!is_string($value)) {
                $errors[$path] = 'Expected a string.';
                return;
            }
            if (mb_strlen($value) > (int) $rules['max']) {
                $errors[$path] = 'Must be at most ' . (int) $rules['max'] . ' characters.';
            }
            if ($type === 'string' && preg_match('/[\r\n]/', $value) === 1) {
                $errors[$path] = 'Line breaks are not allowed here.';
            }
            return;
        }
        if ($type === 'richtext') {
            if (!is_string($value)) {
                $errors[$path] = 'Expected rich text.';
                return;
            }
            if (mb_strlen($value) > (int) $rules['max']) {
                $errors[$path] = 'Must be at most ' . (int) $rules['max'] . ' characters.';
            }
            return;
        }
        if ($type === 'url') {
            if (!is_string($value) || !Sanitizer::isSafeUrl($value)) {
                $errors[$path] = 'Expected a relative path, anchor or http(s) URL.';
            }
            return;
        }
        if ($type === 'enum') {
            if (!is_string($value) || !in_array($value, $rules['values'], true)) {
                $errors[$path] = 'Unsupported value.';
            }
            return;
        }
        if ($type === 'media') {
            $this->media($errors, $value, $path, (bool) ($rules['required'] ?? false));
            return;
        }
        if ($type === 'list') {
            if (!is_array($value) || !array_is_list($value)) {
                $errors[$path] = 'Expected a list.';
                return;
            }
            $count = count($value);
            if (isset($rules['min']) && $count < (int) $rules['min']) {
                $errors[$path] = 'At least ' . (int) $rules['min'] . ' items are required.';
            }
            if (isset($rules['max']) && $count > (int) $rules['max']) {
                $errors[$path] = 'At most ' . (int) $rules['max'] . ' items are allowed.';
            }
            foreach ($value as $index => $item) {
                if (!is_array($item)) {
                    $errors["$path[$index]"] = 'Each item must be an object.';
                    continue;
                }
                foreach (array_keys($item) as $key) {
                    if (!array_key_exists($key, $rules['item'])) {
                        $errors["$path[$index].$key"] = 'Unknown property.';
                    }
                }
                foreach ($rules['item'] as $key => $itemRules) {
                    $itemValue = $item[$key] ?? null;
                    if ($itemValue === null || $itemValue === '') {
                        if ((bool) ($itemRules['required'] ?? false)) {
                            $errors["$path[$index].$key"] = 'This property is required.';
                        }
                        continue;
                    }
                    $this->propValue($errors, $itemValue, $itemRules, "$path[$index].$key");
                }
            }
            return;
        }
        if ($type === 'object') {
            if (!is_array($value)) {
                $errors[$path] = 'Expected an object.';
                return;
            }
            foreach (array_keys($value) as $key) {
                if (!array_key_exists($key, $rules['fields'])) {
                    $errors["$path.$key"] = 'Unknown property.';
                }
            }
            foreach ($rules['fields'] as $key => $fieldRules) {
                $fieldValue = $value[$key] ?? null;
                if ($fieldValue === null || $fieldValue === '') {
                    if ((bool) ($fieldRules['required'] ?? false)) {
                        $errors["$path.$key"] = 'This property is required.';
                    }
                    continue;
                }
                $this->propValue($errors, $fieldValue, $fieldRules, "$path.$key");
            }
            return;
        }
        $errors[$path] = 'Unsupported property schema.';
    }

    private function media(array &$errors, mixed $value, string $path, bool $required): void
    {
        if (!is_array($value)) {
            if ($required) {
                $errors[$path] = 'A media reference is required.';
            }
            return;
        }
        foreach (array_keys($value) as $key) {
            if (!in_array($key, ['id','alt'], true)) {
                $errors["$path.$key"] = 'Unknown media reference field.';
            }
        }
        if (!Uuid::isValid($value['id'] ?? null)) {
            $errors[$path . '.id'] = 'Media id must be a UUID.';
        }
        if (isset($value['alt']) && (!is_string($value['alt']) || mb_strlen($value['alt']) > 300)) {
            $errors[$path . '.alt'] = 'Alternative text must be at most 300 characters.';
        }
    }

    private function design(array &$errors, mixed $design, string $path): void
    {
        if (!is_array($design)) {
            $errors[$path] = 'Design settings must be an object.';
            return;
        }
        $this->keys($errors, $design, $path, ['align','tone','spacing']);
        $this->enum($errors, $design, 'align', $path . '.align', ['start','center','end'], true);
        $this->enum($errors, $design, 'tone', $path . '.tone', ['default','inverse','accent'], true);
        $this->enum($errors, $design, 'spacing', $path . '.spacing', ['sm','md','lg'], true);
    }

    private function responsive(array &$errors, mixed $responsive, string $path): void
    {
        if (!is_array($responsive)) {
            $errors[$path] = 'Responsive overrides must be an object.';
            return;
        }
        foreach ($responsive as $breakpoint => $overrides) {
            $breakpointPath = "$path.$breakpoint";
            if (!in_array($breakpoint, ['mobile','tablet','desktop'], true)) {
                $errors[$breakpointPath] = 'Unknown breakpoint.';
                continue;
            }
            if (!is_array($overrides)) {
                $errors[$breakpointPath] = 'Overrides must be an object.';
                continue;
            }
            $this->keys($errors, $overrides, $breakpointPath, ['columns','align','size','hide']);
            if (array_key_exists('columns', $overrides)) {
                $this->intRange($errors, $overrides, 'columns', $breakpointPath . '.columns', 1, 4, true);
            }
            if (array_key_exists('align', $overrides)) {
                $this->enum($errors, $overrides, 'align', $breakpointPath . '.align', ['start','center','end'], true);
            }
            if (array_key_exists('size', $overrides)) {
                $this->enum($errors, $overrides, 'size', $breakpointPath . '.size', ['sm','md','lg'], true);
            }
            if (array_key_exists('hide', $overrides) && !is_bool($overrides['hide'])) {
                $errors[$breakpointPath . '.hide'] = 'Hide must be a boolean.';
            }
        }
    }

    private function animation(array &$errors, mixed $animation, string $path): void
    {
        if (!is_array($animation)) {
            $errors[$path] = 'Animation settings must be an object.';
            return;
        }
        $this->keys($errors, $animation, $path, ['preset','duration','delay','easing']);
        $this->enum($errors, $animation, 'preset', $path . '.preset', ['none','fade','rise','stagger'], true);
        $this->intRange($errors, $animation, 'duration', $path . '.duration', 0, 1200, true);
        $this->intRange($errors, $animation, 'delay', $path . '.delay', 0, 600, true);
        $this->enum($errors, $animation, 'easing', $path . '.easing', ['standard','calm'], true);
    }

    private function visibility(array &$errors, mixed $visibility, string $path): void
    {
        if (!is_array($visibility)) {
            $errors[$path] = 'Visibility settings must be an object.';
            return;
        }
        $this->keys($errors, $visibility, $path, ['devices']);
        $devices = $visibility['devices'] ?? null;
        if (!is_array($devices) || !array_is_list($devices) || $devices === []) {
            $errors[$path . '.devices'] = 'At least one device is required.';
            return;
        }
        foreach ($devices as $index => $device) {
            if (!in_array($device, ['desktop','tablet','mobile'], true)) {
                $errors["$path.devices[$index]"] = 'Unknown device.';
            }
        }
    }

    private function a11y(array &$errors, mixed $a11y, string $path): void
    {
        if (!is_array($a11y)) {
            $errors[$path] = 'Accessibility settings must be an object.';
            return;
        }
        $this->keys($errors, $a11y, $path, ['landmark','label','headingLevel']);
        $this->enum($errors, $a11y, 'landmark', $path . '.landmark', ['none','region','complementary'], true);
        $this->string($errors, $a11y, 'label', $path . '.label', 0, 160);
        $this->intRange($errors, $a11y, 'headingLevel', $path . '.headingLevel', 2, 4, true);
    }

    /** @param array<string,mixed> $value @param list<string> $allowed */
    private function keys(array &$errors, array $value, string $path, array $allowed): void
    {
        foreach (array_keys($value) as $key) {
            if (!in_array($key, $allowed, true)) {
                $errors[$path . '.' . $key] = 'Unknown field.';
            }
        }
    }

    /** @param array<string,mixed> $value */
    private function string(array &$errors, array $value, string $key, string $path, int $min, int $max, bool $required = false): void
    {
        $field = $value[$key] ?? null;
        if ($field === null) {
            if ($required) {
                $errors[$path] = 'This field is required.';
            }
            return;
        }
        if (!is_string($field)) {
            $errors[$path] = 'Expected a string.';
            return;
        }
        $length = mb_strlen($field);
        if ($length < $min || $length > $max) {
            $errors[$path] = "Must be between {$min} and {$max} characters.";
        }
    }

    /** @param array<string,mixed> $value @param list<string> $allowed */
    private function enum(array &$errors, array $value, string $key, string $path, array $allowed, bool $required = false): void
    {
        $field = $value[$key] ?? null;
        if ($field === null) {
            if ($required) {
                $errors[$path] = 'This field is required.';
            }
            return;
        }
        if (!is_string($field) || !in_array($field, $allowed, true)) {
            $errors[$path] = 'Unsupported value.';
        }
    }

    /** @param array<string,mixed> $value */
    private function intRange(array &$errors, array $value, string $key, string $path, int $min, int $max, bool $required = false): void
    {
        $field = $value[$key] ?? null;
        if ($field === null) {
            if ($required) {
                $errors[$path] = 'This field is required.';
            }
            return;
        }
        if (!is_int($field) || $field < $min || $field > $max) {
            $errors[$path] = "Must be an integer between {$min} and {$max}.";
        }
    }
}
