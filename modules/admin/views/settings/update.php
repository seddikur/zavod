<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Setting $model */

$this->title = 'Изменить: ' . $model->key;
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'actions' => [
        ['label' => 'К списку', 'url' => ['index']],
        ['label' => 'Просмотр', 'url' => ['view', 'id' => $model->id]],
    ],
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php'); ?>
<?= $this->render('_form', ['model' => $model]) ?>
<?php $this->endContent(); ?>
