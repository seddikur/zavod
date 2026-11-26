<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$t = Yii::$app->params['site'];
$this->title = $t['prices']['title'] . ' — ' . $t['common']['brand'];
$this->params['meta_description'] = $t['prices']['subtitle'];
$this->params['fullWidth'] = true;
?>
<?= $this->render('_hero', [
    'hero' => $t['prices']['hero'],
    'ctaSecondaryHref' => '#prices',
]) ?>
<section class="section" id="prices">
    <div class="container">
        <header class="section__head">
            <h2><?= Html::encode($t['prices']['title']) ?></h2>
            <p><?= Html::encode($t['prices']['subtitle']) ?></p>
        </header>
        <div class="grid grid--prices">
            <?php foreach ($t['prices']['items'] as $price): ?>
                <div class="card price<?= !empty($price['featured']) ? ' price--featured' : '' ?>">
                    <?php if (!empty($price['badge'])): ?>
                        <div class="price__badge"><?= Html::encode($price['badge']) ?></div>
                    <?php endif ?>
                    <h3><?= Html::encode($price['title']) ?></h3>
                    <p class="price__value"><?= Html::encode($price['value']) ?></p>
                    <p class="price__note"><?= Html::encode($price['note']) ?></p>
                    <a href="<?= Html::encode(Url::to(['/site/index', '#' => 'contacts'])) ?>"
                       class="btn <?= !empty($price['featured']) ? 'btn--accent' : 'btn--ghost' ?>">
                        <?= Html::encode($t['common']['discuss']) ?>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>
