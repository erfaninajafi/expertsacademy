<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-timeline cms-timeline--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-timeline__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-timeline__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>
    <ol class="cms-timeline__list">
        <?php foreach ($items as $index => $item): ?>
            <li class="cms-timeline__item">
                <div class="cms-timeline__marker" aria-hidden="true"><?= $e($item['marker'] ?? sprintf('%02d', $index + 1)) ?></div>
                <div class="cms-timeline__card">
                    <?php if (!empty($item['meta'])): ?><p class="cms-eyebrow"><?= $e($item['meta']) ?></p><?php endif; ?>
                    <h3 class="cms-timeline__heading"><?= $e($item['title'] ?? '') ?></h3>
                    <?php if (!empty($item['body'])): ?><p class="cms-timeline__copy"><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</div>
