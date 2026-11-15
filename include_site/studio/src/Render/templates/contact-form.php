<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$uid = 'contact-' . $ctx['uid'];
?>
<div class="cms-contact cms-contact--<?= $e($variant) ?>">
    <?php if ($variant === 'split'): ?>
        <aside class="cms-contact__aside">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-contact__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-contact__intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
            <dl class="cms-contact__details">
                <?php if (!empty($p['email'])): ?><div><dt>Email</dt><dd><a href="mailto:<?= $e($p['email']) ?>"><?= $e($p['email']) ?></a></dd></div><?php endif; ?>
                <?php if (!empty($p['phone'])): ?><div><dt>Phone</dt><dd><a href="tel:<?= $e(preg_replace('/[^0-9+]/', '', (string) $p['phone'])) ?>"><?= $e($p['phone']) ?></a></dd></div><?php endif; ?>
                <?php if (!empty($p['address'])): ?><div><dt>Studio</dt><dd><?= nl2br($e($p['address'])) ?></dd></div><?php endif; ?>
            </dl>
        </aside>
    <?php else: ?>
        <header class="cms-contact__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-contact__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-contact__intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>

    <form class="cms-contact__form" method="post" action="">
        <div class="cms-contact__fields">
            <label class="cms-contact__field" for="<?= $e($uid) ?>-name">
                <span>Name</span>
                <input id="<?= $e($uid) ?>-name" name="name" type="text" autocomplete="name" required>
            </label>
            <label class="cms-contact__field" for="<?= $e($uid) ?>-email">
                <span>Email</span>
                <input id="<?= $e($uid) ?>-email" name="email" type="email" autocomplete="email" required>
            </label>
            <?php if ($variant === 'card'): ?>
                <label class="cms-contact__field" for="<?= $e($uid) ?>-org">
                    <span>Organisation</span>
                    <input id="<?= $e($uid) ?>-org" name="organisation" type="text" autocomplete="organization">
                </label>
            <?php endif; ?>
            <label class="cms-contact__field cms-contact__field--wide" for="<?= $e($uid) ?>-message">
                <span>What are you working on?</span>
                <textarea id="<?= $e($uid) ?>-message" name="message" rows="5" required></textarea>
            </label>
        </div>
        <div class="cms-contact__foot">
            <button class="cms-button cms-button--primary" type="submit"><?= $e($p['submitLabel'] ?? 'Send enquiry') ?></button>
            <?php if (!empty($p['note'])): ?><p class="cms-contact__note"><?= $e($p['note']) ?></p><?php endif; ?>
        </div>
    </form>

    <?php if ($variant !== 'split' && (!empty($p['email']) || !empty($p['phone']) || !empty($p['address']))): ?>
        <dl class="cms-contact__details cms-contact__details--row">
            <?php if (!empty($p['email'])): ?><div><dt>Email</dt><dd><a href="mailto:<?= $e($p['email']) ?>"><?= $e($p['email']) ?></a></dd></div><?php endif; ?>
            <?php if (!empty($p['phone'])): ?><div><dt>Phone</dt><dd><?= $e($p['phone']) ?></dd></div><?php endif; ?>
            <?php if (!empty($p['address'])): ?><div><dt>Studio</dt><dd><?= $e($p['address']) ?></dd></div><?php endif; ?>
        </dl>
    <?php endif; ?>
</div>
