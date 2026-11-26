<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\inventory\ProductionOrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$documentStatusFilter = require __DIR__ . '/../_partials/document-status-filter.php';

$this->title = 'Отгрузки';

echo $this->render('/_partials/inventory-crud-index', [
    'title' => $this->title,
    'createLabel' => '+ Отгрузка',
    'pjaxId' => 'production-order-pjax',
    'emptyText' => 'Отгрузок пока нет',
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'gridColumns' => [
        ['attribute' => 'id', 'headerOptions' => ['style' => 'width:70px']],
        'order_number',
        [
            'attribute' => 'doc_date',
            'format' => ['date', 'php:d.m.Y'],
            'filter' => false,
        ],
        [
            'attribute' => 'status',
            'filter' => $documentStatusFilter,
            'value' => static fn ($model) => $documentStatusFilter[$model->status] ?? $model->status,
        ],
        [
            'attribute' => 'actual_quantity',
            'filter' => false,
        ],
    ],
]);