<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Receipt $model */

$this->title = 'Новый приход';

echo $this->render('/_partials/inventory-entity-form-page', [
    'title' => $this->title,
    'model' => $model,
    'formView' => '_form',
    'formParams' => compact('model', 'itemRows', 'materials', 'units', 'suppliers', 'warehouses'),
]);