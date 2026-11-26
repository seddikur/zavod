<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$t = Yii::$app->params['site'];
$this->title = $t['meta']['title'];
$this->params['meta_description'] = $t['meta']['description'];
$this->params['fullWidth'] = true;
?>

<?= $this->render('_hero', [
    'hero' => $t['hero'],
    'ctaSecondaryHref' => '#services',
]) ?>

<section class="section" id="services">
    <div class="container">
        <header class="section__head">
            <h2><?= Html::encode($t['services']['title']) ?></h2>
            <p><?= Html::encode($t['services']['subtitle']) ?></p>
        </header>
        <div class="grid grid--services">
            <?php foreach ($t['services']['items'] as $item): ?>
                <a href="<?= Html::encode(Url::to(['/site/index', '#' => 'contacts'])) ?>" class="card card--link">
                    <span class="card__icon" aria-hidden="true"><?= Html::encode($item['num']) ?></span>
                    <h3><?= Html::encode($item['title']) ?></h3>
                    <p><?= Html::encode($item['text']) ?></p>
                    <span class="card__more"><?= Html::encode($t['common']['more']) ?></span>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</section>

<section class="section" id="advantages">
    <div class="container">
        <header class="section__head">
            <h2><?= Html::encode($t['advantages']['title']) ?></h2>
            <p><?= Html::encode($t['advantages']['subtitle']) ?></p>
        </header>
        <div class="grid grid--adv">
            <?php foreach ($t['advantages']['items'] as $item): ?>
                <div class="card adv">
                    <span class="adv__num"><?= Html::encode($item['num']) ?></span>
                    <h3><?= Html::encode($item['title']) ?></h3>
                    <p><?= Html::encode($item['text']) ?></p>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<section class="section section--alt" id="reviews">
    <div class="container">
        <header class="section__head">
            <h2><?= Html::encode($t['reviews']['title']) ?></h2>
            <p><?= Html::encode($t['reviews']['subtitle']) ?></p>
        </header>
        <div class="grid grid--reviews">
            <?php foreach ($t['reviews']['items'] as $review): ?>
                <blockquote class="card review">
                    <p><?= Html::encode($review['text']) ?></p>
                    <footer><?= Html::encode($review['author']) ?></footer>
                </blockquote>
            <?php endforeach ?>
        </div>
        <div class="logos" aria-label="Клиенты">
            <?php foreach ($t['reviews']['logos'] as $logo): ?>
                <span><?= Html::encode($logo) ?></span>
            <?php endforeach ?>
        </div>
    </div>
</section>

<section class="section" id="faq-contacts">
    <div class="container container--split">
        <div class="faq">
            <header class="section__head section__head--left">
                <h2><?= Html::encode($t['faq']['title']) ?></h2>
                <p><?= Html::encode($t['faq']['subtitle']) ?></p>
            </header>
            <?php foreach (array_slice($t['faq']['items'], 0, 2) as $item): ?>
                <details class="faq__item"<?= !empty($item['open']) ? ' open' : '' ?>>
                    <summary><?= Html::encode($item['q']) ?></summary>
                    <p><?= Html::encode($item['a']) ?></p>
                </details>
            <?php endforeach ?>
            <p style="margin-top: var(--space-4);">
                <a href="<?= Html::encode(Url::to(['/site/faq'])) ?>" class="card__more">
                    Все вопросы →
                </a>
            </p>
        </div>
        <?= $this->render('_contacts') ?>
    </div>
</section>
