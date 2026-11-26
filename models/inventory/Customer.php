<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string|null $inn
 * @property bool $is_active
 */
class Customer extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%customers}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 200],
            [['inn'], 'string', 'max' => 12],
            [['is_active'], 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'inn' => 'ИНН',
            'is_active' => 'Активен',
        ];
    }
}