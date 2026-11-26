<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var bool $standalone */

use yii\helpers\Html;

$standalone = $standalone ?? false;
$t = Yii::$app->params['site'];
$c = $t['contacts'];
?>
<?php if ($standalone): ?>
<section class="section section--page" id="contacts">
    <div class="container">
        <header class="section__head">
            <h2><?= Html::encode($c['title']) ?></h2>
            <p><?= Html::encode($c['subtitle']) ?></p>
        </header>
<?php else: ?>
<div class="contacts" id="contacts">
    <header class="section__head section__head--left">
        <h2><?= Html::encode($c['title']) ?></h2>
        <p><?= Html::encode($c['subtitle']) ?></p>
    </header>
<?php endif ?>

    <form class="form" action="#" method="post">
        <label class="field">
            <span><?= Html::encode($c['form']['name']) ?></span>
            <input type="text" name="name" id="form-name" required
                   placeholder="<?= Html::encode($c['form']['namePlaceholder']) ?>" />
        </label>
        <label class="field">
            <span><?= Html::encode($c['form']['contact']) ?></span>
            <input type="text" name="contact" id="form-contact" required
                   placeholder="<?= Html::encode($c['form']['contactPlaceholder']) ?>" />
        </label>
        <label class="field">
            <span><?= Html::encode($c['form']['task']) ?></span>
            <textarea name="task" rows="4" id="form-task"
                      placeholder="<?= Html::encode($c['form']['taskPlaceholder']) ?>"></textarea>
        </label>
        <button type="submit" class="btn btn--accent btn--lg btn--block">
            <?= Html::encode($c['form']['submit']) ?>
        </button>
        <p class="form__note"><?= Html::encode($c['form']['note']) ?></p>
    </form>
    <address class="nap">
        <p><strong><?= Html::encode($c['address']['brand']) ?></strong></p>
        <p><?= Html::encode($c['address']['street']) ?></p>
        <p>
            <a href="tel:<?= Html::encode($c['address']['phoneHref']) ?>">
                <?= Html::encode($c['address']['phone']) ?>
            </a>
        </p>
        <p>
            <a href="mailto:<?= Html::encode($c['address']['email']) ?>">
                <?= Html::encode($c['address']['email']) ?>
            </a>
        </p>
    </address>

<?php if ($standalone): ?>
    </div>
</section>
<?php else: ?>
</div>
<?php endif ?>
