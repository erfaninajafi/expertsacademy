<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$uid = 'book-' . $ctx['uid'];
$slots = is_array($p['slots'] ?? null) ? $p['slots'] : [];
?>
<div class="cms-booking cms-booking--<?= $e($variant) ?>">
    <div class="cms-booking__intro">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-booking__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-booking__copy"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </div>
    <form class="cms-booking__form" method="post" action="">
        <?php if ($variant === 'slots' && $slots): ?>
            <fieldset class="cms-booking__slots">
                <legend>Available times</legend>
                <?php foreach ($slots as $index => $slot): ?>
                    <label class="cms-booking__slot">
                        <input type="radio" name="slot" value="<?= $e($slot['label'] ?? '') ?>" <?= $index === 0 ? 'checked' : '' ?>>
                        <span class="cms-booking__slot-time"><?= $e($slot['label'] ?? '') ?></span>
                        <span class="cms-booking__slot-detail"><?= $e($slot['detail'] ?? '') ?></span>
                    </label>
                <?php endforeach; ?>
            </fieldset>
        <?php endif; ?>
        <div class="cms-booking__fields">
            <label for="<?= $e($uid) ?>-name">Name<input id="<?= $e($uid) ?>-name" name="name" type="text" autocomplete="name" required></label>
            <label for="<?= $e($uid) ?>-email">Email<input id="<?= $e($uid) ?>-email" name="email" type="email" autocomplete="email" required></label>
        </div>
        <?php if ($variant !== 'slots' && $slots): ?>
            <label class="cms-booking__select" for="<?= $e($uid) ?>-slot">Preferred time
                <select id="<?= $e($uid) ?>-slot" name="slot">
                    <?php foreach ($slots as $slot): ?><option><?= $e($slot['label'] ?? '') ?></option><?php endforeach; ?>
                </select>
            </label>
        <?php endif; ?>
        <div class="cms-booking__foot">
            <button class="cms-button cms-button--primary" type="submit"><?= $e($p['submitLabel'] ?? 'Request this time') ?></button>
            <?php if (!empty($p['note'])): ?><p class="cms-booking__note"><?= $e($p['note']) ?></p><?php endif; ?>
        </div>
    </form>
</div>
