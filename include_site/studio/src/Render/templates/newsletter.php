<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$uid = 'news-' . $ctx['uid'];
?>
<div class="cms-news cms-news--<?= $e($variant) ?>">
    <div class="cms-news__copy">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-news__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['body'])): ?><p class="cms-news__body"><?= nl2br($e($p['body'])) ?></p><?php endif; ?>
    </div>
    <form class="cms-news__form" method="post" action="">
        <label class="sr-only" for="<?= $e($uid) ?>-email">Email address</label>
        <input id="<?= $e($uid) ?>-email" name="email" type="email" autocomplete="email" required placeholder="<?= $e($p['placeholder'] ?? 'you@example.com') ?>">
        <button class="cms-button cms-button--primary" type="submit"><?= $e($p['submitLabel'] ?? 'Subscribe') ?></button>
    </form>
    <?php if (!empty($p['note'])): ?><p class="cms-news__note"><?= $e($p['note']) ?></p><?php endif; ?>
</div>
