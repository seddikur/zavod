<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $adjustment_id
 * @property int $material_id
 * @property string $quantity
 * @property int $unit_id
 */
class AdjustmentItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%adjustment_items}}';
    }

    public function rules(): array
    {
        return [
            [['adjustment_id', 'material_id', 'quantity', 'unit_id'], 'required'],
            [['adjustment_id', 'material_id', 'unit_id'], 'integer'],
            [['quantity'], 'number'],
            ['quantity', 'compare', 'compareValue' => 0, 'operator' => '!=', 'message' => 'Количество не может быть 0.'],
        ];
    }
}