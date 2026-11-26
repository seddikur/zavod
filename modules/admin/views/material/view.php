<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Material $model */

$this->title = $model->name;

echo $this->render('/_partials/inventory-entity-view', [
    'title' => $this->title,
    'subtitle' => $model->code,
    'model' => $model,
    'deleteConfirm' => 'Удалить материал?',
    'detailAttributes' => [
        'id',
        'code',
        'name',
        'category_id',
        'unit_id',
        'default_warehouse_id',
        'is_active:boolean',
        ['attribute' => 'created_at', 'format' => ['datetime', 'php:d.m.Y H:i']],
    ],
]);