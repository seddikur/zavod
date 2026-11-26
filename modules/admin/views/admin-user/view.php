<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\AdminUser $model */

use yii\widgets\DetailView;

$this->title = 'Администратор: ' . $model->username;
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'actions' => [
        ['label' => 'К списку', 'url' => ['index']],
        ['label' => 'Изменить', 'url' => ['update', 'id' => $model->id], 'options' => ['class' => 'cc-btn-primary']],
        [
            'label' => 'Отключить',
            'url' => ['delete', 'id' => $model->id],
            'options' => [
                'class' => 'cc-btn-danger',
                'data' => [
                    'confirm' => 'Отключить администратора?',
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
            'username',
            [
                'attribute' => 'role',
                'value' => $model->getRoleLabel(),
            ],
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
            [
                'attribute' => 'last_login_at',
                'format' => ['datetime', 'php:d.m.Y H:i'],
            ],
            'last_login_ip',
        ],
    ]) ?>
</div>
<?php $this->endContent(); ?>
