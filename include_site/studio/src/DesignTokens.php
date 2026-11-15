<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio;

/**
 * Design token document (schemaVersion 2) and its CSS-variable compiler.
 *
 * Phase 2 adds optional token groups (semantic colours, font weights, spacing
 * scale, grid, gradients, motion presets, RTL settings). They are optional so
 * documents and themes created in Phase 1 keep validating unchanged; the
 * compiler always falls back to the documented defaults.
 */
final class DesignTokens
{
    public const SCHEMA_VERSION = 2;

    /**
     * Complete a stored token document with any missing optional defaults, so a
     * draft created before a new token group was introduced stays editable.
     *
     * @param array<string,mixed> $tokens
     * @return array<string,mixed>
     */
    public static function withDefaults(array $tokens): array
    {
        $mode = ($tokens['mode'] ?? 'light') === 'dark' ? 'dark' : 'light';
        return self::mergeDefaults(self::defaults($mode), $tokens);
    }

    /**
     * @param array<string,mixed> $defaults
     * @param array<string,mixed> $tokens
     * @return array<string,mixed>
     */
    private static function mergeDefaults(array $defaults, array $tokens): array
    {
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $tokens)) {
                $tokens[$key] = $value;
            } elseif (is_array($value) && is_array($tokens[$key])) {
                $tokens[$key] = self::mergeDefaults($value, $tokens[$key]);
            }
        }
        return $tokens;
    }

    /** @return array<string,mixed> */
    public static function defaults(string $mode = 'light'): array
    {
        return [
            'schemaVersion' => self::SCHEMA_VERSION,
            'name'          => 'Academy / Editorial',
            'mode'          => $mode,
            'colors' => [
                'light' => [
                    'brand' => '#824822', 'onBrand' => '#ffffff', 'surface' => '#ffffff',
                    'canvas' => '#f5f3ed', 'text' => '#172b26', 'muted' => '#59675f',
                    'border' => '#d5d9ce', 'accent' => '#e8c58b', 'success' => '#236149',
                    'danger' => '#a52e30', 'focus' => '#824822',
                    'info' => '#1f5f8b', 'warning' => '#8a5a00',
                ],
                'dark' => [
                    'brand' => '#e8c58b', 'onBrand' => '#172b26', 'surface' => '#20332c',
                    'canvas' => '#15271f', 'text' => '#f5f3ed', 'muted' => '#b8c6b9',
                    'border' => '#526458', 'accent' => '#8c562f', 'success' => '#a1d3ad',
                    'danger' => '#ffb3b1', 'focus' => '#e8c58b',
                    'info' => '#9dc7e6', 'warning' => '#f2c879',
                ],
            ],
            'typography' => [
                'latin' => 'editorial', 'rtl' => 'academy',
                'base' => 17, 'display' => 80, 'heading' => 48,
                'scale' => 1.24, 'lineHeight' => 1.7,
                'weights' => ['regular' => 400, 'medium' => 500, 'semibold' => 600, 'bold' => 700],
                'tracking' => ['display' => -0.02, 'body' => 0],
                'fluid' => ['min' => 15, 'max' => 18],
            ],
            'spacing'   => [
                'unit' => 4, 'section' => 88, 'card' => 32, 'gap' => 24,
                'scale' => [0, 4, 8, 12, 16, 24, 32, 48, 64, 96],
            ],
            'layout'    => [
                'container' => 1280, 'reading' => 680, 'columns' => 12,
                'wide' => 1440, 'narrow' => 960, 'grid' => ['gutter' => 24],
                'breakpoints' => ['mobile' => 640, 'tablet' => 960, 'desktop' => 1280],
            ],
            'radius'    => ['small' => 4, 'medium' => 12, 'large' => 24, 'xl' => 32, 'pill' => 999],
            'shadow'    => 'soft',
            'shadows'   => [
                'sm' => '0 1px 2px rgba(23,43,38,.08)',
                'md' => '0 12px 28px -20px rgba(23,43,38,.35)',
                'lg' => '0 32px 60px -36px rgba(23,43,38,.45)',
            ],
            'button'    => ['height' => 48, 'weight' => 600, 'style' => 'solid', 'radius' => 'pill', 'paddingX' => 28],
            'background'=> ['pattern' => 'none', 'gradient' => 'none', 'overlay' => 40],
            'gradients' => [
                'dawn'     => ['from' => '#824822', 'to' => '#e8c58b', 'angle' => 135],
                'midnight' => ['from' => '#172b26', 'to' => '#2e4a3f', 'angle' => 160],
                'sand'     => ['from' => '#f5f3ed', 'to' => '#e8c58b', 'angle' => 120],
                'forest'   => ['from' => '#236149', 'to' => '#8fb99a', 'angle' => 140],
            ],
            'motion'    => [
                'preset' => 'reveal', 'enabled' => true, 'duration' => 480, 'distance' => 20, 'easing' => 'calm',
                'presets' => [
                    'fade'  => ['distance' => 0, 'duration' => 320],
                    'rise'  => ['distance' => 20, 'duration' => 480],
                    'slide' => ['distance' => 48, 'duration' => 560],
                    'scale' => ['distance' => 0, 'duration' => 420],
                ],
            ],
            'rtl'       => ['fontScale' => 1, 'letterSpacing' => 0],
        ];
    }

    private const COLOR_KEYS = ['brand','onBrand','surface','canvas','text','muted','border','accent','success','danger','focus'];

    /** @return array<string,string> field => message */
    public static function errors(mixed $tokens): array
    {
        $errors = [];
        if (!is_array($tokens)) {
            return ['tokens' => 'Design tokens must be an object.'];
        }
        $known = ['schemaVersion','name','mode','colors','typography','spacing','layout','radius','shadow',
            'shadows','button','background','gradients','motion','rtl','logo'];
        foreach (array_keys($tokens) as $key) {
            if (!in_array($key, $known, true)) {
                $errors['tokens.' . $key] = 'Unknown token group.';
            }
        }
        if (($tokens['schemaVersion'] ?? null) !== self::SCHEMA_VERSION) {
            $errors['tokens.schemaVersion'] = 'Unsupported token schema version.';
        }
        if (!is_string($tokens['name'] ?? null) || mb_strlen($tokens['name']) > 120) {
            $errors['tokens.name'] = 'Token set name must be a string of at most 120 characters.';
        }
        if (!in_array($tokens['mode'] ?? null, ['light','dark'], true)) {
            $errors['tokens.mode'] = 'Mode must be light or dark.';
        }
        foreach (['light','dark'] as $mode) {
            foreach (self::COLOR_KEYS as $key) {
                $value = $tokens['colors'][$mode][$key] ?? null;
                if (!is_string($value) || preg_match('/\A#[0-9a-fA-F]{6}\z/D', $value) !== 1) {
                    $errors["tokens.colors.$mode.$key"] = 'Expected a hex colour.';
                }
            }
            foreach (['info','warning'] as $key) {
                if (!array_key_exists($key, $tokens['colors'][$mode] ?? [])) {
                    continue;
                }
                $value = $tokens['colors'][$mode][$key];
                if (!is_string($value) || preg_match('/\A#[0-9a-fA-F]{6}\z/D', $value) !== 1) {
                    $errors["tokens.colors.$mode.$key"] = 'Expected a hex colour.';
                }
            }
        }
        $typography = $tokens['typography'] ?? null;
        if (!is_array($typography)) {
            $errors['tokens.typography'] = 'Typography tokens are required.';
        } else {
            foreach (['latin','rtl'] as $key) {
                if (!in_array($typography[$key] ?? null, ['editorial','academy','manrope','system'], true)) {
                    $errors["tokens.typography.$key"] = 'Unknown font family token.';
                }
            }
            foreach (['base','display','heading'] as $key) {
                $value = $typography[$key] ?? null;
                if (!is_int($value) || $value < 12 || $value > 160) {
                    $errors["tokens.typography.$key"] = 'Font size must be an integer between 12 and 160.';
                }
            }
            if (!is_numeric($typography['scale'] ?? null) || (float) $typography['scale'] < 1.0 || (float) $typography['scale'] > 2.0) {
                $errors['tokens.typography.scale'] = 'Type scale must be between 1.0 and 2.0.';
            }
            if (!is_numeric($typography['lineHeight'] ?? null) || (float) $typography['lineHeight'] < 1.2 || (float) $typography['lineHeight'] > 2.2) {
                $errors['tokens.typography.lineHeight'] = 'Line height must be between 1.2 and 2.2.';
            }
            foreach (['regular','medium','semibold','bold'] as $weight) {
                if (!array_key_exists($weight, $typography['weights'] ?? [])) {
                    continue;
                }
                $value = $typography['weights'][$weight];
                if (!is_int($value) || $value < 100 || $value > 900 || $value % 100 !== 0) {
                    $errors["tokens.typography.weights.$weight"] = 'Font weight must be a multiple of 100 between 100 and 900.';
                }
            }
            foreach (['display','body'] as $key) {
                if (!array_key_exists($key, $typography['tracking'] ?? [])) {
                    continue;
                }
                if (!is_numeric($typography['tracking'][$key]) || abs((float) $typography['tracking'][$key]) > 0.1) {
                    $errors["tokens.typography.tracking.$key"] = 'Tracking must be between -0.1 and 0.1em.';
                }
            }
            if (array_key_exists('fluid', $typography)) {
                foreach (['min','max'] as $key) {
                    $value = $typography['fluid'][$key] ?? null;
                    if (!is_int($value) || $value < 12 || $value > 32) {
                        $errors["tokens.typography.fluid.$key"] = 'Fluid size must be an integer between 12 and 32.';
                    }
                }
                if ((int) ($typography['fluid']['min'] ?? 0) > (int) ($typography['fluid']['max'] ?? 0)) {
                    $errors['tokens.typography.fluid'] = 'Fluid minimum cannot exceed the maximum.';
                }
            }
        }
        foreach (['unit' => [1, 12], 'section' => [0, 240], 'card' => [0, 96], 'gap' => [0, 64]] as $key => [$min, $max]) {
            if (!is_int($tokens['spacing'][$key] ?? null) || $tokens['spacing'][$key] < $min || $tokens['spacing'][$key] > $max) {
                $errors["tokens.spacing.$key"] = "Spacing value must be an integer between {$min} and {$max}.";
            }
        }
        if (array_key_exists('scale', $tokens['spacing'] ?? [])) {
            $scale = $tokens['spacing']['scale'];
            if (!is_array($scale) || !array_is_list($scale) || $scale === [] || count($scale) > 16) {
                $errors['tokens.spacing.scale'] = 'Spacing scale must be a list of up to 16 integers.';
            } else {
                foreach ($scale as $index => $step) {
                    if (!is_int($step) || $step < 0 || $step > 192) {
                        $errors["tokens.spacing.scale[$index]"] = 'Each spacing step must be an integer between 0 and 192.';
                    }
                }
            }
        }
        foreach (['mobile' => [320, 768], 'tablet' => [600, 1200], 'desktop' => [960, 1920]] as $key => [$min, $max]) {
            if (!is_int($tokens['layout']['breakpoints'][$key] ?? null) || $tokens['layout']['breakpoints'][$key] < $min || $tokens['layout']['breakpoints'][$key] > $max) {
                $errors["tokens.layout.breakpoints.$key"] = 'Breakpoint outside the allowed range.';
            }
        }
        if (!is_int($tokens['layout']['container'] ?? null) || $tokens['layout']['container'] < 960 || $tokens['layout']['container'] > 1600) {
            $errors['tokens.layout.container'] = 'Container width must be between 960 and 1600.';
        }
        if (!is_int($tokens['layout']['reading'] ?? null) || $tokens['layout']['reading'] < 480 || $tokens['layout']['reading'] > 900) {
            $errors['tokens.layout.reading'] = 'Reading width must be between 480 and 900.';
        }
        foreach (['wide' => [1100, 1920], 'narrow' => [640, 1100]] as $key => [$min, $max]) {
            if (!array_key_exists($key, $tokens['layout'] ?? [])) {
                continue;
            }
            if (!is_int($tokens['layout'][$key]) || $tokens['layout'][$key] < $min || $tokens['layout'][$key] > $max) {
                $errors["tokens.layout.$key"] = "Width must be an integer between {$min} and {$max}.";
            }
        }
        if (!in_array($tokens['shadow'] ?? null, ['soft','crisp','none'], true)) {
            $errors['tokens.shadow'] = 'Unknown shadow token.';
        }
        if (array_key_exists('shadows', $tokens)) {
            foreach (['sm','md','lg'] as $key) {
                $value = $tokens['shadows'][$key] ?? null;
                if (!is_string($value) || $value === '' || mb_strlen($value) > 160 || preg_match('/[;{}<>]/', $value) === 1) {
                    $errors["tokens.shadows.$key"] = 'Shadow values must be plain CSS box-shadow strings.';
                }
            }
        }
        if (!in_array($tokens['button']['style'] ?? null, ['solid','outline'], true)) {
            $errors['tokens.button.style'] = 'Unknown button style.';
        }
        if (array_key_exists('radius', $tokens['button'] ?? []) && !in_array($tokens['button']['radius'], ['small','medium','large','xl','pill'], true)) {
            $errors['tokens.button.radius'] = 'Unknown button radius token.';
        }
        if (!in_array($tokens['background']['pattern'] ?? null, ['none','grid','grain'], true)) {
            $errors['tokens.background.pattern'] = 'Unknown background pattern.';
        }
        if (array_key_exists('gradients', $tokens)) {
            $allowed = ['dawn','midnight','sand','forest'];
            foreach ($tokens['gradients'] as $key => $gradient) {
                if (!in_array($key, $allowed, true)) {
                    $errors['tokens.gradients.' . $key] = 'Unknown gradient preset.';
                    continue;
                }
                foreach (['from','to'] as $stop) {
                    $value = $gradient[$stop] ?? null;
                    if (!is_string($value) || preg_match('/\A#[0-9a-fA-F]{6}\z/D', $value) !== 1) {
                        $errors["tokens.gradients.$key.$stop"] = 'Expected a hex colour.';
                    }
                }
                if (!is_int($gradient['angle'] ?? null) || $gradient['angle'] < 0 || $gradient['angle'] > 360) {
                    $errors["tokens.gradients.$key.angle"] = 'Gradient angle must be an integer between 0 and 360.';
                }
            }
        }
        if (!in_array($tokens['motion']['preset'] ?? null, ['none','reveal','stagger','emphasis'], true)) {
            $errors['tokens.motion.preset'] = 'Unknown motion preset.';
        }
        if (array_key_exists('presets', $tokens['motion'] ?? [])) {
            foreach (['fade','rise','slide','scale'] as $key) {
                if (!array_key_exists($key, $tokens['motion']['presets'])) {
                    continue;
                }
                $preset = $tokens['motion']['presets'][$key];
                foreach (['duration' => [0, 1600], 'distance' => [0, 160]] as $field => [$min, $max]) {
                    $value = $preset[$field] ?? null;
                    if (!is_int($value) || $value < $min || $value > $max) {
                        $errors["tokens.motion.presets.$key.$field"] = "Value must be an integer between {$min} and {$max}.";
                    }
                }
            }
        }
        if (array_key_exists('rtl', $tokens)) {
            $rtl = $tokens['rtl'];
            if (!is_numeric($rtl['fontScale'] ?? null) || (float) $rtl['fontScale'] < 0.8 || (float) $rtl['fontScale'] > 1.4) {
                $errors['tokens.rtl.fontScale'] = 'RTL font scale must be between 0.8 and 1.4.';
            }
            if (!is_numeric($rtl['letterSpacing'] ?? null) || abs((float) $rtl['letterSpacing']) > 0.08) {
                $errors['tokens.rtl.letterSpacing'] = 'RTL letter spacing must be between -0.08 and 0.08em.';
            }
        }
        return $errors;
    }

    /** @return array<string,string> */
    private static function fontStack(string $token): string
    {
        return match ($token) {
            'academy'  => "'Manrope', 'Segoe UI', system-ui, -apple-system, sans-serif",
            'manrope'  => "'Manrope', 'Helvetica Neue', system-ui, sans-serif",
            'system'   => "system-ui, -apple-system, 'Segoe UI', sans-serif",
            default    => "'Fraunces', 'Iowan Old Style', Georgia, 'Times New Roman', serif",
        };
    }

    /** Compiles validated tokens into CSS custom properties. */
    public static function css(array $tokens): string
    {
        $defaults = self::defaults();
        $mode = ($tokens['mode'] ?? 'light') === 'dark' ? 'dark' : 'light';
        $colors = $tokens['colors'][$mode] ?? $defaults['colors'][$mode];
        $typography = $tokens['typography'] ?? $defaults['typography'];
        $weights = $typography['weights'] ?? $defaults['typography']['weights'];
        $tracking = $typography['tracking'] ?? $defaults['typography']['tracking'];
        $fluid = $typography['fluid'] ?? $defaults['typography']['fluid'];
        $spacing = $tokens['spacing'] ?? $defaults['spacing'];
        $layout = $tokens['layout'] ?? $defaults['layout'];
        $radius = $tokens['radius'] ?? $defaults['radius'];
        $shadows = $tokens['shadows'] ?? $defaults['shadows'];
        $button = $tokens['button'] ?? $defaults['button'];
        $gradients = $tokens['gradients'] ?? $defaults['gradients'];
        $motion = $tokens['motion'] ?? $defaults['motion'];
        $motionPresets = $motion['presets'] ?? $defaults['motion']['presets'];
        $rtl = $tokens['rtl'] ?? $defaults['rtl'];

        $vars = [
            '--cms-brand' => $colors['brand'],
            '--cms-on-brand' => $colors['onBrand'],
            '--cms-surface' => $colors['surface'],
            '--cms-canvas' => $colors['canvas'],
            '--cms-text' => $colors['text'],
            '--cms-muted' => $colors['muted'],
            '--cms-border' => $colors['border'],
            '--cms-accent' => $colors['accent'],
            '--cms-success' => $colors['success'],
            '--cms-danger' => $colors['danger'],
            '--cms-focus' => $colors['focus'],
            '--cms-info' => $colors['info'] ?? $defaults['colors']['light']['info'],
            '--cms-warning' => $colors['warning'] ?? $defaults['colors']['light']['warning'],
            '--cms-font-latin' => self::fontStack((string) ($typography['latin'] ?? 'editorial')),
            '--cms-font-rtl' => self::fontStack((string) ($typography['rtl'] ?? 'academy')),
            '--cms-font-display-family' => self::fontStack((string) ($typography['latin'] ?? 'editorial')),
            '--cms-font-weight-regular' => (string) ($weights['regular'] ?? 400),
            '--cms-font-weight-medium' => (string) ($weights['medium'] ?? 500),
            '--cms-font-weight-semibold' => (string) ($weights['semibold'] ?? 600),
            '--cms-font-weight-bold' => (string) ($weights['bold'] ?? 700),
            '--cms-tracking-display' => ((float) ($tracking['display'] ?? -0.02)) . 'em',
            '--cms-tracking-body' => ((float) ($tracking['body'] ?? 0)) . 'em',
            '--cms-font-fluid-min' => ($fluid['min'] ?? 15) . 'px',
            '--cms-font-fluid-max' => ($fluid['max'] ?? 18) . 'px',
            '--cms-font-base' => $typography['base'] . 'px',
            '--cms-font-display' => $typography['display'] . 'px',
            '--cms-font-heading' => $typography['heading'] . 'px',
            '--cms-line-height' => (string) $typography['lineHeight'],
            '--cms-space-section' => $spacing['section'] . 'px',
            '--cms-space-card' => $spacing['card'] . 'px',
            '--cms-space-gap' => $spacing['gap'] . 'px',
            '--cms-container' => $layout['container'] . 'px',
            '--cms-container-wide' => ($layout['wide'] ?? 1440) . 'px',
            '--cms-container-narrow' => ($layout['narrow'] ?? 960) . 'px',
            '--cms-grid-gutter' => (($layout['grid']['gutter'] ?? $spacing['gap'])) . 'px',
            '--cms-reading' => $layout['reading'] . 'px',
            '--cms-radius-small' => $radius['small'] . 'px',
            '--cms-radius-medium' => $radius['medium'] . 'px',
            '--cms-radius-large' => $radius['large'] . 'px',
            '--cms-radius-xl' => ($radius['xl'] ?? 32) . 'px',
            '--cms-radius-pill' => ($radius['pill'] ?? 999) . 'px',
            '--cms-shadow-sm' => $shadows['sm'],
            '--cms-shadow-md' => $shadows['md'],
            '--cms-shadow-lg' => $shadows['lg'],
            '--cms-button-height' => $button['height'] . 'px',
            '--cms-button-padding-x' => ($button['paddingX'] ?? 28) . 'px',
            '--cms-button-weight' => (string) $button['weight'],
            '--cms-motion-duration' => $motion['duration'] . 'ms',
            '--cms-motion-distance' => $motion['distance'] . 'px',
            '--cms-rtl-font-scale' => (string) ($rtl['fontScale'] ?? 1),
            '--cms-rtl-letter-spacing' => ((float) ($rtl['letterSpacing'] ?? 0)) . 'em',
            '--cms-shadow' => match ($tokens['shadow']) {
                'crisp' => '0 1px 0 rgba(0,0,0,.18)',
                'none' => 'none',
                default => '0 18px 40px -28px rgba(23,43,38,.45)',
            },
        ];
        $scale = $spacing['scale'] ?? $defaults['spacing']['scale'];
        foreach ($scale as $index => $step) {
            $vars['--cms-space-' . $index] = (int) $step . 'px';
        }
        foreach ($gradients as $key => $gradient) {
            if (!in_array($key, ['dawn','midnight','sand','forest'], true)) {
                continue;
            }
            $vars['--cms-gradient-' . $key] = 'linear-gradient(' . (int) $gradient['angle'] . 'deg, '
                . $gradient['from'] . ', ' . $gradient['to'] . ')';
        }
        foreach ($motionPresets as $key => $preset) {
            if (!in_array($key, ['fade','rise','slide','scale'], true)) {
                continue;
            }
            $vars['--cms-motion-' . $key . '-duration'] = (int) $preset['duration'] . 'ms';
            $vars['--cms-motion-' . $key . '-distance'] = (int) $preset['distance'] . 'px';
        }

        $css = ':root{';
        foreach ($vars as $name => $value) {
            $css .= $name . ':' . $value . ';';
        }
        return $css . '}';
    }

    /** WCAG contrast ratio for a foreground/background hex pair. */
    public static function contrast(string $foreground, string $background): float
    {
        $luminance = static function (string $hex): float {
            $hex = ltrim($hex, '#');
            $channels = [];
            foreach ([0, 2, 4] as $offset) {
                $channel = hexdec(substr($hex, $offset, 2)) / 255;
                $channels[] = $channel <= 0.03928 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4;
            }
            return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
        };
        $a = $luminance($foreground);
        $b = $luminance($background);
        $light = max($a, $b);
        $dark = min($a, $b);
        return round(($light + 0.05) / ($dark + 0.05), 2);
    }
}
