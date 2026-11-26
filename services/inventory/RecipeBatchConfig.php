<?php

declare(strict_types=1);

namespace app\services\inventory;

use app\models\inventory\Recipe;
use app\models\inventory\Unit;
use Yii;

/**
 * База расчёта состава рецепта (кг на одну «паспортную» партию) — из config/inventory.php.
 */
final class RecipeBatchConfig
{
    public static function batchKg(): string
    {
        $value = Yii::$app->params['inventory']['recipeComponentBatchKg'] ?? '1000';

        return (string) $value;
    }

    public static function batchKgLabel(): string
    {
        $n = rtrim(rtrim(self::batchKg(), '0'), '.');

        return $n . ' кг';
    }

    public static function kgUnitId(): int
    {
        $id = Unit::find()->select('id')->where(['short_name' => 'кг'])->scalar();
        if ($id !== false && $id !== null) {
            return (int) $id;
        }
        $id = Unit::find()->select('id')->orderBy(['id' => SORT_ASC])->scalar();

        return (int) $id;
    }

    public static function applyOutputDefaults(Recipe $recipe): void
    {
        $recipe->output_quantity = self::batchKg();
        $recipe->output_unit_id = self::kgUnitId();
    }

    /**
     * @param iterable<\app\models\inventory\RecipeItem> $rows
     */
    public static function validateComponentSum(iterable $rows): ?string
    {
        $sum = '0';
        foreach ($rows as $row) {
            if ($row->material_id === null || $row->quantity === null || $row->quantity === '') {
                continue;
            }
            $sum = bcadd($sum, (string) $row->quantity, InventoryScale::scale());
        }
        $target = self::batchKg();
        if (bccomp($sum, $target, InventoryScale::scale()) !== 0) {
            return 'Сумма материалов по рецепту: ' . $sum . ' кг. Должно быть ровно '
                . self::batchKgLabel() . ' (см. настройку recipeComponentBatchKg).';
        }

        return null;
    }
}