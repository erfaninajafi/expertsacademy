<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-social cms-social--<?= $e($variant) ?>">
    <?php if (!empty($p['heading'])): ?><p class="cms-social__heading"><?= $e($p['heading']) ?></p><?php endif; ?>
    <ul class="cms-social__list">
        <?php foreach ($items as $item): ?>
            <li>
                <a href="<?= $e($item['href'] ?? '#') ?>" rel="noopener">
                    <span class="cms-social__label"><?= $e($item['label'] ?? '') ?></span>
                    <?php if (!empty($item['handle'])): ?><span class="cms-social__handle"><?= $e($item['handle']) ?></span><?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
