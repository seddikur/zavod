<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Material $model */

$this->title = 'Изменить материал #' . $model->id;

echo $this->render('/_partials/inventory-entity-form-page', [
    'title' => $this->title,
    'model' => $model,
    'formView' => '_form',
    'formParams' => compact('model', 'categories', 'units', 'warehouses'),
]);