<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$plans = is_array($p['plans'] ?? null) ? $p['plans'] : [];
$features = static function (array $plan): array {
    $text = (string) ($plan['features'] ?? '');
    $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
    return array_values(array_filter(array_map('trim', $lines), static fn (string $line): bool => $line !== ''));
};
$featured = null;
foreach ($plans as $plan) {
    if (($plan['featured'] ?? 'no') === 'yes') {
        $featured = $plan;
        break;
    }
}
$featured ??= $plans[0] ?? null;
?>
<div class="cms-pricing cms-pricing--<?= $e($variant) ?>">
    <header class="cms-pricing__head">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-pricing__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </header>

    <?php if ($variant === 'comparison'): ?>
        <div class="cms-pricing__table-wrap">
            <table class="cms-pricing__table">
                <caption class="sr-only"><?= $e($p['heading']) ?></caption>
                <thead>
                    <tr>
                        <th scope="col">Plan</th>
                        <?php foreach ($plans as $plan): ?>
                            <th scope="col" class="<?= ($plan['featured'] ?? 'no') === 'yes' ? 'is-featured' : '' ?>">
                                <span class="cms-pricing__name"><?= $e($plan['name'] ?? '') ?></span>
                                <span class="cms-pricing__price"><?= $e($plan['price'] ?? '') ?></span>
                                <?php if (!empty($plan['period'])): ?><span class="cms-pricing__period"><?= $e($plan['period']) ?></span><?php endif; ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $labels = [];
                    foreach ($plans as $plan) {
                        foreach ($features($plan) as $label) {
                            $labels[$label] = true;
                        }
                    }
                    foreach (array_keys($labels) as $label):
                        ?>
                        <tr>
                            <th scope="row"><?= $e($label) ?></th>
                            <?php foreach ($plans as $plan): ?>
                                <td><?= in_array($label, $features($plan), true) ? '<span aria-label="Included">✓</span>' : '<span class="cms-pricing__dash" aria-label="Not included">—</span>' ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row">Choose</th>
                        <?php foreach ($plans as $plan): ?>
                            <td>
                                <?php if (!empty($plan['cta']['label'])): ?>
                                    <a class="cms-button cms-button--primary" href="<?= $e($plan['cta']['href'] ?? '/') ?>"><?= $e($plan['cta']['label']) ?></a>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php elseif ($variant === 'spotlight' && $featured !== null): ?>
        <div class="cms-pricing__spotlight">
            <article class="cms-pricing__feature">
                <p class="cms-eyebrow">Most chosen</p>
                <h3 class="cms-pricing__name"><?= $e($featured['name'] ?? '') ?></h3>
                <p class="cms-pricing__price"><?= $e($featured['price'] ?? '') ?><span><?= $e($featured['period'] ?? '') ?></span></p>
                <?php if (!empty($featured['summary'])): ?><p class="cms-pricing__summary"><?= nl2br($e($featured['summary'])) ?></p><?php endif; ?>
                <ul class="cms-pricing__features">
                    <?php foreach ($features($featured) as $line): ?><li><?= $e($line) ?></li><?php endforeach; ?>
                </ul>
                <?php if (!empty($featured['cta']['label'])): ?>
                    <?= Partial::button($featured['cta'], 'primary') ?>
                <?php endif; ?>
            </article>
            <div class="cms-pricing__stack">
                <?php foreach ($plans as $plan): if ($plan === $featured) { continue; } ?>
                    <article class="cms-pricing__row">
                        <div>
                            <h3 class="cms-pricing__name"><?= $e($plan['name'] ?? '') ?></h3>
                            <?php if (!empty($plan['summary'])): ?><p class="cms-pricing__summary"><?= nl2br($e($plan['summary'])) ?></p><?php endif; ?>
                        </div>
                        <div class="cms-pricing__row-price">
                            <span class="cms-pricing__price"><?= $e($plan['price'] ?? '') ?></span>
                            <?php if (!empty($plan['cta']['label'])): ?>
                                <a class="cms-button cms-button--ghost" href="<?= $e($plan['cta']['href'] ?? '/') ?>"><?= $e($plan['cta']['label']) ?></a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <ul class="cms-pricing__tiers">
            <?php foreach ($plans as $plan): ?>
                <li class="cms-pricing__tier<?= ($plan['featured'] ?? 'no') === 'yes' ? ' is-featured' : '' ?>">
                    <?php if (($plan['featured'] ?? 'no') === 'yes'): ?><p class="cms-pricing__flag">Most chosen</p><?php endif; ?>
                    <h3 class="cms-pricing__name"><?= $e($plan['name'] ?? '') ?></h3>
                    <p class="cms-pricing__price"><?= $e($plan['price'] ?? '') ?><span><?= $e($plan['period'] ?? '') ?></span></p>
                    <?php if (!empty($plan['summary'])): ?><p class="cms-pricing__summary"><?= nl2br($e($plan['summary'])) ?></p><?php endif; ?>
                    <ul class="cms-pricing__features">
                        <?php foreach ($features($plan) as $line): ?><li><?= $e($line) ?></li><?php endforeach; ?>
                    </ul>
                    <?php if (!empty($plan['cta']['label'])): ?><?= Partial::button($plan['cta'], ($plan['featured'] ?? 'no') === 'yes' ? 'primary' : 'ghost') ?><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($p['note'])): ?><p class="cms-pricing__note"><?= $e($p['note']) ?></p><?php endif; ?>
</div>
