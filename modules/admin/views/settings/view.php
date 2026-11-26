<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Setting $model */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Настройка: ' . $model->key;
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'actions' => [
        ['label' => 'К списку', 'url' => ['index']],
        ['label' => 'Изменить', 'url' => ['update', 'id' => $model->id], 'options' => ['class' => 'cc-btn-primary']],
        [
            'label' => 'Удалить',
            'url' => ['delete', 'id' => $model->id],
            'options' => [
                'class' => 'cc-btn-danger',
                'data' => [
                    'confirm' => 'Удалить настройку?',
                    'method' => 'post',
                ],
            ],
        ],
    ],
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', ['padded' => false]); ?>
<div class="cc-detail">
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-bordered mb-0'],
        'attributes' => [
            'id',
            'key',
            [
                'attribute' => 'value',
                'format' => 'ntext',
            ],
            'description',
            'created_at',
            'updated_at',
        ],
    ]) ?>
</div>
<?php $this->endContent(); ?>
