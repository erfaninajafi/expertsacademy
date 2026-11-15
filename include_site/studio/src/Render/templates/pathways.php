<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
$levels = ['foundation' => 'Foundation', 'practitioner' => 'Practitioner', 'advanced' => 'Advanced'];
?>
<div class="cms-pathways cms-pathways--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-pathways__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-pathways__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>

    <?php if ($variant === 'tracks'): ?>
        <ul class="cms-pathways__tracks">
            <?php foreach ($items as $index => $item): ?>
                <li class="cms-pathways__track">
                    <span class="cms-pathways__track-marker" aria-hidden="true"><?= $e($item['meta'] ?: sprintf('%02d', $index + 1)) ?></span>
                    <div class="cms-pathways__track-body">
                        <p class="cms-pathways__level cms-pathways__level--<?= $e($item['level'] ?? 'foundation') ?>"><?= $e($levels[$item['level'] ?? 'foundation'] ?? '') ?></p>
                        <h3 class="cms-pathways__name"><?= $e($item['title'] ?? '') ?></h3>
                        <?php if (!empty($item['body'])): ?><p class="cms-pathways__copy"><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
                        <?php if (!empty($item['duration'])): ?><p class="cms-pathways__duration"><?= $e($item['duration']) ?></p><?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($variant === 'roadmap'): ?>
        <ol class="cms-pathways__roadmap">
            <?php foreach ($items as $index => $item): ?>
                <li class="cms-pathways__stop">
                    <span class="cms-pathways__dot" aria-hidden="true"></span>
                    <div class="cms-pathways__stop-card">
                        <p class="cms-pathways__level"><?= $e($levels[$item['level'] ?? 'foundation'] ?? '') ?></p>
                        <h3 class="cms-pathways__name"><?= $e($item['title'] ?? '') ?></h3>
                        <?php if (!empty($item['body'])): ?><p class="cms-pathways__copy"><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
                        <p class="cms-pathways__duration"><?= $e($item['duration'] ?? '') ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php else: ?>
        <ol class="cms-pathways__steps">
            <?php foreach ($items as $index => $item): ?>
                <li class="cms-pathways__step">
                    <span class="cms-pathways__step-number" aria-hidden="true"><?= $e(sprintf('%02d', $index + 1)) ?></span>
                    <p class="cms-pathways__level"><?= $e($levels[$item['level'] ?? 'foundation'] ?? '') ?></p>
                    <h3 class="cms-pathways__name"><?= $e($item['title'] ?? '') ?></h3>
                    <?php if (!empty($item['body'])): ?><p class="cms-pathways__copy"><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
                    <p class="cms-pathways__duration"><?= $e($item['duration'] ?? '') ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>
