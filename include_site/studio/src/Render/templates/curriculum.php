<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$modules = is_array($p['modules'] ?? null) ? $p['modules'] : [];
$topics = static function (array $module): array {
    $lines = preg_split('/\r\n|\r|\n/', (string) ($module['topics'] ?? '')) ?: [];
    return array_values(array_filter(array_map('trim', $lines), static fn (string $line): bool => $line !== ''));
};
?>
<div class="cms-curriculum cms-curriculum--<?= $e($variant) ?>">
    <header class="cms-curriculum__head">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-curriculum__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </header>

    <?php if ($variant === 'table'): ?>
        <div class="cms-curriculum__wrap">
            <table class="cms-curriculum__table">
                <caption class="sr-only"><?= $e($p['heading'] ?? 'Curriculum') ?></caption>
                <thead><tr><th scope="col">Module</th><th scope="col">Focus</th><th scope="col">Topics</th><th scope="col">When</th></tr></thead>
                <tbody>
                <?php foreach ($modules as $module): ?>
                    <tr>
                        <th scope="row"><?= $e($module['title'] ?? '') ?></th>
                        <td><?= $e($module['summary'] ?? '') ?></td>
                        <td><?= $e(implode(' · ', $topics($module))) ?></td>
                        <td><?= $e($module['duration'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($variant === 'modules'): ?>
        <ul class="cms-curriculum__cards">
            <?php foreach ($modules as $index => $module): ?>
                <li class="cms-curriculum__card">
                    <span class="cms-curriculum__index" aria-hidden="true"><?= $e(sprintf('%02d', $index + 1)) ?></span>
                    <h3 class="cms-curriculum__name"><?= $e($module['title'] ?? '') ?></h3>
                    <?php if (!empty($module['summary'])): ?><p class="cms-curriculum__copy"><?= $e($module['summary']) ?></p><?php endif; ?>
                    <?php if ($topics($module)): ?><ul class="cms-curriculum__topics"><?php foreach ($topics($module) as $topic): ?><li><?= $e($topic) ?></li><?php endforeach; ?></ul><?php endif; ?>
                    <?php if (!empty($module['duration'])): ?><p class="cms-curriculum__duration"><?= $e($module['duration']) ?></p><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <ol class="cms-curriculum__outline">
            <?php foreach ($modules as $module): ?>
                <li class="cms-curriculum__row">
                    <div class="cms-curriculum__row-head">
                        <h3 class="cms-curriculum__name"><?= $e($module['title'] ?? '') ?></h3>
                        <?php if (!empty($module['duration'])): ?><span class="cms-curriculum__duration"><?= $e($module['duration']) ?></span><?php endif; ?>
                    </div>
                    <?php if (!empty($module['summary'])): ?><p class="cms-curriculum__copy"><?= $e($module['summary']) ?></p><?php endif; ?>
                    <?php if ($topics($module)): ?><p class="cms-curriculum__topic-line"><?= $e(implode(' · ', $topics($module))) ?></p><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>
