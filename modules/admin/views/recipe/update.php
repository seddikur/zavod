<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Recipe $model */

$this->title = 'Изменить рецепт #' . $model->id;

echo $this->render('/_partials/inventory-entity-form-page', [
    'title' => $this->title,
    'model' => $model,
    'formView' => '_form',
    'formParams' => compact('model', 'itemRows', 'materials', 'units', 'warehouses', 'defaultKgUnitId'),
]);