<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\inventory\UnitSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Единицы измерения';

echo $this->render('/_partials/inventory-crud-index', [
    'title' => $this->title,
    'pjaxId' => 'unit-pjax',
    'emptyText' => 'Единиц измерения пока нет',
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'gridColumns' => [
        ['attribute' => 'id', 'headerOptions' => ['style' => 'width:70px']],
        'name',
        'short_name',
    ],
]);