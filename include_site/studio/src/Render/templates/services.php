<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-services cms-services--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-services__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-services__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>

    <ul class="cms-services__list">
        <?php foreach ($items as $index => $item): ?>
            <li class="cms-services__item">
                <?php if ($variant === 'numbered'): ?>
                    <span class="cms-services__number" aria-hidden="true"><?= $e(sprintf('%02d', $index + 1)) ?></span>
                <?php endif; ?>
                <div class="cms-services__body">
                    <?php if (!empty($item['meta'])): ?><p class="cms-eyebrow"><?= $e($item['meta']) ?></p><?php endif; ?>
                    <h3 class="cms-services__name">
                        <?php if (!empty($item['href'])): ?><a href="<?= $e($item['href']) ?>"><?= $e($item['title'] ?? '') ?></a><?php else: ?><?= $e($item['title'] ?? '') ?><?php endif; ?>
                    </h3>
                    <?php if (!empty($item['body'])): ?><p class="cms-services__copy"><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
                </div>
                <?php if (!empty($item['href'])): ?><span class="cms-services__arrow" aria-hidden="true">→</span><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
