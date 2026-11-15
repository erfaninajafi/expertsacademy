<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$image = isset($p['image']['id']) ? $ctx['media']($p['image']['id']) : null;
$alt = (string) ($p['image']['alt'] ?? '');
$stats = is_array($p['stats'] ?? null) ? $p['stats'] : [];
$primary = !empty($p['primaryCta']) ? Partial::button($p['primaryCta'], 'primary') : '';
$secondary = !empty($p['secondaryCta']) ? Partial::button($p['secondaryCta'], 'ghost') : '';
?>
<div class="cms-hcin cms-hcin--<?= $e($variant) ?>">
    <?php if ($variant === 'overlay'): ?>
        <div class="cms-hcin__media" aria-hidden="<?= $image === null ? 'true' : 'false' ?>">
            <?= Partial::figure($image, $alt, 'cms-hcin__figure', true) ?>
            <span class="cms-hcin__veil"></span>
        </div>
        <div class="cms-hcin__content">
            <?php if (!empty($p['badge'])): ?><p class="cms-hcin__badge"><?= $e($p['badge']) ?></p><?php endif; ?>
            <?php if (!empty($p['eyebrow'])): ?><p class="cms-eyebrow"><?= $e($p['eyebrow']) ?></p><?php endif; ?>
            <<?= $tag ?> class="cms-display"><?= $e($p['heading']) ?></<?= $tag ?>>
            <?php if (!empty($p['lead'])): ?><p class="cms-lead"><?= nl2br($e($p['lead'])) ?></p><?php endif; ?>
            <?php if ($primary !== '' || $secondary !== ''): ?>
                <div class="cms-actions"><?= $primary ?><?= $secondary ?></div>
            <?php endif; ?>
        </div>
        <?php if ($stats !== []): ?>
            <dl class="cms-hcin__rail">
                <?php foreach ($stats as $item): ?>
                    <div class="cms-hcin__stat">
                        <dt><?= $e($item['value'] ?? '') ?></dt>
                        <dd><?= $e($item['label'] ?? '') ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        <?php endif; ?>
    <?php elseif ($variant === 'split'): ?>
        <div class="cms-hcin__split">
            <div class="cms-hcin__body">
                <?php if (!empty($p['eyebrow'])): ?><p class="cms-eyebrow"><?= $e($p['eyebrow']) ?></p><?php endif; ?>
                <<?= $tag ?> class="cms-display"><?= $e($p['heading']) ?></<?= $tag ?>>
                <?php if (!empty($p['lead'])): ?><p class="cms-lead"><?= nl2br($e($p['lead'])) ?></p><?php endif; ?>
                <?php if ($primary !== '' || $secondary !== ''): ?>
                    <div class="cms-actions"><?= $primary ?><?= $secondary ?></div>
                <?php endif; ?>
                <?php if (!empty($p['badge'])): ?><p class="cms-hcin__note"><?= $e($p['badge']) ?></p><?php endif; ?>
            </div>
            <div class="cms-hcin__visual">
                <?= Partial::figure($image, $alt, 'cms-hcin__figure', true) ?>
                <?php if ($stats !== []): ?>
                    <dl class="cms-hcin__card">
                        <?php foreach ($stats as $item): ?>
                            <div>
                                <dt><?= $e($item['value'] ?? '') ?></dt>
                                <dd><?= $e($item['label'] ?? '') ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="cms-hcin__minimal">
            <?php if (!empty($p['eyebrow'])): ?><p class="cms-eyebrow"><?= $e($p['eyebrow']) ?></p><?php endif; ?>
            <<?= $tag ?> class="cms-hcin__statement"><?= $e($p['heading']) ?></<?= $tag ?>>
            <?php if (!empty($p['lead'])): ?><p class="cms-hcin__sub"><?= nl2br($e($p['lead'])) ?></p><?php endif; ?>
            <?php if ($primary !== '' || $secondary !== ''): ?>
                <div class="cms-actions"><?= $primary ?><?= $secondary ?></div>
            <?php endif; ?>
            <?php if ($stats !== []): ?>
                <dl class="cms-hcin__inline">
                    <?php foreach ($stats as $item): ?>
                        <div><dt><?= $e($item['value'] ?? '') ?></dt><dd><?= $e($item['label'] ?? '') ?></dd></div>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
