<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$uid = 'team-' . $ctx['uid'];
?>
<div class="cms-teamform cms-teamform--<?= $e($variant) ?>">
    <div class="cms-teamform__intro">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-teamform__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-teamform__copy"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </div>
    <form class="cms-teamform__form" method="post" action="">
        <?php if ($variant === 'steps'): ?><p class="cms-teamform__step">Step 1 of 2 · Team</p><?php endif; ?>
        <div class="cms-teamform__fields">
            <label for="<?= $e($uid) ?>-name">Name<input id="<?= $e($uid) ?>-name" name="name" type="text" autocomplete="name" required></label>
            <label for="<?= $e($uid) ?>-email">Work email<input id="<?= $e($uid) ?>-email" name="email" type="email" autocomplete="email" required></label>
            <label for="<?= $e($uid) ?>-org">Organisation<input id="<?= $e($uid) ?>-org" name="organisation" type="text" autocomplete="organization"></label>
            <label for="<?= $e($uid) ?>-size">Team size
                <select id="<?= $e($uid) ?>-size" name="size"><option>2–5</option><option>6–15</option><option>16–40</option><option>40+</option></select>
            </label>
            <label class="cms-teamform__wide" for="<?= $e($uid) ?>-brief">The brief<textarea id="<?= $e($uid) ?>-brief" name="brief" rows="4" required></textarea></label>
        </div>
        <div class="cms-teamform__foot">
            <button class="cms-button cms-button--primary" type="submit"><?= $e($p['submitLabel'] ?? 'Request a proposal') ?></button>
            <?php if (!empty($p['note'])): ?><p class="cms-teamform__note"><?= $e($p['note']) ?></p><?php endif; ?>
        </div>
    </form>
</div>
