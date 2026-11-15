<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
$stars = static fn (string $rating): string => str_repeat('★', max(1, min(5, (int) $rating))) . str_repeat('☆', 5 - max(1, min(5, (int) $rating)));
?>
<div class="cms-reviews cms-reviews--<?= $e($variant) ?>">
    <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-reviews__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>

    <?php if ($variant === 'summary'): ?>
        <div class="cms-reviews__summary">
            <div class="cms-reviews__score">
                <span class="cms-reviews__average"><?= $e($p['average'] ?? '') ?></span>
                <span class="cms-reviews__stars" aria-label="<?= $e($p['average'] ?? '') ?> out of 5"><?= $stars((string) round((float) ($p['average'] ?? 5))) ?></span>
                <span class="cms-reviews__count"><?= $e($p['count'] ?? '') ?></span>
            </div>
            <ul class="cms-reviews__quotes">
                <?php foreach (array_slice($items, 0, 2) as $item): ?>
                    <li>
                        <blockquote class="cms-reviews__quote"><?= $e($item['quote'] ?? '') ?></blockquote>
                        <p class="cms-reviews__meta"><?= $e($item['name'] ?? '') ?><?= !empty($item['role']) ? ' · ' . $e($item['role']) : '' ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <ul class="cms-reviews__list">
            <?php foreach ($items as $item): ?>
                <li class="cms-reviews__card">
                    <p class="cms-reviews__stars" aria-label="<?= $e($item['rating'] ?? '5') ?> out of 5"><?= $stars((string) ($item['rating'] ?? '5')) ?></p>
                    <blockquote class="cms-reviews__quote"><?= $e($item['quote'] ?? '') ?></blockquote>
                    <p class="cms-reviews__meta"><?= $e($item['name'] ?? '') ?><?= !empty($item['role']) ? ' · ' . $e($item['role']) : '' ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
