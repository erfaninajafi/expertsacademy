<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$links = is_array($p['links'] ?? null) ? $p['links'] : [];
$logo = isset($p['logo']['id']) ? $ctx['media']($p['logo']['id']) : null;
?>
<nav class="cms-nav cms-nav--<?= $e($variant) ?>" aria-label="<?= $e($p['brand'] ?? 'Primary') ?>">
    <?php if ($variant === 'centered'): ?>
        <div class="cms-nav__brand cms-nav__brand--centered">
            <?php if ($logo !== null): ?><?= Partial::figure($logo, (string) ($p['logo']['alt'] ?? ''), 'cms-nav__logo', true) ?><?php endif; ?>
            <span class="cms-nav__name"><?= $e($p['brand'] ?? '') ?></span>
            <?php if (!empty($p['tagline'])): ?><span class="cms-nav__tagline"><?= $e($p['tagline']) ?></span><?php endif; ?>
        </div>
        <div class="cms-nav__row">
            <ul class="cms-nav__links">
                <?php foreach ($links as $link): ?>
                    <li><a href="<?= $e($link['href'] ?? '#') ?>"><?= $e($link['label'] ?? '') ?></a></li>
                <?php endforeach; ?>
            </ul>
            <?php if (!empty($p['primaryCta']['label'])): ?><span class="cms-nav__cta"><?= Partial::button($p['primaryCta']) ?></span><?php endif; ?>
        </div>
    <?php elseif ($variant === 'split'): ?>
        <ul class="cms-nav__links cms-nav__links--start">
            <?php foreach ($links as $link): ?>
                <li><a href="<?= $e($link['href'] ?? '#') ?>"><?= $e($link['label'] ?? '') ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="cms-nav__brand">
            <?php if ($logo !== null): ?><?= Partial::figure($logo, (string) ($p['logo']['alt'] ?? ''), 'cms-nav__logo', true) ?><?php endif; ?>
            <span class="cms-nav__name"><?= $e($p['brand'] ?? '') ?></span>
        </div>
        <div class="cms-nav__actions">
            <?php if (!empty($p['secondaryCta']['label'])): ?><?= Partial::button($p['secondaryCta'], 'ghost') ?><?php endif; ?>
            <?php if (!empty($p['primaryCta']['label'])): ?><?= Partial::button($p['primaryCta']) ?><?php endif; ?>
        </div>
    <?php elseif ($variant === 'minimal'): ?>
        <div class="cms-nav__brand">
            <span class="cms-nav__mark" aria-hidden="true">✦</span>
            <span class="cms-nav__name"><?= $e($p['brand'] ?? '') ?></span>
        </div>
        <div class="cms-nav__actions">
            <?php foreach (array_slice($links, 0, 2) as $link): ?>
                <a class="cms-nav__quiet" href="<?= $e($link['href'] ?? '#') ?>"><?= $e($link['label'] ?? '') ?></a>
            <?php endforeach; ?>
            <?php if (!empty($p['primaryCta']['label'])): ?><?= Partial::button($p['primaryCta']) ?><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="cms-nav__brand">
            <?php if ($logo !== null): ?><?= Partial::figure($logo, (string) ($p['logo']['alt'] ?? ''), 'cms-nav__logo', true) ?><?php endif; ?>
            <span class="cms-nav__name"><?= $e($p['brand'] ?? '') ?></span>
        </div>
        <ul class="cms-nav__links">
            <?php foreach ($links as $link): ?>
                <li><a href="<?= $e($link['href'] ?? '#') ?>"><?= $e($link['label'] ?? '') ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="cms-nav__actions">
            <?php if (!empty($p['secondaryCta']['label'])): ?><a class="cms-nav__quiet" href="<?= $e($p['secondaryCta']['href'] ?? '#') ?>"><?= $e($p['secondaryCta']['label']) ?></a><?php endif; ?>
            <?php if (!empty($p['primaryCta']['label'])): ?><?= Partial::button($p['primaryCta']) ?><?php endif; ?>
        </div>
    <?php endif; ?>
</nav>
