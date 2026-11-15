<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$rich = $ctx['rich'];
$image = isset($p['image']['id']) ? $ctx['media']($p['image']['id']) : null;
$alt = (string) ($p['image']['alt'] ?? '');
?>
<div class="cms-media-text cms-media-text--<?= $e($ctx['variant']) ?>">
    <div class="cms-media-text__media"><?= Partial::figure($image, $alt, 'cms-media-text__figure') ?></div>
    <div class="cms-media-text__body">
        <?= Partial::heading($ctx['headingLevel'], (string) $p['heading']) ?>
        <?php if (!empty($p['body'])): ?><div class="cms-richtext"><?= $rich((string) $p['body']) ?></div><?php endif; ?>
        <?php if (!empty($p['cta'])): ?><div class="cms-actions"><?= Partial::button($p['cta'], 'primary') ?></div><?php endif; ?>
    </div>
</div>
