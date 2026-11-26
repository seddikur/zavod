<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\inventory\MaterialSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Материалы';

echo $this->render('/_partials/inventory-crud-index', [
    'title' => $this->title,
    'pjaxId' => 'material-pjax',
    'emptyText' => 'Материалов пока нет',
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'gridColumns' => [
        ['attribute' => 'id', 'headerOptions' => ['style' => 'width:70px']],
        'code',
        'name',
        'category_id',
        'unit_id',
        'is_active:boolean',
    ],
]);