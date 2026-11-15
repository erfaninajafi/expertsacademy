<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;

$p = $ctx['props'];
$e = $ctx['e'];
$variant = $ctx['variant'];
$tag = 'h' . $ctx['headingLevel'];
$members = is_array($p['members'] ?? null) ? $p['members'] : [];
$media = static function (array $member) use ($ctx): array {
    $image = isset($member['image']['id']) ? $ctx['media']($member['image']['id']) : null;
    return [$image, (string) ($member['image']['alt'] ?? '')];
};
?>
<div class="cms-team cms-team--<?= $e($variant) ?>">
    <?php if (!empty($p['heading']) || !empty($p['intro'])): ?>
        <header class="cms-team__head">
            <?php if (!empty($p['heading'])): ?><<?= $tag ?> class="cms-team__title"><?= $e($p['heading']) ?></<?= $tag ?>><?php endif; ?>
            <?php if (!empty($p['intro'])): ?><p class="cms-intro"><?= nl2br($e($p['intro'])) ?></p><?php endif; ?>
        </header>
    <?php endif; ?>

    <?php if ($variant === 'editorial'): ?>
        <ol class="cms-team__editorial">
            <?php foreach ($members as $index => $member): ?>
                <li class="cms-team__row">
                    <span class="cms-team__index" aria-hidden="true"><?= $e(sprintf('%02d', $index + 1)) ?></span>
                    <div>
                        <h3 class="cms-team__name"><?php if (!empty($member['href'])): ?><a href="<?= $e($member['href']) ?>"><?= $e($member['name'] ?? '') ?></a><?php else: ?><?= $e($member['name'] ?? '') ?><?php endif; ?></h3>
                        <?php if (!empty($member['role'])): ?><p class="cms-team__role"><?= $e($member['role']) ?></p><?php endif; ?>
                        <?php if (!empty($member['bio'])): ?><p class="cms-team__bio"><?= $e($member['bio']) ?></p><?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php elseif ($variant === 'spotlight'): ?>
        <div class="cms-team__spotlight">
            <?php if (!empty($members[0])): ?>
                <?php [$image, $alt] = $media($members[0]); ?>
                <div class="cms-team__lead">
                    <?php if ($image !== null): ?><?= Partial::figure($image, $alt, 'cms-team__figure', true) ?><?php endif; ?>
                    <div class="cms-team__lead-copy">
                        <h3 class="cms-team__name"><?= $e($members[0]['name'] ?? '') ?></h3>
                        <?php if (!empty($members[0]['role'])): ?><p class="cms-team__role"><?= $e($members[0]['role']) ?></p><?php endif; ?>
                        <?php if (!empty($members[0]['bio'])): ?><p class="cms-team__bio"><?= $e($members[0]['bio']) ?></p><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <ul class="cms-team__mini">
                <?php foreach (array_slice($members, 1) as $member): ?>
                    <?php [$image, $alt] = $media($member); ?>
                    <li>
                        <?php if ($image !== null): ?><span class="cms-team__mini-media"><?= Partial::figure($image, $alt, 'cms-team__avatar') ?></span><?php endif; ?>
                        <span>
                            <span class="cms-team__mini-name"><?= $e($member['name'] ?? '') ?></span>
                            <span class="cms-team__role"><?= $e($member['role'] ?? '') ?></span>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($variant === 'list'): ?>
        <ul class="cms-team__list">
            <?php foreach ($members as $member): ?>
                <?php [$image, $alt] = $media($member); ?>
                <li class="cms-team__item">
                    <?php if ($image !== null): ?><span class="cms-team__thumb"><?= Partial::figure($image, $alt, 'cms-team__figure') ?></span><?php endif; ?>
                    <div class="cms-team__copy">
                        <h3 class="cms-team__name"><?= $e($member['name'] ?? '') ?></h3>
                        <?php if (!empty($member['role'])): ?><p class="cms-team__role"><?= $e($member['role']) ?></p><?php endif; ?>
                        <?php if (!empty($member['bio'])): ?><p class="cms-team__bio"><?= $e($member['bio']) ?></p><?php endif; ?>
                    </div>
                    <?php if (!empty($member['href'])): ?><a class="cms-team__link" href="<?= $e($member['href']) ?>">Profile →</a><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <ul class="cms-team__grid">
            <?php foreach ($members as $member): ?>
                <?php [$image, $alt] = $media($member); ?>
                <li class="cms-team__card">
                    <?php if ($image !== null): ?><?= Partial::figure($image, $alt, 'cms-team__figure') ?><?php endif; ?>
                    <div class="cms-team__card-copy">
                        <h3 class="cms-team__name"><?php if (!empty($member['href'])): ?><a href="<?= $e($member['href']) ?>"><?= $e($member['name'] ?? '') ?></a><?php else: ?><?= $e($member['name'] ?? '') ?><?php endif; ?></h3>
                        <?php if (!empty($member['role'])): ?><p class="cms-team__role"><?= $e($member['role']) ?></p><?php endif; ?>
                        <?php if (!empty($member['bio'])): ?><p class="cms-team__bio"><?= $e($member['bio']) ?></p><?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
