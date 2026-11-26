<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\inventory\AdjustmentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$adjustmentStatusFilter = require __DIR__ . '/../_partials/adjustment-status-filter.php';

$this->title = 'Корректировки';

echo $this->render('/_partials/inventory-crud-index', [
    'title' => $this->title,
    'createLabel' => '+ Корректировка',
    'pjaxId' => 'adjustment-pjax',
    'emptyText' => 'Корректировок пока нет',
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'gridColumns' => [
        ['attribute' => 'id', 'headerOptions' => ['style' => 'width:70px']],
        'doc_number',
        [
            'attribute' => 'doc_date',
            'format' => ['date', 'php:d.m.Y'],
            'filter' => false,
        ],
        [
            'attribute' => 'status',
            'filter' => $adjustmentStatusFilter,
            'value' => static fn ($model) => $adjustmentStatusFilter[$model->status] ?? $model->status,
        ],
        'warehouse_id',
    ],
]);