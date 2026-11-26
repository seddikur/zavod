<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $category_id
 * @property int $unit_id
 * @property int|null $default_warehouse_id
 * @property bool $is_active
 * @property string $created_at
 */
class Material extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%materials}}';
    }

    public function rules(): array
    {
        return [
            [['code', 'name', 'category_id', 'unit_id'], 'required'],
            [['category_id', 'unit_id', 'default_warehouse_id'], 'integer'],
            [['is_active'], 'boolean'],
            [['code'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 200],
            [['code'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'code' => 'Код',
            'name' => 'Название',
            'category_id' => 'Категория',
            'unit_id' => 'Единица',
            'default_warehouse_id' => 'Склад по умолчанию',
            'is_active' => 'Активен',
            'created_at' => 'Создан',
        ];
    }
}