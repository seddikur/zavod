<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Customer $model */

$this->title = $model->name;

echo $this->render('/_partials/inventory-entity-view', [
    'title' => $this->title,
    'model' => $model,
    'deleteConfirm' => 'Удалить заказчика?',
    'detailAttributes' => ['id', 'name', 'inn', 'is_active:boolean'],
]);