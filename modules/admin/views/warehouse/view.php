<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Warehouse $model */

$this->title = $model->name;

echo $this->render('/_partials/inventory-entity-view', [
    'title' => $this->title,
    'model' => $model,
    'deleteConfirm' => 'Удалить склад?',
    'detailAttributes' => [
        'id',
        'name',
        'is_active:boolean',
    ],
]);