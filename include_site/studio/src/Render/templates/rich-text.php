<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$rich = $ctx['rich'];
?>
<div class="cms-prose cms-prose--<?= $e($ctx['variant']) ?>">
    <?php if (!empty($p['heading'])): ?><?= Partial::heading($ctx['headingLevel'], $p['heading']) ?><?php endif; ?>
    <div class="cms-richtext"><?= $rich((string) $p['body']) ?></div>
</div>
