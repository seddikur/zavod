<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Unit $model */

$this->title = $model->name;

echo $this->render('/_partials/inventory-entity-view', [
    'title' => $this->title,
    'subtitle' => $model->short_name,
    'model' => $model,
    'deleteConfirm' => 'Удалить единицу измерения?',
    'detailAttributes' => ['id', 'name', 'short_name', 'base_unit_id', 'factor'],
]);