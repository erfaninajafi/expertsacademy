<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$uid = 'tabs-' . $ctx['uid'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-tabs cms-tabs--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-tabs__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-tabs__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>

    <div class="cms-tabs__control">
        <?php foreach ($items as $index => $item): ?>
            <input class="cms-tabs__input" type="radio" name="<?= $e($uid) ?>" id="<?= $e($uid . '-' . $index) ?>" <?= $index === 0 ? 'checked' : '' ?>>
        <?php endforeach; ?>
        <div class="cms-tabs__nav" role="group" aria-label="<?= $e($p['heading'] ?? 'Tabs') ?>">
            <?php foreach ($items as $index => $item): ?>
                <label class="cms-tabs__label" for="<?= $e($uid . '-' . $index) ?>"><?= $e($item['label'] ?? '') ?></label>
            <?php endforeach; ?>
        </div>
        <div class="cms-tabs__panels">
            <?php foreach ($items as $index => $item): ?>
                <section class="cms-tabs__panel" aria-label="<?= $e($item['label'] ?? '') ?>">
                    <h3 class="cms-tabs__panel-title"><?= $e($item['title'] ?? '') ?></h3>
                    <div class="cms-richtext"><?= $ctx['rich']((string) ($item['body'] ?? '')) ?></div>
                    <?php if (!empty($item['cta']['label'])): ?>
                        <div class="cms-actions"><?= Partial::button($item['cta']) ?></div>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</div>
