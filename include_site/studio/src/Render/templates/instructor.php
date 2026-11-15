<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$image = isset($p['image']['id']) ? $ctx['media']($p['image']['id']) : null;
$alt = (string) ($p['image']['alt'] ?? '');
$credentials = is_array($p['credentials'] ?? null) ? $p['credentials'] : [];
?>
<div class="cms-instructor cms-instructor--<?= $e($variant) ?>">
    <div class="cms-instructor__portrait">
        <?= Partial::figure($image, $alt !== '' ? $alt : (string) ($p['name'] ?? ''), 'cms-instructor__figure') ?>
    </div>
    <div class="cms-instructor__body">
        <p class="cms-eyebrow">Faculty</p>
        <h2 class="cms-instructor__name"><?= $e($p['name'] ?? '') ?></h2>
        <?php if (!empty($p['role'])): ?><p class="cms-instructor__role"><?= $e($p['role']) ?></p><?php endif; ?>
        <?php if (!empty($p['bio'])): ?><div class="cms-richtext"><?= $ctx['rich']((string) $p['bio']) ?></div><?php endif; ?>

        <?php if ($variant === 'credentials' && $credentials): ?>
            <ul class="cms-instructor__credentials">
                <?php foreach ($credentials as $credential): ?><li><?= $e($credential['label'] ?? '') ?></li><?php endforeach; ?>
            </ul>
        <?php elseif ($variant === 'interview' && !empty($p['quote'])): ?>
            <blockquote class="cms-instructor__quote"><?= $e($p['quote']) ?></blockquote>
        <?php endif; ?>

        <?php if ($variant === 'split' && $credentials): ?>
            <ul class="cms-instructor__tags"><?php foreach ($credentials as $credential): ?><li><?= $e($credential['label'] ?? '') ?></li><?php endforeach; ?></ul>
        <?php endif; ?>

        <?php if (!empty($p['cta']['label'])): ?><div class="cms-actions"><?= Partial::button($p['cta'], 'ghost') ?></div><?php endif; ?>
    </div>
</div>
