<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
?>
<div class="cms-press cms-press--<?= $e($variant) ?>">
    <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-press__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
    <?php if ($variant === 'table'): ?>
        <div class="cms-press__wrap">
            <table class="cms-press__table">
                <thead><tr><th scope="col">Outlet</th><th scope="col">Story</th><th scope="col">Date</th><th scope="col"></th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <th scope="row"><?= $e($item['outlet'] ?? '') ?></th>
                        <td><?= $e($item['title'] ?? '') ?></td>
                        <td><?= $e($item['date'] ?? '') ?></td>
                        <td><?php if (!empty($item['href'])): ?><a href="<?= $e($item['href']) ?>">Read</a><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <ul class="cms-press__list">
            <?php foreach ($items as $item): ?>
                <li class="cms-press__item">
                    <p class="cms-press__outlet"><?= $e($item['outlet'] ?? '') ?><?php if (!empty($item['date'])): ?><span><?= $e($item['date']) ?></span><?php endif; ?></p>
                    <h3 class="cms-press__headline"><?php if (!empty($item['href'])): ?><a href="<?= $e($item['href']) ?>"><?= $e($item['title'] ?? '') ?></a><?php else: ?><?= $e($item['title'] ?? '') ?><?php endif; ?></h3>
                    <?php if (!empty($item['quote']) && $variant !== 'featured'): ?><p class="cms-press__quote">“<?= $e($item['quote']) ?>”</p><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
