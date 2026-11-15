<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$options = is_array($p['options'] ?? null) ? $p['options'] : [];
$features = is_array($p['features'] ?? null) ? $p['features'] : [];
$cells = static function (array $feature): array {
    $parts = array_map('trim', explode('|', (string) ($feature['values'] ?? '')));
    return $parts;
};
?>
<div class="cms-matrix cms-matrix--<?= $e($variant) ?>">
    <header class="cms-matrix__head">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-matrix__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </header>

    <?php if ($variant === 'cards'): ?>
        <div class="cms-matrix__cards">
            <?php foreach ($options as $index => $option): ?>
                <section class="cms-matrix__card <?= ($option['highlighted'] ?? 'no') === 'yes' ? 'is-featured' : '' ?>">
                    <h3 class="cms-matrix__name"><?= $e($option['name'] ?? '') ?></h3>
                    <?php if (!empty($option['summary'])): ?><p class="cms-matrix__summary"><?= $e($option['summary']) ?></p><?php endif; ?>
                    <dl class="cms-matrix__facts">
                        <?php foreach ($features as $feature): ?>
                            <div><dt><?= $e($feature['feature'] ?? '') ?></dt><dd><?= $e($cells($feature)[$index] ?? '—') ?></dd></div>
                        <?php endforeach; ?>
                    </dl>
                </section>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="cms-matrix__wrap">
            <table class="cms-matrix__table">
                <caption class="sr-only"><?= $e($p['heading'] ?? 'Comparison') ?></caption>
                <thead>
                    <tr>
                        <th scope="col">Criterion</th>
                        <?php foreach ($options as $option): ?>
                            <th scope="col" class="<?= ($option['highlighted'] ?? 'no') === 'yes' ? 'is-featured' : '' ?>">
                                <?= $e($option['name'] ?? '') ?>
                                <?php if (!empty($option['summary'])): ?><span class="cms-matrix__hint"><?= $e($option['summary']) ?></span><?php endif; ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($features as $feature): ?>
                        <tr>
                            <th scope="row"><?= $e($feature['feature'] ?? '') ?></th>
                            <?php foreach ($options as $index => $option): ?>
                                <td><?= $e($cells($feature)[$index] ?? '—') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php if (!empty($p['note'])): ?><p class="cms-matrix__note"><?= $e($p['note']) ?></p><?php endif; ?>
</div>
