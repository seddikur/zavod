<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Новый пользователь';
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'actions' => [
        ['label' => 'К списку', 'url' => ['index']],
    ],
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php'); ?>
<?= $this->render('_form', ['model' => $model]) ?>
<?php $this->endContent(); ?>
