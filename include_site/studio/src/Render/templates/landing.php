<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$uid = 'land-' . $ctx['uid'];
$image = isset($p['image']['id']) ? $ctx['media']($p['image']['id']) : null;
$alt = (string) ($p['image']['alt'] ?? '');
$bullets = is_array($p['bullets'] ?? null) ? $p['bullets'] : [];
?>
<div class="cms-landing cms-landing--<?= $e($variant) ?>">
    <div class="cms-landing__copy">
        <?php if (!empty($p['eyebrow'])): ?><p class="cms-eyebrow"><?= $e($p['eyebrow']) ?></p><?php endif; ?>
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-landing__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['lead'])): ?><p class="cms-landing__lead"><?= nl2br($e($p['lead'])) ?></p><?php endif; ?>
        <?php if ($bullets): ?><ul class="cms-landing__bullets"><?php foreach ($bullets as $bullet): ?><li><?= $e($bullet['text'] ?? '') ?></li><?php endforeach; ?></ul><?php endif; ?>
        <?php if (!empty($p['primaryCta']['label']) && $variant !== 'waitlist'): ?>
            <div class="cms-actions">
                <?= Partial::button($p['primaryCta']) ?>
                <?php if (!empty($p['secondaryCta']['label'])): ?><?= Partial::button($p['secondaryCta'], 'ghost') ?><?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (in_array($variant, ['waitlist', 'form-first'], true)): ?>
        <form class="cms-landing__form" method="post" action="">
            <label class="sr-only" for="<?= $e($uid) ?>-email">Email address</label>
            <input id="<?= $e($uid) ?>-email" name="email" type="email" autocomplete="email" required placeholder="you@example.com">
            <button class="cms-button cms-button--primary" type="submit"><?= $e($p['primaryCta']['label'] ?? 'Join the list') ?></button>
        </form>
    <?php elseif ($variant === 'split-form'): ?>
        <form class="cms-landing__card" method="post" action="">
            <p class="cms-landing__card-title"><?= $e($p['secondaryCta']['label'] ?? 'Start an application') ?></p>
            <label for="<?= $e($uid) ?>-name">Name<input id="<?= $e($uid) ?>-name" name="name" type="text" required></label>
            <label for="<?= $e($uid) ?>-mail">Email<input id="<?= $e($uid) ?>-mail" name="email" type="email" required></label>
            <button class="cms-button cms-button--primary" type="submit"><?= $e($p['primaryCta']['label'] ?? 'Apply') ?></button>
        </form>
    <?php else: ?>
        <div class="cms-landing__media">
            <?= Partial::figure($image, $alt !== '' ? $alt : (string) ($p['heading'] ?? ''), 'cms-landing__figure', true) ?>
            <?php if ($variant === 'event'): ?>
                <span class="cms-landing__badge"><?= $e($p['eyebrow'] ?: 'Next cohort') ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
