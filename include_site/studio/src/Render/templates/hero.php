<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$image = isset($p['image']['id']) ? $ctx['media']($p['image']['id']) : null;
$alt = (string) ($p['image']['alt'] ?? '');
?>
<div class="cms-hero cms-hero--<?= $e($variant) ?>">
    <div class="cms-hero__body">
        <?php if (!empty($p['eyebrow'])): ?><p class="cms-eyebrow"><?= $e($p['eyebrow']) ?></p><?php endif; ?>
        <<?= $tag ?> class="cms-display"><?= $e($p['heading']) ?></<?= $tag ?>>
        <?php if (!empty($p['lead'])): ?><p class="cms-lead"><?= nl2br($e($p['lead'])) ?></p><?php endif; ?>
        <?php if (!empty($p['primaryCta']) || !empty($p['secondaryCta'])): ?>
            <div class="cms-actions">
                <?= !empty($p['primaryCta']) ? Partial::button($p['primaryCta'], 'primary') : '' ?>
                <?= !empty($p['secondaryCta']) ? Partial::button($p['secondaryCta'], 'ghost') : '' ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($variant !== 'centered'): ?>
        <div class="cms-hero__media"><?= Partial::figure($image, $alt, 'cms-hero__figure', true) ?></div>
    <?php elseif ($image !== null): ?>
        <div class="cms-hero__media cms-hero__media--wide"><?= Partial::figure($image, $alt, 'cms-hero__figure', true) ?></div>
    <?php endif; ?>
</div>
