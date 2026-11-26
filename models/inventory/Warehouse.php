<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property bool $is_active
 */
class Warehouse extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%warehouses}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'is_active' => 'Активен',
        ];
    }
}