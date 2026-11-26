<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\MaterialCategory $model */

$this->title = 'Новая категория';

echo $this->render('/_partials/inventory-entity-form-page', [
    'title' => $this->title,
    'model' => $model,
    'formView' => '_form',
]);