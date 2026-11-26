<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Adjustment $model */
/** @var app\models\inventory\AdjustmentItem[] $items */

use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$adjustmentStatusFilter = require __DIR__ . '/../_partials/adjustment-status-filter.php';

$this->title = 'Корректировка ' . $model->doc_number;

$actions = [['label' => 'К списку', 'url' => ['index']]];
if ($model->isDraft()) {
    $actions[] = ['label' => 'Изменить', 'url' => ['update', 'id' => $model->id], 'options' => ['class' => 'cc-btn-primary']];
    $actions[] = [
        'label' => 'Удалить',
        'url' => ['delete', 'id' => $model->id],
        'options' => [
            'class' => 'cc-btn-danger',
            'data' => ['confirm' => 'Удалить черновик корректировки?', 'method' => 'post'],
        ],
    ];
    $actions[] = ['label' => 'Провести', 'url' => ['post', 'id' => $model->id], 'options' => ['class' => 'cc-btn-primary', 'data' => ['method' => 'post']]];
}
if ($model->isPosted()) {
    $actions[] = [
        'label' => 'Компенсировать',
        'url' => ['compensate', 'id' => $model->id],
        'options' => [
            'class' => 'cc-btn',
            'data' => [
                'method' => 'post',
                'confirm' => 'Создать противоположную корректировку и провести?',
            ],
        ],
    ];
}
?>

<?= $this->render('/_partials/page-header', ['title' => $this->title, 'actions' => $actions]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', ['padded' => false]); ?>
<div class="cc-detail">
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-bordered mb-0'],
        'attributes' => [
            'id',
            'doc_number',
            ['attribute' => 'doc_date', 'format' => ['date', 'php:d.m.Y']],
            'warehouse_id',
            [
                'attribute' => 'status',
                'value' => $adjustmentStatusFilter[$model->status] ?? $model->status,
            ],
            'reason:ntext',
            ['attribute' => 'posted_at', 'format' => ['datetime', 'php:d.m.Y H:i']],
        ],
    ]) ?>
</div>
<ul class="list-group mt-4 mx-3 mb-3">
    <?php foreach ($items as $item): ?>
        <li class="list-group-item">Материал #<?= (int) $item->material_id ?>: <?= Html::encode($item->quantity) ?> кг</li>
    <?php endforeach; ?>
</ul>
<?php $this->endContent(); ?>