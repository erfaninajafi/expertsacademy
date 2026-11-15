<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
?>
<div class="cms-location cms-location--<?= $e($variant) ?>">
    <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-location__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
    <div class="cms-location__grid">
        <div class="cms-location__block">
            <h3 class="cms-location__label">Studio</h3>
            <?php if (!empty($p['address'])): ?><p class="cms-location__address"><?= nl2br($e($p['address'])) ?></p><?php endif; ?>
            <?php if (!empty($p['hours'])): ?><p class="cms-location__hours"><?= nl2br($e($p['hours'])) ?></p><?php endif; ?>
        </div>
        <div class="cms-location__block">
            <h3 class="cms-location__label">Contact</h3>
            <?php if (!empty($p['email'])): ?><p><a href="mailto:<?= $e($p['email']) ?>"><?= $e($p['email']) ?></a></p><?php endif; ?>
            <?php if (!empty($p['phone'])): ?><p><a href="tel:<?= $e(preg_replace('/[^0-9+]/', '', (string) $p['phone'])) ?>"><?= $e($p['phone']) ?></a></p><?php endif; ?>
            <?php if (!empty($p['note'])): ?><p class="cms-location__note"><?= nl2br($e($p['note'])) ?></p><?php endif; ?>
        </div>
    </div>
</div>
