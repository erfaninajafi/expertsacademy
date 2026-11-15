<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$columns = is_array($p['columns'] ?? null) ? $p['columns'] : [];
$parse = static function (string $text): array {
    $out = [];
    foreach (preg_split('/\r\n|\r|\n/', $text) ?: [] as $line) {
        $line = trim($line);
        if ($line === '') { continue; }
        $parts = array_map('trim', explode('|', $line, 2));
        $out[] = ['label' => $parts[0], 'href' => $parts[1] ?? '#'];
    }
    return $out;
};
?>
<nav class="cms-mega cms-mega--<?= $e($variant) ?>" aria-label="<?= $e($p['heading'] ?? 'Explore') ?>">
    <?php if (!empty($p['heading'])): ?><p class="cms-mega__heading"><?= $e($p['heading']) ?></p><?php endif; ?>
    <div class="cms-mega__grid">
        <?php foreach ($columns as $column): ?>
            <section class="cms-mega__column">
                <h2 class="cms-mega__title"><?= $e($column['title'] ?? '') ?></h2>
                <ul><?php foreach ($parse((string) ($column['links'] ?? '')) as $link): ?>
                    <li><a href="<?= $e($link['href']) ?>"><?= $e($link['label']) ?></a></li>
                <?php endforeach; ?></ul>
            </section>
        <?php endforeach; ?>
        <?php if ($variant === 'featured' && !empty($p['featureTitle'])): ?>
            <aside class="cms-mega__feature">
                <p class="cms-mega__feature-title"><?= $e($p['featureTitle']) ?></p>
                <?php if (!empty($p['featureBody'])): ?><p class="cms-mega__feature-copy"><?= $e($p['featureBody']) ?></p><?php endif; ?>
                <?php if (!empty($p['featureCta']['label'])): ?><?= Partial::button($p['featureCta'], 'ghost') ?><?php endif; ?>
            </aside>
        <?php endif; ?>
    </div>
</nav>
