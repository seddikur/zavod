<?php

declare(strict_types=1);

/**
 * Построчный состав рецепта (материал + кг на норму выпуска).
 *
 * @var yii\web\View $this
 * @var app\models\inventory\RecipeItem[] $itemRows
 * @var array<int, string> $materials
 * @var array<int, string> $warehouses
 * @var int $defaultKgUnitId
 * @var string|null $recipeBatchLabel
 */

use app\models\inventory\RecipeItem;
use yii\bootstrap5\Html;
use yii\helpers\Json;

$rowPrefix = 'RecipeItem';
$recipeBatchLabel = $recipeBatchLabel ?? null;
$optionsJson = Json::htmlEncode([
    'materials' => $materials,
    'warehouses' => $warehouses,
    'kgUnitId' => $defaultKgUnitId,
]);

$renderRow = static function (
    string $index,
    RecipeItem $row,
    array $materials,
    array $warehouses,
    int $defaultKgUnitId,
    string $rowPrefix,
): string {
    $name = static fn (string $attr): string => $rowPrefix . '[' . $index . '][' . $attr . ']';

    ob_start();
    ?>
    <tr data-cc-item-row>
        <td>
            <?= Html::activeDropDownList(
                $row,
                'material_id',
                $materials,
                [
                    'class' => 'form-select',
                    'name' => $name('material_id'),
                    'prompt' => '— выберите материал —',
                ],
            ) ?>
        </td>
        <td style="max-width: 140px">
            <?= Html::activeTextInput($row, 'quantity', [
                'class' => 'form-control',
                'name' => $name('quantity'),
                'inputmode' => 'decimal',
                'step' => '0.01',
                'placeholder' => '0.00',
            ]) ?>
        </td>
        <td>
            <?= Html::activeDropDownList(
                $row,
                'warehouse_id',
                $warehouses,
                [
                    'class' => 'form-select',
                    'name' => $name('warehouse_id'),
                    'prompt' => '— выберите склад —',
                ],
            ) ?>
        </td>
        <td class="text-end" style="width: 52px">
            <?= Html::button(
                Html::tag('i', 'close', ['class' => 'material-symbols-rounded', 'aria-hidden' => 'true']),
                [
                    'type' => 'button',
                    'class' => 'btn btn-sm btn-link text-danger p-0',
                    'title' => 'Удалить строку',
                    'aria-label' => 'Удалить строку',
                    'data-cc-item-row-remove' => '1',
                ],
            ) ?>
        </td>
        <?= Html::hiddenInput($name('unit_id'), (string) ($row->unit_id ?: $defaultKgUnitId)) ?>
    </tr>
    <?php

    return (string) ob_get_clean();
};

$templateRow = new RecipeItem(['unit_id' => $defaultKgUnitId]);
?>

<div
    class="cc-item-rows"
    data-cc-item-rows="recipe"
    data-cc-item-rows-prefix="<?= Html::encode($rowPrefix) ?>"
    data-cc-item-rows-options="<?= $optionsJson ?>"
>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
        <p class="text-muted small mb-0">
            <?php if ($recipeBatchLabel !== null): ?>
                Килограммы каждого материала на партию <strong><?= Html::encode($recipeBatchLabel) ?></strong>.
            <?php else: ?>
                Килограммы каждого материала на партию из настройки.
            <?php endif; ?>
        </p>
        <?= Html::button('+ Добавить материал', [
            'type' => 'button',
            'class' => 'btn btn-sm cc-btn',
            'data-cc-item-row-add' => '1',
        ]) ?>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead>
            <tr>
                <th>Материал</th>
                <th>Кг</th>
                <th>Склад списания</th>
                <th></th>
            </tr>
            </thead>
            <tbody data-cc-item-rows-body>
            <?php foreach ($itemRows as $i => $row): ?>
                <?= $renderRow((string) $i, $row, $materials, $warehouses, $defaultKgUnitId, $rowPrefix) ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <template data-cc-item-row-tpl>
        <?= $renderRow('__index__', $templateRow, $materials, $warehouses, $defaultKgUnitId, $rowPrefix) ?>
    </template>
</div>