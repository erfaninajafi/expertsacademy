<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
$tiers = ['strategic' => 'Strategic', 'programme' => 'Programme', 'community' => 'Community'];
$grouped = [];
foreach ($items as $item) {
    $grouped[(string) ($item['tier'] ?? 'community')][] = $item;
}
?>
<div class="cms-partners cms-partners--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-partners__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-partners__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>

    <?php if ($variant === 'tiers'): ?>
        <div class="cms-partners__tiers">
            <?php foreach ($grouped as $tier => $partners): ?>
                <section class="cms-partners__tier">
                    <h3 class="cms-partners__tier-title"><?= $e($tiers[$tier] ?? $tier) ?></h3>
                    <ul>
                        <?php foreach ($partners as $partner): ?>
                            <li><strong><?= $e($partner['name'] ?? '') ?></strong><span><?= $e($partner['description'] ?? '') ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <ul class="cms-partners__list">
            <?php foreach ($items as $partner): ?>
                <?php $image = isset($partner['image']['id']) ? $ctx['media']($partner['image']['id']) : null; ?>
                <li class="cms-partners__item">
                    <?php if ($image !== null): ?><span class="cms-partners__logo"><?= Partial::figure($image, (string) ($partner['image']['alt'] ?? ($partner['name'] ?? '')), 'cms-partners__figure') ?></span><?php endif; ?>
                    <span class="cms-partners__name"><?= $e($partner['name'] ?? '') ?></span>
                    <?php if ($variant !== 'strip' && !empty($partner['description'])): ?><span class="cms-partners__copy"><?= $e($partner['description']) ?></span><?php endif; ?>
                    <span class="cms-partners__badge"><?= $e($tiers[$partner['tier'] ?? 'community'] ?? '') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
