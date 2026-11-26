<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $recipe_id
 * @property int $material_id
 * @property int $warehouse_id
 * @property string $quantity
 * @property int $unit_id
 */
class RecipeItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%recipe_items}}';
    }

    public function attributeLabels(): array
    {
        return [
            'material_id' => 'Материал',
            'quantity' => 'Количество, кг',
            'warehouse_id' => 'Склад списания',
            'unit_id' => 'Единица',
        ];
    }

    public function rules(): array
    {
        return [
            [['recipe_id', 'material_id', 'warehouse_id', 'quantity', 'unit_id'], 'required'],
            [['recipe_id', 'material_id', 'warehouse_id', 'unit_id'], 'integer'],
            [['quantity'], 'number', 'min' => 0.01],
        ];
    }

    public function getMaterial(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Material::class, ['id' => 'material_id']);
    }
}