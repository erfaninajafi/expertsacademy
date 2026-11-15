<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-spec cms-spec--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-spec__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-spec__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>

    <?php if ($variant === 'table'): ?>
        <div class="cms-spec__wrap">
            <table class="cms-spec__table">
                <caption class="sr-only"><?= $e($p['heading'] ?? 'Specification') ?></caption>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <th scope="row"><?= $e($item['label'] ?? '') ?></th>
                            <td>
                                <span class="cms-spec__value"><?= $e($item['value'] ?? '') ?></span>
                                <?php if (!empty($item['note'])): ?><span class="cms-spec__note"><?= $e($item['note']) ?></span><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <dl class="cms-spec__rows">
            <?php foreach ($items as $item): ?>
                <div class="cms-spec__row">
                    <dt><?= $e($item['label'] ?? '') ?></dt>
                    <dd>
                        <span class="cms-spec__value"><?= $e($item['value'] ?? '') ?></span>
                        <?php if (!empty($item['note'])): ?><span class="cms-spec__note"><?= $e($item['note']) ?></span><?php endif; ?>
                    </dd>
                </div>
            <?php endforeach; ?>
        </dl>
    <?php endif; ?>
</div>
