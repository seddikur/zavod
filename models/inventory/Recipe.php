<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $output_quantity
 * @property int $output_unit_id
 * @property string $recipe_date
 * @property bool $is_active
 * @property string $created_at
 */
class Recipe extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%recipes}}';
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'code' => 'Код рецепта',
            'name' => 'Название смеси',
            'output_quantity' => 'База расчёта (кг)',
            'output_unit_id' => 'Ед. базы',
            'recipe_date' => 'Дата рецепта',
            'is_active' => 'Активен',
            'created_at' => 'Создан',
        ];
    }

    public function rules(): array
    {
        return [
            [['code', 'name', 'output_quantity', 'output_unit_id', 'recipe_date'], 'required'],
            [['output_unit_id'], 'integer'],
            [['output_quantity'], 'number', 'min' => 0.01],
            [['recipe_date'], 'date', 'format' => 'php:Y-m-d'],
            [['is_active'], 'boolean'],
            [['code'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 200],
        ];
    }

    public function getItems(): \yii\db\ActiveQuery
    {
        return $this->hasMany(RecipeItem::class, ['recipe_id' => 'id']);
    }

    /** Текущая (активная) версия по коду — см. RecipeVersionService. */
    public function isCurrentVersion(): bool
    {
        return (bool) $this->is_active;
    }
}