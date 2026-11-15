<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<nav class="cms-crumbs cms-crumbs--<?= $e($variant) ?>" aria-label="Breadcrumb">
    <?php if ($variant === 'eyebrow' && !empty($p['current'])): ?>
        <p class="cms-crumbs__current"><?= $e($p['current']) ?></p>
    <?php endif; ?>
    <ol class="cms-crumbs__list">
        <?php foreach ($items as $index => $item): ?>
            <li class="cms-crumbs__item">
                <a href="<?= $e($item['href'] ?? '#') ?>"><?= $e($item['label'] ?? '') ?></a>
            </li>
        <?php endforeach; ?>
        <?php if ($variant === 'inline' && !empty($p['current'])): ?>
            <li class="cms-crumbs__item cms-crumbs__item--current" aria-current="page"><?= $e($p['current']) ?></li>
        <?php endif; ?>
    </ol>
</nav>
