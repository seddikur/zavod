<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $hero */
/** @var string $ctaSecondaryHref */

use yii\helpers\Html;
use yii\helpers\Url;

$ctaSecondaryHref = $ctaSecondaryHref ?? '#';
?>
<section class="hero">
    <div class="container hero__inner">
        <div class="hero__content">
            <p class="eyebrow"><span class="dot"></span> <?= Html::encode($hero['eyebrow']) ?></p>
            <h1><?= Html::encode($hero['h1']) ?></h1>
            <p class="hero__lead"><?= Html::encode($hero['lead']) ?></p>
            <div class="hero__actions">
                <a href="<?= Html::encode(Url::to(['/site/index', '#' => 'contacts'])) ?>" class="btn btn--accent btn--lg">
                    <?= Html::encode($hero['ctaPrimary']) ?>
                </a>
                <a href="<?= Html::encode($ctaSecondaryHref) ?>" class="btn btn--ghost btn--lg">
                    <?= Html::encode($hero['ctaSecondary']) ?>
                </a>
            </div>
            <ul class="hero__stats">
                <?php foreach ($hero['stats'] as $stat): ?>
                    <li>
                        <strong><?= Html::encode($stat['value']) ?></strong>
                        <span><?= Html::encode($stat['label']) ?></span>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="hero__panel" aria-hidden="true">
            <div class="panel__row">
                <span class="panel__dot panel__dot--accent"></span>
                <span><?= Html::encode($hero['panel']['row1']) ?></span>
            </div>
            <div class="panel__row">
                <span class="panel__dot"></span>
                <span><?= Html::encode($hero['panel']['row2']) ?></span>
            </div>
            <div class="panel__row">
                <span class="panel__dot panel__dot--blue"></span>
                <span><?= Html::encode($hero['panel']['row3']) ?></span>
            </div>
            <div class="panel__grid">
                <span></span><span></span><span></span>
                <span></span><span class="is-accent"></span><span></span>
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
</section>
