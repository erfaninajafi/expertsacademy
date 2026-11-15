<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-process cms-process--<?= $e($variant) ?>">
    <header class="cms-process__head">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-process__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </header>
    <ol class="cms-process__list">
        <?php foreach ($items as $index => $item): ?>
            <li class="cms-process__step">
                <span class="cms-process__number" aria-hidden="true"><?= $e(sprintf('%02d', $index + 1)) ?></span>
                <div class="cms-process__body">
                    <div class="cms-process__row">
                        <h3 class="cms-process__name"><?= $e($item['title'] ?? '') ?></h3>
                        <?php if (!empty($item['duration'])): ?><span class="cms-process__duration"><?= $e($item['duration']) ?></span><?php endif; ?>
                    </div>
                    <?php if (!empty($item['body'])): ?><p class="cms-process__copy"><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</div>
