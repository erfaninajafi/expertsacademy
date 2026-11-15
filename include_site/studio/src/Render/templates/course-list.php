<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-courses cms-courses--<?= $e($ctx['variant']) ?>">
    <?php if (!empty($p['heading'])): ?><?= Partial::heading($ctx['headingLevel'], $p['heading']) ?><?php endif; ?>
    <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    <ul class="cms-courses__list">
        <?php foreach ($items as $item):
            $image = isset($item['image']['id']) ? $ctx['media']($item['image']['id']) : null;
            $alt = (string) ($item['image']['alt'] ?? '');
            $href = (string) ($item['href'] ?? ''); ?>
            <li class="cms-courses__item">
                <?php if ($href !== ''): ?><a class="cms-courses__link" href="<?= $e($href) ?>"><?php endif; ?>
                <?= Partial::figure($image, $alt, 'cms-courses__figure') ?>
                <div class="cms-courses__body">
                    <?php if (!empty($item['meta'])): ?><p class="cms-eyebrow"><?= $e($item['meta']) ?></p><?php endif; ?>
                    <h3 class="cms-courses__title"><?= $e($item['title'] ?? '') ?></h3>
                    <?php if (!empty($item['summary'])): ?><p><?= nl2br($e($item['summary'])) ?></p><?php endif; ?>
                </div>
                <?php if ($href !== ''): ?></a><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
