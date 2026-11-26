<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\ProductionOrder $model */
/** @var app\models\inventory\ProductionConsumption[] $consumptions */

use app\enums\DocumentStatus;
use yii\widgets\DetailView;

$documentStatusFilter = require __DIR__ . '/../_partials/document-status-filter.php';

$this->title = 'Отгрузка ' . $model->order_number;

$actions = [['label' => 'К списку', 'url' => ['index']]];
if ($model->isDraft()) {
    $actions[] = ['label' => 'Изменить', 'url' => ['update', 'id' => $model->id], 'options' => ['class' => 'cc-btn-primary']];
    $actions[] = [
        'label' => 'Удалить',
        'url' => ['delete', 'id' => $model->id],
        'options' => [
            'class' => 'cc-btn-danger',
            'data' => ['confirm' => 'Удалить черновик отгрузки?', 'method' => 'post'],
        ],
    ];
    $actions[] = ['label' => 'Провести', 'url' => ['post', 'id' => $model->id], 'options' => ['class' => 'cc-btn-primary', 'data' => ['method' => 'post']]];
}
$canReverse = $model->status === DocumentStatus::Posted->value && $model->reversed_by === null;
?>

<?= $this->render('/_partials/page-header', ['title' => $this->title, 'actions' => $actions]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', ['padded' => false]); ?>
<div class="cc-detail">
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-bordered mb-0'],
        'attributes' => [
            'id',
            'order_number',
            ['attribute' => 'doc_date', 'format' => ['date', 'php:d.m.Y']],
            [
                'attribute' => 'status',
                'value' => $documentStatusFilter[$model->status] ?? $model->status,
            ],
            'planned_quantity',
            'actual_quantity',
            ['attribute' => 'posted_at', 'format' => ['datetime', 'php:d.m.Y H:i']],
        ],
    ]) ?>
</div>
<?php if ($consumptions !== []): ?>
    <h3 class="h6 mt-4 px-3">Списание</h3>
    <ul class="list-group mx-3 mb-3">
        <?php foreach ($consumptions as $row): ?>
            <li class="list-group-item">
                Материал #<?= (int) $row->material_id ?>,
                склад #<?= (int) $row->warehouse_id ?>:
                <?= htmlspecialchars((string) $row->quantity, ENT_QUOTES, 'UTF-8') ?> кг
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php if ($canReverse): ?>
    <div class="px-3 pb-3">
        <?= $this->render('/_partials/reverse-form', ['action' => ['reverse', 'id' => $model->id]]) ?>
    </div>
<?php endif; ?>
<?php $this->endContent(); ?>