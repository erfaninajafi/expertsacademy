<?php
/** @var array $ctx */
$e = $ctx['e'];
$size = (string) ($ctx['props']['size'] ?? 'medium');
?>
<div class="cms-divider cms-divider--<?= $e($ctx['variant']) ?> cms-divider--<?= $e($size) ?>" aria-hidden="true">
    <?php if ($ctx['variant'] === 'line'): ?><hr><?php endif; ?>
</div>
