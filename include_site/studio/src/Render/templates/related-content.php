<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-related cms-related--<?= $e($variant) ?>">
    <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-related__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
    <ul class="cms-related__list">
        <?php foreach ($items as $item): ?>
            <?php $image = isset($item['image']['id']) ? $ctx['media']($item['image']['id']) : null; ?>
            <li class="cms-related__item">
                <?php if ($variant === 'cards'): ?>
                    <div class="cms-related__media"><?= Partial::figure($image, (string) ($item['image']['alt'] ?? ($item['title'] ?? '')), 'cms-related__figure') ?></div>
                <?php endif; ?>
                <div class="cms-related__body">
                    <?php if (!empty($item['meta'])): ?><p class="cms-eyebrow"><?= $e($item['meta']) ?></p><?php endif; ?>
                    <h3 class="cms-related__name"><a href="<?= $e($item['href'] ?? '#') ?>"><?= $e($item['title'] ?? '') ?></a></h3>
                    <?php if (!empty($item['summary']) && $variant !== 'compact'): ?><p class="cms-related__copy"><?= nl2br($e($item['summary'])) ?></p><?php endif; ?>
                </div>
                <?php if ($variant === 'list'): ?><span class="cms-related__arrow" aria-hidden="true">→</span><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
