<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-certs cms-certs--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-certs__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-certs__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>
    <ul class="cms-certs__list">
        <?php foreach ($items as $item): ?>
            <?php $image = isset($item['image']['id']) ? $ctx['media']($item['image']['id']) : null; ?>
            <li class="cms-certs__item">
                <?php if ($image !== null): ?>
                    <span class="cms-certs__mark"><?= Partial::figure($image, (string) ($item['image']['alt'] ?? ($item['title'] ?? '')), 'cms-certs__figure') ?></span>
                <?php else: ?>
                    <span class="cms-certs__seal" aria-hidden="true">✦</span>
                <?php endif; ?>
                <div class="cms-certs__body">
                    <h3 class="cms-certs__name"><?= $e($item['title'] ?? '') ?></h3>
                    <?php if (!empty($item['issuer'])): ?><p class="cms-certs__issuer"><?= $e($item['issuer']) ?></p><?php endif; ?>
                </div>
                <?php if (!empty($item['year'])): ?><span class="cms-certs__year"><?= $e($item['year']) ?></span><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
