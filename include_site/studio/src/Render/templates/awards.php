<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-awards cms-awards--<?= $e($variant) ?>">
    <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-awards__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
    <ul class="cms-awards__list">
        <?php foreach ($items as $item): ?>
            <li class="cms-awards__item">
                <span class="cms-awards__year"><?= $e($item['year'] ?? '') ?></span>
                <div class="cms-awards__body">
                    <h3 class="cms-awards__name"><?= $e($item['title'] ?? '') ?></h3>
                    <?php if (!empty($item['issuer'])): ?><p class="cms-awards__issuer"><?= $e($item['issuer']) ?></p><?php endif; ?>
                    <?php if (!empty($item['note'])): ?><p class="cms-awards__note"><?= $e($item['note']) ?></p><?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
