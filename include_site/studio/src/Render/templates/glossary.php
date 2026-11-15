<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-glossary cms-glossary--<?= $e($variant) ?>">
    <header class="cms-glossary__head">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-glossary__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </header>
    <dl class="cms-glossary__list">
        <?php foreach ($items as $item): ?>
            <div class="cms-glossary__item">
                <dt><?= $e($item['term'] ?? '') ?></dt>
                <dd><?= nl2br($e($item['definition'] ?? '')) ?></dd>
            </div>
        <?php endforeach; ?>
    </dl>
</div>
