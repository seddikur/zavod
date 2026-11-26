<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\inventory\RecipeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Рецепты';

echo $this->render('/_partials/inventory-crud-index', [
    'title' => $this->title,
    'subtitle' => 'Версии составов смеси',
    'createLabel' => '+ Рецепт',
    'pjaxId' => 'recipe-pjax',
    'emptyText' => 'Рецептов пока нет',
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'gridColumns' => [
        ['attribute' => 'id', 'headerOptions' => ['style' => 'width:70px']],
        'code',
        'name',
        [
            'attribute' => 'recipe_date',
            'format' => ['date', 'php:d.m.Y'],
            'filter' => false,
        ],
        'is_active:boolean',
    ],
]);