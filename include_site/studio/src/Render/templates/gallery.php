<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-gallery cms-gallery--<?= $e($ctx['variant']) ?>">
    <?php if (!empty($p['heading'])): ?><?= Partial::heading($ctx['headingLevel'], $p['heading']) ?><?php endif; ?>
    <ul class="cms-gallery__list">
        <?php foreach ($items as $item):
            $image = isset($item['image']['id']) ? $ctx['media']($item['image']['id']) : null;
            $alt = (string) ($item['image']['alt'] ?? ''); ?>
            <li class="cms-gallery__item">
                <?= Partial::figure($image, $alt, 'cms-gallery__figure') ?>
                <?php if (!empty($item['caption'])): ?><span class="cms-gallery__caption"><?= $e($item['caption']) ?></span><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
