<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Recipe $model */
/** @var app\models\inventory\RecipeItem[] $items */

use app\services\inventory\RecipeBatchConfig;
use yii\bootstrap5\Html;

$this->title = 'Рецепт ' . $model->code;

$afterDetail = '<h3 class="h6 mt-4">Состав</h3><ul class="list-group">';
foreach ($items as $item) {
    $line = '#' . (int) $item->material_id . ' — ' . Html::encode($item->quantity) . ' кг';
    if ($item->warehouse_id) {
        $line .= ' (склад #' . (int) $item->warehouse_id . ')';
    }
    $afterDetail .= '<li class="list-group-item">' . $line . '</li>';
}
$afterDetail .= '</ul>';

echo $this->render('/_partials/inventory-entity-view', [
    'title' => $this->title,
    'subtitle' => $model->name,
    'model' => $model,
    'deleteConfirm' => 'Удалить рецепт?',
    'extraActions' => [
        ['label' => 'Новая версия', 'url' => ['new-version', 'id' => $model->id], 'options' => ['class' => 'cc-btn']],
    ],
    'detailAttributes' => [
        'id',
        'code',
        'name',
        [
            'label' => 'База расчёта состава',
            'value' => RecipeBatchConfig::batchKgLabel(),
        ],
        [
            'attribute' => 'recipe_date',
            'format' => ['date', 'php:d.m.Y'],
        ],
        'is_active:boolean',
    ],
    'afterDetail' => $afterDetail,
]);