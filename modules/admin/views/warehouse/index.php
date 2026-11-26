<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\inventory\WarehouseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Склады';

echo $this->render('/_partials/inventory-crud-index', [
    'title' => $this->title,
    'pjaxId' => 'warehouse-pjax',
    'emptyText' => 'Складов пока нет',
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'gridColumns' => [
        ['attribute' => 'id', 'headerOptions' => ['style' => 'width:70px']],
        'name',
        'is_active:boolean',
    ],
]);