<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\PageView $model */

use yii\widgets\DetailView;

$this->title = 'Просмотр #' . $model->id;
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'subtitle' => (string) $model->path,
    'actions' => [
        ['label' => 'К списку', 'url' => ['index']],
        [
            'label' => 'Удалить',
            'url' => ['delete', 'id' => $model->id],
            'options' => [
                'class' => 'cc-btn-danger',
                'data' => [
                    'confirm' => 'Удалить запись просмотра?',
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
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d.m.Y H:i:s'],
            ],
            'path',
            'route',
            [
                'attribute' => 'query_string',
                'format' => 'ntext',
            ],
            'utm_source',
            'utm_medium',
            [
                'attribute' => 'referrer',
                'format' => 'ntext',
            ],
            'visitor_id',
            'ip',
            [
                'attribute' => 'user_agent',
                'format' => 'ntext',
            ],
            [
                'attribute' => 'is_bot',
                'value' => $model->is_bot ? 'Да' : 'Нет',
            ],
        ],
    ]) ?>
</div>
<?php $this->endContent(); ?>
