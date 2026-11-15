<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
$grouped = [];
foreach ($items as $item) {
    $key = $variant === 'az'
        ? strtoupper(mb_substr((string) ($item['letter'] ?: ($item['title'] ?? '#')), 0, 1))
        : (string) ($item['category'] ?? 'General');
    $grouped[$key][] = $item;
}
ksort($grouped);
?>
<div class="cms-index cms-index--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-index__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-index__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>
    <?php if ($variant === 'letters'): ?>
        <nav class="cms-index__letters" aria-label="Jump to a letter">
            <?php foreach (array_keys($grouped) as $letter): ?>
                <a href="#index-<?= $e(preg_replace('/[^A-Za-z0-9]/', '', (string) $letter)) ?>"><?= $e($letter) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="cms-index__groups">
            <?php foreach ($grouped as $key => $entries): ?>
                <section class="cms-index__group" id="index-<?= $e(preg_replace('/[^A-Za-z0-9]/', '', (string) $key)) ?>">
                    <h3 class="cms-index__letter"><?= $e($key) ?></h3>
                    <ul><?php foreach ($entries as $entry): ?><li><a href="<?= $e($entry['href'] ?? '#') ?>"><?= $e($entry['title'] ?? '') ?></a></li><?php endforeach; ?></ul>
                </section>
            <?php endforeach; ?>
        </div>
    <?php elseif ($variant === 'list'): ?>
        <ul class="cms-index__list">
            <?php foreach ($items as $item): ?>
                <li><a href="<?= $e($item['href'] ?? '#') ?>"><?= $e($item['title'] ?? '') ?></a><span><?= $e($item['category'] ?? '') ?></span></li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($variant === 'cards'): ?>
        <ul class="cms-index__cards">
            <?php foreach ($items as $item): ?>
                <li><p class="cms-eyebrow"><?= $e($item['category'] ?? '') ?></p><h3><a href="<?= $e($item['href'] ?? '#') ?>"><?= $e($item['title'] ?? '') ?></a></h3><?php if (!empty($item['summary'])): ?><p><?= $e($item['summary']) ?></p><?php endif; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="cms-index__groups">
            <?php foreach ($grouped as $key => $entries): ?>
                <section class="cms-index__group">
                    <h3 class="cms-index__letter"><?= $e($key) ?></h3>
                    <ul><?php foreach ($entries as $entry): ?><li><a href="<?= $e($entry['href'] ?? '#') ?>"><?= $e($entry['title'] ?? '') ?></a></li><?php endforeach; ?></ul>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
