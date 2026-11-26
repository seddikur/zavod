<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\inventory\MaterialStockSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Остатки';

echo $this->render('/_partials/inventory-crud-index', [
    'title' => $this->title,
    'subtitle' => 'Агрегат material_stock',
    'pjaxId' => 'material-stock-pjax',
    'emptyText' => 'Остатков нет',
    'showCreate' => false,
    'crudActions' => [],
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'gridColumns' => [
        'material_id',
        'warehouse_id',
        [
            'attribute' => 'quantity',
            'format' => ['decimal', 2],
        ],
        [
            'attribute' => 'updated_at',
            'format' => ['datetime', 'php:d.m.Y H:i'],
            'filter' => false,
        ],
    ],
]);