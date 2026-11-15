<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-checklist cms-checklist--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-checklist__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-checklist__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>
    <ul class="cms-checklist__list">
        <?php foreach ($items as $item): ?>
            <li class="cms-checklist__item <?= ($item['done'] ?? 'yes') === 'no' ? 'is-missing' : 'is-done' ?>">
                <span class="cms-checklist__mark" aria-hidden="true"><?= ($item['done'] ?? 'yes') === 'no' ? '–' : '✓' ?></span>
                <span class="cms-checklist__body">
                    <span class="cms-checklist__text"><?= $e($item['text'] ?? '') ?></span>
                    <?php if (!empty($item['detail'])): ?><span class="cms-checklist__detail"><?= $e($item['detail']) ?></span><?php endif; ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
