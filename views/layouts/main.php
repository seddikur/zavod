<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use app\widgets\Noty;
use yii\helpers\Html;

$this->render('_head');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Html::encode(Yii::$app->language) ?>">
<head>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title) ?></title>
</head>
<body>
<?php $this->beginBody() ?>

<?php if (empty($this->params['authLayout'])): ?>
    <?= $this->render('_header') ?>
<?php endif ?>

<main id="main" role="main">
    <?= $content ?>
</main>

<?php if (empty($this->params['authLayout'])): ?>
    <?= $this->render('_footer') ?>
<?php endif ?>

<?= Noty::widget() ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
