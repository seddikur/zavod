<?php

declare(strict_types=1);

namespace app\models\inventory;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $receipt_id
 * @property int $material_id
 * @property string $quantity
 * @property int $unit_id
 * @property string $price
 * @property string $amount
 */
class ReceiptItem extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%receipt_items}}';
    }

    public function rules(): array
    {
        return [
            [['receipt_id', 'material_id', 'quantity', 'unit_id'], 'required'],
            [['receipt_id', 'material_id', 'unit_id'], 'integer'],
            [['quantity', 'price', 'amount'], 'number'],
        ];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate() && $this->quantity !== null && $this->price !== null) {
            $this->amount = bcmul((string) $this->quantity, (string) $this->price, 2);
        }

        return true;
    }
}