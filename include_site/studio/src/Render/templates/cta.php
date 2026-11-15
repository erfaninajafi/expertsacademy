<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
?>
<div class="cms-cta cms-cta--<?= $e($ctx['variant']) ?>">
    <div class="cms-cta__body">
        <?= Partial::heading($ctx['headingLevel'], (string) $p['heading'], 'cms-heading cms-cta__heading') ?>
        <?php if (!empty($p['body'])): ?><p><?= nl2br($e($p['body'])) ?></p><?php endif; ?>
    </div>
    <div class="cms-cta__action"><?= Partial::button($p['primaryCta'], 'primary') ?></div>
</div>
