<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$image = isset($p['image']['id']) ? $ctx['media']($p['image']['id']) : null;
$alt = (string) ($p['image']['alt'] ?? '');
?>
<div class="cms-author cms-author--<?= $e($variant) ?>">
    <div class="cms-author__portrait">
        <?= Partial::figure($image, $alt !== '' ? $alt : (string) ($p['name'] ?? ''), 'cms-author__figure') ?>
    </div>
    <div class="cms-author__body">
        <p class="cms-eyebrow">Written by</p>
        <p class="cms-author__name">
            <?php if (!empty($p['href'])): ?><a href="<?= $e($p['href']) ?>"><?= $e($p['name'] ?? '') ?></a><?php else: ?><?= $e($p['name'] ?? '') ?><?php endif; ?>
        </p>
        <?php if (!empty($p['role'])): ?><p class="cms-author__role"><?= $e($p['role']) ?></p><?php endif; ?>
        <?php if (!empty($p['bio'])): ?><p class="cms-author__bio"><?= nl2br($e($p['bio'])) ?></p><?php endif; ?>
    </div>
</div>
