<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$t = Yii::$app->params['site'];
?>
<footer class="footer">
    <div class="container footer__inner">
        <?= $this->render('_logo', [
            'small' => true,
            'brand' => $t['common']['brand'],
        ]) ?>
        <p class="footer__copy"><?= Html::encode($t['footer']['copy']) ?></p>
        <nav class="footer__nav" aria-label="Футер">
            <?php foreach ($t['footer']['nav'] as $item): ?>
                <a href="<?= Html::encode(Url::to($item['route'])) ?>">
                    <?= Html::encode($item['label']) ?>
                </a>
            <?php endforeach ?>
        </nav>
    </div>
</footer>
