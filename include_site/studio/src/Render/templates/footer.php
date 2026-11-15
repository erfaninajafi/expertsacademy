<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$columns = is_array($p['columns'] ?? null) ? $p['columns'] : [];
$social = is_array($p['social'] ?? null) ? $p['social'] : [];
$parse = static function (string $text): array {
    $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
    $out = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        $parts = array_map('trim', explode('|', $line, 2));
        $out[] = ['label' => $parts[0], 'href' => $parts[1] ?? '#'];
    }
    return $out;
};
?>
<footer class="cms-footer cms-footer--<?= $e($variant) ?>">
    <?php if ($variant === 'editorial'): ?>
        <div class="cms-footer__masthead">
            <p class="cms-footer__wordmark"><?= $e($p['brand'] ?? '') ?></p>
            <?php if (!empty($p['blurb'])): ?><p class="cms-footer__lede"><?= nl2br($e($p['blurb'])) ?></p><?php endif; ?>
        </div>
        <div class="cms-footer__columns">
            <?php foreach ($columns as $column): ?>
                <div class="cms-footer__column">
                    <h3 class="cms-footer__title"><?= $e($column['title'] ?? '') ?></h3>
                    <ul><?php foreach ($parse((string) ($column['links'] ?? '')) as $link): ?>
                        <li><a href="<?= $e($link['href']) ?>"><?= $e($link['label']) ?></a></li>
                    <?php endforeach; ?></ul>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($variant === 'compact'): ?>
        <div class="cms-footer__bar">
            <span class="cms-footer__name"><?= $e($p['brand'] ?? '') ?></span>
            <ul class="cms-footer__inline">
                <?php foreach ($columns as $column): ?>
                    <?php foreach ($parse((string) ($column['links'] ?? '')) as $link): ?>
                        <li><a href="<?= $e($link['href']) ?>"><?= $e($link['label']) ?></a></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
            <div class="cms-footer__social">
                <?php foreach ($social as $link): ?><a href="<?= $e($link['href'] ?? '#') ?>" rel="noopener"><?= $e($link['label'] ?? '') ?></a><?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="cms-footer__lead">
            <p class="cms-footer__name"><?= $e($p['brand'] ?? '') ?></p>
            <?php if (!empty($p['blurb'])): ?><p class="cms-footer__copy"><?= nl2br($e($p['blurb'])) ?></p><?php endif; ?>
            <div class="cms-footer__social">
                <?php foreach ($social as $link): ?><a href="<?= $e($link['href'] ?? '#') ?>" rel="noopener"><?= $e($link['label'] ?? '') ?></a><?php endforeach; ?>
            </div>
        </div>
        <div class="cms-footer__columns">
            <?php foreach ($columns as $column): ?>
                <div class="cms-footer__column">
                    <h3 class="cms-footer__title"><?= $e($column['title'] ?? '') ?></h3>
                    <ul><?php foreach ($parse((string) ($column['links'] ?? '')) as $link): ?>
                        <li><a href="<?= $e($link['href']) ?>"><?= $e($link['label']) ?></a></li>
                    <?php endforeach; ?></ul>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($p['legal'])): ?><p class="cms-footer__legal"><?= $e($p['legal']) ?></p><?php endif; ?>
</footer>
