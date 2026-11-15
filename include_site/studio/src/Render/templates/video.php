<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$poster = isset($p['poster']['id']) ? $ctx['media']($p['poster']['id']) : null;
$alt = (string) ($p['poster']['alt'] ?? '');
$href = (string) ($p['cta']['href'] ?? '#');
?>
<div class="cms-video cms-video--<?= $e($variant) ?>">
    <a class="cms-video__frame" href="<?= $e($href) ?>" aria-label="<?= $e($p['cta']['label'] ?? $p['heading'] ?? 'Play video') ?>">
        <?= Partial::figure($poster, $alt !== '' ? $alt : (string) ($p['heading'] ?? ''), 'cms-video__poster', true) ?>
        <span class="cms-video__play" aria-hidden="true">▶</span>
        <?php if (!empty($p['duration'])): ?><span class="cms-video__duration"><?= $e($p['duration']) ?></span><?php endif; ?>
    </a>
    <div class="cms-video__copy">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-video__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-video__intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        <?php if (!empty($p['caption'])): ?><p class="cms-video__caption"><?= $e($p['caption']) ?></p><?php endif; ?>
        <?php if (!empty($p['cta']['label'])): ?><div class="cms-actions"><?= Partial::button($p['cta'], 'ghost') ?></div><?php endif; ?>
    </div>
</div>
