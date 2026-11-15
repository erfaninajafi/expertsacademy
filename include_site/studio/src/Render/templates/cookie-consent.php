<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
?>
<div class="cms-cookie cms-cookie--<?= $e($variant) ?>" role="region" aria-label="Cookie notice">
    <p class="cms-cookie__message"><?= nl2br($e($p['message'] ?? '')) ?></p>
    <div class="cms-cookie__actions">
        <button type="button" class="cms-button cms-button--primary"><?= $e($p['acceptLabel'] ?? 'Accept') ?></button>
        <button type="button" class="cms-button cms-button--ghost"><?= $e($p['settingsLabel'] ?? 'Settings') ?></button>
        <?php if (!empty($p['policyHref'])): ?><a class="cms-cookie__link" href="<?= $e($p['policyHref']) ?>">Privacy policy</a><?php endif; ?>
    </div>
</div>
