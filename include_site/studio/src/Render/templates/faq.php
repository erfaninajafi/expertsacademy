<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-faq cms-faq--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-faq__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-faq__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>

    <div class="cms-faq__list">
        <?php foreach ($items as $index => $item): ?>
            <section class="cms-faq__item">
                <?php if ($variant === 'divided'): ?><span class="cms-faq__number" aria-hidden="true"><?= $e(sprintf('%02d', $index + 1)) ?></span><?php endif; ?>
                <div class="cms-faq__body">
                    <h3 class="cms-faq__question"><?= $e($item['question'] ?? '') ?></h3>
                    <div class="cms-richtext"><?= $ctx['rich']((string) ($item['answer'] ?? '')) ?></div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
    <?php if (!empty($p['note'])): ?><p class="cms-faq__note"><?= $e($p['note']) ?></p><?php endif; ?>
</div>
