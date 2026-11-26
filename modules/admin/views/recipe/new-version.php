<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Recipe $model */
/** @var int $fromId */

$this->title = 'Новая версия рецепта';

echo $this->render('/_partials/page-header', [
    'title' => $this->title,
    'subtitle' => 'На основе #' . $fromId,
    'actions' => [
        ['label' => 'К списку', 'url' => ['index']],
        ['label' => 'Просмотр исходника', 'url' => ['view', 'id' => $fromId]],
    ],
]);

$this->beginContent('@app/modules/admin/views/_partials/crud-card.php');
echo $this->render('_form', compact('model', 'itemRows', 'materials', 'units', 'warehouses', 'defaultKgUnitId'));
$this->endContent();