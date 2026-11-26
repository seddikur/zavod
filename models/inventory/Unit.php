<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int|null $base_unit_id
 * @property string $factor
 */
class Unit extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%units}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'short_name', 'factor'], 'required'],
            [['base_unit_id'], 'integer'],
            [['factor'], 'number', 'min' => 0.01],
            [['name'], 'string', 'max' => 50],
            [['short_name'], 'string', 'max' => 10],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'short_name' => 'Кратко',
            'base_unit_id' => 'Базовая единица',
            'factor' => 'Коэффициент к базе',
        ];
    }
}