<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\inventory\MaterialCategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Категории материалов';

echo $this->render('/_partials/inventory-crud-index', [
    'title' => $this->title,
    'pjaxId' => 'material-category-pjax',
    'emptyText' => 'Категорий пока нет',
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'gridColumns' => [
        ['attribute' => 'id', 'headerOptions' => ['style' => 'width:70px']],
        'name',
    ],
]);