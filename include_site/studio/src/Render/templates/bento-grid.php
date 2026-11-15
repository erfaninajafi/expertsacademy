<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$items = is_array($p['items'] ?? null) ? $p['items'] : [];
$tag = 'h' . $ctx['headingLevel'];
?>
<div class="cms-bento cms-bento--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-bento__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-bento__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>
    <ul class="cms-bento__grid">
        <?php foreach ($items as $index => $item):
            $size = in_array($item['size'] ?? '', ['standard', 'wide', 'tall', 'hero'], true) ? $item['size'] : 'standard';
            $image = isset($item['image']['id']) ? $ctx['media']($item['image']['id']) : null;
            $alt = (string) ($item['image']['alt'] ?? '');
            $href = (string) ($item['href'] ?? '');
            $lead = $variant === 'editorial' && $index === 0;
            ?>
            <li class="cms-bento__tile cms-bento__tile--<?= $e($size) ?><?= $lead ? ' cms-bento__tile--lead' : '' ?>">
                <?php if ($href !== ''): ?><a class="cms-bento__link" href="<?= $e($href) ?>"><?php endif; ?>
                <?php if ($variant !== 'metrics' && $image !== null): ?>
                    <div class="cms-bento__media"><?= Partial::figure($image, $alt, 'cms-bento__figure') ?></div>
                <?php endif; ?>
                <div class="cms-bento__body">
                    <?php if (!empty($item['tag'])): ?><p class="cms-eyebrow"><?= $e($item['tag']) ?></p><?php endif; ?>
                    <?php if ($variant === 'metrics'): ?>
                        <p class="cms-bento__metric"><?= $e($item['title'] ?? '') ?></p>
                        <?php if (!empty($item['body'])): ?><p class="cms-bento__label"><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
                    <?php else: ?>
                        <h3 class="cms-bento__heading"><?= $e($item['title'] ?? '') ?></h3>
                        <?php if (!empty($item['body'])): ?><p class="cms-bento__copy"><?= nl2br($e($item['body'])) ?></p><?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php if ($href !== ''): ?></a><?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
