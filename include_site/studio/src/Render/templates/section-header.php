<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
?>
<header class="cms-shead cms-shead--<?= $e($variant) ?>">
    <?php if (in_array($variant, ['eyebrow', 'numbered'], true) && !empty($p['eyebrow'])): ?><p class="cms-eyebrow"><?= $e($p['eyebrow']) ?></p><?php endif; ?>
    <div class="cms-shead__row">
        <div class="cms-shead__copy">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-shead__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['lead'])): ?><p class="cms-shead__lead"><?= nl2br($e($p['lead'])) ?></p><?php endif; ?>
        </div>
        <?php if (!empty($p['cta']['label']) && in_array($variant, ['split', 'actions'], true)): ?>
            <div class="cms-shead__action"><?= Partial::button($p['cta'], 'ghost') ?></div>
        <?php endif; ?>
        <?php if (!empty($p['meta']) && $variant === 'rule'): ?><span class="cms-shead__meta"><?= $e($p['meta']) ?></span><?php endif; ?>
    </div>
</header>
