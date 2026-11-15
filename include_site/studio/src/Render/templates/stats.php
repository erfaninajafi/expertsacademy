<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-stats cms-stats--<?= $e($ctx['variant']) ?>">
    <?php if (!empty($p['heading'])): ?><?= Partial::heading($ctx['headingLevel'], $p['heading']) ?><?php endif; ?>
    <dl class="cms-stats__list">
        <?php foreach ($items as $item): ?>
            <div class="cms-stats__item">
                <dt class="cms-stats__value"><?= $e($item['value'] ?? '') ?></dt>
                <dd class="cms-stats__label"><?= $e($item['label'] ?? '') ?></dd>
            </div>
        <?php endforeach; ?>
    </dl>
</div>
