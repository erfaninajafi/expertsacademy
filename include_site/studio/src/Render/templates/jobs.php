<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-jobs cms-jobs--<?= $e($variant) ?>">
    <header class="cms-jobs__head">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-jobs__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </header>
    <?php if ($variant === 'table'): ?>
        <div class="cms-jobs__wrap">
            <table class="cms-jobs__table">
                <caption class="sr-only"><?= $e($p['heading'] ?? 'Open roles') ?></caption>
                <thead><tr><th scope="col">Role</th><th scope="col">Location</th><th scope="col">Type</th><th scope="col"></th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <th scope="row"><?= $e($item['title'] ?? '') ?></th>
                        <td><?= $e($item['location'] ?? '') ?></td>
                        <td><?= $e($item['type'] ?? '') ?></td>
                        <td><?php if (!empty($item['href'])): ?><a href="<?= $e($item['href']) ?>">View role</a><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <ul class="cms-jobs__list">
            <?php foreach ($items as $item): ?>
                <li class="cms-jobs__item">
                    <div class="cms-jobs__body">
                        <h3 class="cms-jobs__name"><?php if (!empty($item['href'])): ?><a href="<?= $e($item['href']) ?>"><?= $e($item['title'] ?? '') ?></a><?php else: ?><?= $e($item['title'] ?? '') ?><?php endif; ?></h3>
                        <?php if (!empty($item['summary'])): ?><p class="cms-jobs__copy"><?= $e($item['summary']) ?></p><?php endif; ?>
                    </div>
                    <div class="cms-jobs__meta">
                        <?php if (!empty($item['location'])): ?><span><?= $e($item['location']) ?></span><?php endif; ?>
                        <?php if (!empty($item['type'])): ?><span><?= $e($item['type']) ?></span><?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
