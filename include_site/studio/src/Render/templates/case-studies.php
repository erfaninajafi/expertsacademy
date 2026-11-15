<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
$figure = static function (array $item) use ($ctx): string {
    if (!isset($item['image']['id'])) {
        return '';
    }
    $image = $ctx['media']($item['image']['id']);
    return Partial::figure($image, (string) ($item['image']['alt'] ?? ($item['title'] ?? '')), 'cms-cases__figure');
};
?>
<div class="cms-cases cms-cases--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-cases__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-cases__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>

    <?php if ($variant === 'featured' && !empty($items[0])): ?>
        <?php $lead = $items[0]; ?>
        <article class="cms-cases__lead">
            <div class="cms-cases__lead-media"><?= $figure($lead) ?></div>
            <div class="cms-cases__lead-copy">
                <p class="cms-eyebrow"><?= $e($lead['client'] ?? '') ?><?= !empty($lead['meta']) ? ' · ' . $e($lead['meta']) : '' ?></p>
                <h3 class="cms-cases__name"><?php if (!empty($lead['href'])): ?><a href="<?= $e($lead['href']) ?>"><?= $e($lead['title'] ?? '') ?></a><?php else: ?><?= $e($lead['title'] ?? '') ?><?php endif; ?></h3>
                <?php if (!empty($lead['summary'])): ?><p class="cms-cases__copy"><?= nl2br($e($lead['summary'])) ?></p><?php endif; ?>
                <?php if (!empty($lead['result'])): ?><p class="cms-cases__result"><?= $e($lead['result']) ?></p><?php endif; ?>
            </div>
        </article>
        <ul class="cms-cases__stack">
            <?php foreach (array_slice($items, 1) as $item): ?>
                <li class="cms-cases__stack-item">
                    <div>
                        <p class="cms-eyebrow"><?= $e($item['client'] ?? '') ?></p>
                        <h3 class="cms-cases__name"><?php if (!empty($item['href'])): ?><a href="<?= $e($item['href']) ?>"><?= $e($item['title'] ?? '') ?></a><?php else: ?><?= $e($item['title'] ?? '') ?><?php endif; ?></h3>
                    </div>
                    <span class="cms-cases__result"><?= $e($item['result'] ?? '') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <ul class="cms-cases__list">
            <?php foreach ($items as $item): ?>
                <li class="cms-cases__item">
                    <?php if ($variant === 'grid'): ?>
                        <div class="cms-cases__media"><?= $figure($item) ?></div>
                    <?php endif; ?>
                    <div class="cms-cases__body">
                        <p class="cms-eyebrow"><?= $e($item['client'] ?? '') ?><?= !empty($item['meta']) ? ' · ' . $e($item['meta']) : '' ?></p>
                        <h3 class="cms-cases__name"><?php if (!empty($item['href'])): ?><a href="<?= $e($item['href']) ?>"><?= $e($item['title'] ?? '') ?></a><?php else: ?><?= $e($item['title'] ?? '') ?><?php endif; ?></h3>
                        <?php if (!empty($item['summary'])): ?><p class="cms-cases__copy"><?= nl2br($e($item['summary'])) ?></p><?php endif; ?>
                        <?php if (!empty($item['result'])): ?><p class="cms-cases__result"><?= $e($item['result']) ?></p><?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
