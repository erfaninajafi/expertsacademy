<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-resources cms-resources--<?= $e($variant) ?>">
    <header class="cms-resources__head">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-resources__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </header>
    <ul class="cms-resources__list">
        <?php foreach ($items as $item): ?>
            <?php $image = isset($item['image']['id']) ? $ctx['media']($item['image']['id']) : null; ?>
            <li class="cms-resources__item">
                <?php if ($variant === 'cards'): ?>
                    <div class="cms-resources__media"><?= Partial::figure($image, (string) ($item['image']['alt'] ?? ($item['title'] ?? '')), 'cms-resources__figure') ?></div>
                <?php endif; ?>
                <div class="cms-resources__body">
                    <?php if (!empty($item['kind'])): ?><p class="cms-eyebrow"><?= $e($item['kind']) ?></p><?php endif; ?>
                    <h3 class="cms-resources__name"><a href="<?= $e($item['href'] ?? '#') ?>"><?= $e($item['title'] ?? '') ?></a></h3>
                    <?php if (!empty($item['summary']) && $variant !== 'rows'): ?><p class="cms-resources__copy"><?= $e($item['summary']) ?></p><?php endif; ?>
                </div>
                <?php if ($variant === 'rows'): ?><span class="cms-resources__arrow" aria-hidden="true">→</span><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
