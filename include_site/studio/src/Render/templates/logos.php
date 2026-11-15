<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-logos cms-logos--<?= $e($ctx['variant']) ?>">
    <?php if (!empty($p['heading'])): ?><?= Partial::heading($ctx['headingLevel'], $p['heading']) ?><?php endif; ?>
    <ul class="cms-logos__list" aria-label="Partner organisations">
        <?php foreach ($items as $item):
            $image = isset($item['image']['id']) ? $ctx['media']($item['image']['id']) : null; ?>
            <li class="cms-logos__item">
                <?php if ($image !== null): ?>
                    <?= Partial::figure($image, (string) ($item['name'] ?? ''), 'cms-logos__figure') ?>
                <?php else: ?>
                    <span class="cms-logos__name"><?= $e($item['name'] ?? '') ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
