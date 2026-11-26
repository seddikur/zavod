<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $model */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Пользователь #' . $model->id;
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'subtitle' => $model->getFullName(),
    'actions' => [
        ['label' => 'К списку', 'url' => ['index']],
        ['label' => 'Изменить', 'url' => ['update', 'id' => $model->id], 'options' => ['class' => 'cc-btn-primary']],
        [
            'label' => 'Удалить',
            'url' => ['delete', 'id' => $model->id],
            'options' => [
                'class' => 'cc-btn-danger',
                'data' => [
                    'confirm' => 'Пометить пользователя как удалённого?',
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
            'phone',
            'email',
            'first_name',
            'last_name',
            [
                'attribute' => 'status',
                'value' => $model->getStatusLabel(),
            ],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d.m.Y H:i'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['datetime', 'php:d.m.Y H:i'],
            ],
        ],
    ]) ?>
</div>
<?php $this->endContent(); ?>
