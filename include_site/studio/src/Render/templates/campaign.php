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
<div class="cms-campaign cms-campaign--<?= $e($variant) ?>">
    <?php if ($image !== null): ?>
        <div class="cms-campaign__media"><?= Partial::figure($image, $alt, 'cms-campaign__figure', true) ?></div>
    <?php endif; ?>
    <div class="cms-campaign__copy">
        <?php if (!empty($p['eyebrow'])): ?><p class="cms-eyebrow"><?= $e($p['eyebrow']) ?></p><?php endif; ?>
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-campaign__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['lead'])): ?><p class="cms-campaign__lead"><?= nl2br($e($p['lead'])) ?></p><?php endif; ?>
        <?php if (!empty($p['proof'])): ?><p class="cms-campaign__proof"><?= $e($p['proof']) ?></p><?php endif; ?>
        <?php if (!empty($p['cta']['label'])): ?><div class="cms-actions"><?= Partial::button($p['cta']) ?></div><?php endif; ?>
    </div>
</div>
