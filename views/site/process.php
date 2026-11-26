<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$t = Yii::$app->params['site'];
$this->title = $t['process']['title'] . ' — ' . $t['common']['brand'];
$this->params['meta_description'] = $t['process']['subtitle'];
$this->params['fullWidth'] = true;
?>
<?= $this->render('_hero', [
    'hero' => $t['process']['hero'],
    'ctaSecondaryHref' => '#process',
]) ?>
<section class="section section--alt" id="process">
    <div class="container">
        <header class="section__head">
            <h2><?= Html::encode($t['process']['title']) ?></h2>
            <p><?= Html::encode($t['process']['subtitle']) ?></p>
        </header>
        <ol class="steps">
            <?php foreach ($t['process']['steps'] as $step): ?>
                <li class="step">
                    <span class="step__num"><?= Html::encode($step['num']) ?></span>
                    <h3><?= Html::encode($step['title']) ?></h3>
                    <p><?= Html::encode($step['text']) ?></p>
                </li>
            <?php endforeach ?>
        </ol>
        <div class="tech">
            <span class="tech__label"><?= Html::encode($t['process']['techLabel']) ?></span>
            <?php foreach ($t['process']['tech'] as $tech): ?>
                <span class="chip"><?= Html::encode($tech) ?></span>
            <?php endforeach ?>
        </div>
    </div>
</section>
