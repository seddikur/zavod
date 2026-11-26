<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$t = Yii::$app->params['site'];
$this->title = $t['faq']['title'] . ' — ' . $t['common']['brand'];
$this->params['meta_description'] = $t['faq']['subtitle'];
$this->params['fullWidth'] = true;
?>
<?= $this->render('_hero', [
    'hero' => $t['faq']['hero'],
    'ctaSecondaryHref' => '#faq',
]) ?>
<section class="section" id="faq">
    <div class="container container--split">
        <div class="faq">
            <header class="section__head section__head--left">
                <h2><?= Html::encode($t['faq']['title']) ?></h2>
                <p><?= Html::encode($t['faq']['subtitle']) ?></p>
            </header>
            <?php foreach ($t['faq']['items'] as $item): ?>
                <details class="faq__item"<?= !empty($item['open']) ? ' open' : '' ?>>
                    <summary><?= Html::encode($item['q']) ?></summary>
                    <p><?= Html::encode($item['a']) ?></p>
                </details>
            <?php endforeach ?>
        </div>
        <?= $this->render('_contacts') ?>
    </div>
</section>
