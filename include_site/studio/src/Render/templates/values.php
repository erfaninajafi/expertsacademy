<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-values cms-values--<?= $e($variant) ?>">
    <header class="cms-values__head">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-values__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </header>
    <ul class="cms-values__list">
        <?php foreach ($items as $index => $item): ?>
            <li class="cms-values__item">
                <span class="cms-values__marker" aria-hidden="true"><?= $e($item['marker'] ?: sprintf('%02d', $index + 1)) ?></span>
                <div>
                    <h3 class="cms-values__name"><?= $e($item['title'] ?? '') ?></h3>
                    <?php if (!empty($item['body'])): ?><p class="cms-values__copy"><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
