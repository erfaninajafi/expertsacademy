<?php
/** @var array $ctx */
use ExpertsAcademy\Studio\Render\Partial;
$p = $ctx['props'];
$e = $ctx['e'];
$avatar = isset($p['avatar']['id']) ? $ctx['media']($p['avatar']['id']) : null;
$alt = (string) ($p['avatar']['alt'] ?? '');
?>
<figure class="cms-quote cms-quote--<?= $e($ctx['variant']) ?>">
    <blockquote class="cms-quote__text"><?= nl2br($e($p['quote'])) ?></blockquote>
    <?php if (!empty($p['attribution']) || $avatar !== null): ?>
        <figcaption class="cms-quote__meta">
            <?php if ($avatar !== null): ?><span class="cms-quote__avatar"><?= Partial::figure($avatar, $alt, 'cms-quote__image') ?></span><?php endif; ?>
            <span>
                <?php if (!empty($p['attribution'])): ?><strong><?= $e($p['attribution']) ?></strong><?php endif; ?>
                <?php if (!empty($p['role'])): ?><span class="cms-quote__role"><?= $e($p['role']) ?></span><?php endif; ?>
            </span>
        </figcaption>
    <?php endif; ?>
</figure>
