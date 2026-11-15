<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$uid = 'apply-' . $ctx['uid'];
?>
<div class="cms-apply cms-apply--<?= $e($variant) ?>">
    <div class="cms-apply__intro">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-apply__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-apply__copy"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        <?php if (!empty($p['deadline'])): ?><p class="cms-apply__deadline"><?= $e($p['deadline']) ?></p><?php endif; ?>
    </div>

    <form class="cms-apply__form" method="post" action="">
        <?php if ($variant === 'steps'): ?><p class="cms-apply__step-label">Step 1 of 3 · About you</p><?php endif; ?>
        <fieldset class="cms-apply__group">
            <legend class="cms-apply__legend">About you</legend>
            <div class="cms-apply__fields">
                <label for="<?= $e($uid) ?>-name"><span>Name</span><input id="<?= $e($uid) ?>-name" name="name" type="text" autocomplete="name" required></label>
                <label for="<?= $e($uid) ?>-email"><span>Email</span><input id="<?= $e($uid) ?>-email" name="email" type="email" autocomplete="email" required></label>
                <label for="<?= $e($uid) ?>-role"><span>Current role</span><input id="<?= $e($uid) ?>-role" name="role" type="text" autocomplete="organization-title"></label>
            </div>
        </fieldset>
        <fieldset class="cms-apply__group">
            <legend class="cms-apply__legend">What you want to change</legend>
            <label class="cms-apply__wide" for="<?= $e($uid) ?>-goal"><span>Goal for the next six months</span><textarea id="<?= $e($uid) ?>-goal" name="goal" rows="4" required></textarea></label>
        </fieldset>
        <fieldset class="cms-apply__group">
            <legend class="cms-apply__legend">Availability</legend>
            <label for="<?= $e($uid) ?>-start"><span>Preferred cohort</span>
                <select id="<?= $e($uid) ?>-start" name="cohort"><option>January</option><option>April</option><option>September</option></select>
            </label>
        </fieldset>
        <div class="cms-apply__foot">
            <button class="cms-button cms-button--primary" type="submit"><?= $e($p['submitLabel'] ?? 'Submit application') ?></button>
            <?php if (!empty($p['note'])): ?><p class="cms-apply__note"><?= $e($p['note']) ?></p><?php endif; ?>
        </div>
    </form>
</div>
