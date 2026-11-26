<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$t = Yii::$app->params['site'];
$this->title = $t['cases']['title'] . ' — ' . $t['common']['brand'];
$this->params['meta_description'] = $t['cases']['subtitle'];
$this->params['fullWidth'] = true;
?>
<?= $this->render('_hero', [
    'hero' => $t['cases']['hero'],
    'ctaSecondaryHref' => '#cases',
]) ?>
<section class="section section--alt" id="cases">
    <div class="container">
        <header class="section__head">
            <h2><?= Html::encode($t['cases']['title']) ?></h2>
            <p><?= Html::encode($t['cases']['subtitle']) ?></p>
        </header>
        <div class="grid grid--cases">
            <?php foreach ($t['cases']['items'] as $case): ?>
                <article class="card case">
                    <div class="case__tag"><?= Html::encode($case['tag']) ?></div>
                    <h3><?= Html::encode($case['title']) ?></h3>
                    <p class="case__task"><?= Html::encode($case['task']) ?></p>
                    <p class="case__result">
                        <strong><?= Html::encode($t['common']['resultLabel']) ?></strong>
                        <?= Html::encode($case['result']) ?>
                    </p>
                </article>
            <?php endforeach ?>
        </div>
    </div>
</section>
