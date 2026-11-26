<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Receipt $model */
/** @var app\models\inventory\ReceiptItem[] $items */

use app\enums\DocumentStatus;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

$documentStatusFilter = require __DIR__ . '/../_partials/document-status-filter.php';

$this->title = 'Приход ' . $model->doc_number;

$actions = [['label' => 'К списку', 'url' => ['index']]];
if ($model->isDraft()) {
    $actions[] = ['label' => 'Изменить', 'url' => ['update', 'id' => $model->id], 'options' => ['class' => 'cc-btn-primary']];
    $actions[] = [
        'label' => 'Удалить',
        'url' => ['delete', 'id' => $model->id],
        'options' => [
            'class' => 'cc-btn-danger',
            'data' => ['confirm' => 'Удалить черновик прихода?', 'method' => 'post'],
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
            'doc_number',
            ['attribute' => 'doc_date', 'format' => ['date', 'php:d.m.Y']],
            [
                'attribute' => 'status',
                'value' => $documentStatusFilter[$model->status] ?? $model->status,
            ],
            'total_amount',
            ['attribute' => 'posted_at', 'format' => ['datetime', 'php:d.m.Y H:i']],
        ],
    ]) ?>
</div>
<ul class="list-group mt-4 mx-3 mb-3">
    <?php foreach ($items as $item): ?>
        <li class="list-group-item">Материал #<?= (int) $item->material_id ?>: <?= Html::encode($item->quantity) ?> кг</li>
    <?php endforeach; ?>
</ul>
<?php if ($canReverse): ?>
    <div class="px-3 pb-3">
        <?= $this->render('/_partials/reverse-form', ['action' => ['reverse', 'id' => $model->id]]) ?>
    </div>
<?php endif; ?>
<?php $this->endContent(); ?>