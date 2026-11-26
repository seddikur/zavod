<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\inventory\SupplierSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Поставщики';

echo $this->render('/_partials/inventory-crud-index', [
    'title' => $this->title,
    'pjaxId' => 'supplier-pjax',
    'emptyText' => 'Поставщиков пока нет',
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'gridColumns' => [
        ['attribute' => 'id', 'headerOptions' => ['style' => 'width:70px']],
        'name',
        'inn',
        'is_active:boolean',
    ],
]);