<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Supplier $model */

$this->title = 'Новый поставщик';

echo $this->render('/_partials/inventory-entity-form-page', [
    'title' => $this->title,
    'model' => $model,
    'formView' => '_form',
]);