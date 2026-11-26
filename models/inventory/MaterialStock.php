<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $material_id
 * @property int $warehouse_id
 * @property string $quantity
 * @property string $updated_at
 */
class MaterialStock extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%material_stock}}';
    }

    public static function primaryKey(): array
    {
        return ['material_id', 'warehouse_id'];
    }
}