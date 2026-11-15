<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-features cms-features--<?= $e($ctx['variant']) ?>">
    <?php if (!empty($p['heading'])): ?><?= Partial::heading($ctx['headingLevel'], $p['heading']) ?><?php endif; ?>
    <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    <ul class="cms-features__list">
        <?php foreach ($items as $item): ?>
            <li class="cms-features__item">
                <h3 class="cms-features__title"><?= $e($item['title'] ?? '') ?></h3>
                <?php if (!empty($item['body'])): ?><p><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
