<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $production_order_id
 * @property int $material_id
 * @property int $warehouse_id
 * @property int|null $recipe_item_id
 * @property string $quantity
 * @property int $unit_id
 */
class ProductionConsumption extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%production_consumptions}}';
    }

    public function rules(): array
    {
        return [
            [['production_order_id', 'material_id', 'warehouse_id', 'quantity', 'unit_id'], 'required'],
            [['production_order_id', 'material_id', 'warehouse_id', 'recipe_item_id', 'unit_id'], 'integer'],
            [['quantity'], 'number'],
        ];
    }
}