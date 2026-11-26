<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\AdminLoginLog $model */

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Вход #' . $model->id;
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'actions' => [
        ['label' => 'К списку', 'url' => ['index']],
        [
            'label' => 'Удалить',
            'url' => ['delete', 'id' => $model->id],
            'options' => [
                'class' => 'cc-btn-danger',
                'data' => [
                    'confirm' => 'Удалить запись журнала?',
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
            'admin_user_id',
            'username',
            'ip',
            [
                'attribute' => 'user_agent',
                'format' => 'ntext',
            ],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d.m.Y H:i:s'],
            ],
        ],
    ]) ?>
</div>
<?php $this->endContent(); ?>
