<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$t = Yii::$app->params['site'];
$action = Yii::$app->controller->action->id ?? '';

$nav = [
    [
        'label' => $t['nav']['services'],
        'url' => Url::to(['/site/index', '#' => 'services']),
        'active' => $action === 'index',
    ],
    [
        'label' => $t['nav']['cases'],
        'url' => Url::to(['/site/cases']),
        'active' => $action === 'cases',
    ],
    [
        'label' => $t['nav']['process'],
        'url' => Url::to(['/site/process']),
        'active' => $action === 'process',
    ],
    [
        'label' => $t['nav']['prices'],
        'url' => Url::to(['/site/prices']),
        'active' => $action === 'prices',
    ],
    [
        'label' => $t['nav']['faq'],
        'url' => Url::to(['/site/faq']),
        'active' => $action === 'faq',
    ],
    [
        'label' => $t['nav']['contacts'],
        'url' => Url::to(['/site/index', '#' => 'contacts']),
        'active' => false,
    ],
];
?>
<header class="header">
    <div class="container header__inner">
        <?= $this->render('_logo', ['brand' => $t['common']['brand']]) ?>
        <nav class="nav" aria-label="Основная навигация">
            <?php foreach ($nav as $item): ?>
                <a href="<?= Html::encode($item['url']) ?>"<?= $item['active'] ? ' class="is-active"' : '' ?>>
                    <?= Html::encode($item['label']) ?>
                </a>
            <?php endforeach ?>
        </nav>
        <div class="header__actions">
            <a href="<?= Html::encode(Url::to(['/site/index', '#' => 'contacts'])) ?>" class="btn btn--accent">
                <?= Html::encode($t['nav']['ctaHeader']) ?>
            </a>
        </div>
    </div>
</header>
