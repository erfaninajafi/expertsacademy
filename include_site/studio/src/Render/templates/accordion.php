<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$rich = $ctx['rich'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
$uid = $ctx['uid'];
?>
<div class="cms-accordion cms-accordion--<?= $e($ctx['variant']) ?>">
    <?php if (!empty($p['heading'])): ?><?= Partial::heading($ctx['headingLevel'], $p['heading']) ?><?php endif; ?>
    <?php foreach ($items as $index => $item): $id = 'cms-acc-' . $uid . '-' . $index; ?>
        <details class="cms-accordion__item"<?= $index === 0 ? ' open' : '' ?>>
            <summary class="cms-accordion__summary" id="<?= $e($id) ?>-summary"><?= $e($item['question'] ?? '') ?></summary>
            <div class="cms-accordion__panel" role="region" aria-labelledby="<?= $e($id) ?>-summary">
                <div class="cms-richtext"><?= $rich((string) ($item['answer'] ?? '')) ?></div>
            </div>
        </details>
    <?php endforeach; ?>
</div>
