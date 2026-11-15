<?php
/** @var array $ctx */
$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
$grouped = [];
foreach ($items as $item) {
    $grouped[(string) ($item['day'] ?? '')][] = $item;
}
?>
<div class="cms-schedule cms-schedule--<?= $e($variant) ?>">
    <header class="cms-schedule__head">
        <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-schedule__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
        <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
    </header>

    <?php if ($variant === 'table'): ?>
        <div class="cms-schedule__wrap">
            <table class="cms-schedule__table">
                <caption class="sr-only"><?= $e($p['heading'] ?? 'Schedule') ?></caption>
                <thead><tr><th scope="col">Day</th><th scope="col">Time</th><th scope="col">Session</th><th scope="col">Speaker</th><th scope="col">Track</th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <th scope="row"><?= $e($item['day'] ?? '') ?></th>
                        <td><?= $e($item['time'] ?? '') ?></td>
                        <td><?= $e($item['title'] ?? '') ?></td>
                        <td><?= $e($item['speaker'] ?? '') ?></td>
                        <td><?= $e($item['track'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($variant === 'days'): ?>
        <div class="cms-schedule__days">
            <?php foreach ($grouped as $day => $sessions): ?>
                <section class="cms-schedule__day">
                    <h3 class="cms-schedule__day-name"><?= $e($day) ?></h3>
                    <ul>
                        <?php foreach ($sessions as $session): ?>
                            <li>
                                <span class="cms-schedule__time"><?= $e($session['time'] ?? '') ?></span>
                                <span class="cms-schedule__session">
                                    <span class="cms-schedule__name"><?= $e($session['title'] ?? '') ?></span>
                                    <span class="cms-schedule__speaker"><?= $e($session['speaker'] ?? '') ?></span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <ol class="cms-schedule__agenda">
            <?php foreach ($items as $item): ?>
                <li class="cms-schedule__row">
                    <span class="cms-schedule__when"><strong><?= $e($item['day'] ?? '') ?></strong><?php if (!empty($item['time'])): ?><span><?= $e($item['time']) ?></span><?php endif; ?></span>
                    <span class="cms-schedule__session">
                        <span class="cms-schedule__name"><?= $e($item['title'] ?? '') ?></span>
                        <?php if (!empty($item['speaker'])): ?><span class="cms-schedule__speaker"><?= $e($item['speaker']) ?></span><?php endif; ?>
                    </span>
                    <?php if (!empty($item['track'])): ?><span class="cms-schedule__track"><?= $e($item['track']) ?></span><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
</div>
