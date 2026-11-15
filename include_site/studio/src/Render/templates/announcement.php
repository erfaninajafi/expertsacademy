<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
?>
<div class="cms-announce cms-announce--<?= $e($variant) ?>" role="status">
    <div class="cms-announce__copy">
        <?php if (!empty($p['eyebrow'])): ?><p class="cms-announce__eyebrow"><?= $e($p['eyebrow']) ?></p><?php endif; ?>
        <p class="cms-announce__message"><?= $e($p['message'] ?? '') ?></p>
        <?php if (!empty($p['detail'])): ?><p class="cms-announce__detail"><?= nl2br($e($p['detail'])) ?></p><?php endif; ?>
    </div>
    <?php if (!empty($p['cta']['label'])): ?>
        <div class="cms-announce__action"><?= Partial::button($p['cta'], $variant === 'bar' ? 'primary' : 'primary') ?></div>
    <?php endif; ?>
</div>
