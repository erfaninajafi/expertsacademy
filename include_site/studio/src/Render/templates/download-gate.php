<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$uid = 'download-' . $ctx['uid'];
?>
<div class="cms-download cms-download--<?= $e($variant) ?>">
    <div class="cms-download__copy">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-download__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['body'])): ?><p class="cms-download__body"><?= nl2br($e($p['body'])) ?></p><?php endif; ?>
        <?php if (!empty($p['fileLabel'])): ?><p class="cms-download__file"><?= $e($p['fileLabel']) ?></p><?php endif; ?>
    </div>
    <form class="cms-download__form" method="post" action="">
        <label class="sr-only" for="<?= $e($uid) ?>-email">Email address</label>
        <input id="<?= $e($uid) ?>-email" name="email" type="email" autocomplete="email" required placeholder="you@example.com">
        <button class="cms-button cms-button--primary" type="submit"><?= $e($p['submitLabel'] ?? 'Send the guide') ?></button>
    </form>
    <?php if (!empty($p['fileHref'])): ?>
        <a class="cms-download__direct" href="<?= $e($p['fileHref']) ?>">Direct download</a>
    <?php endif; ?>
    <?php if (!empty($p['note'])): ?><p class="cms-download__note"><?= $e($p['note']) ?></p><?php endif; ?>
</div>
