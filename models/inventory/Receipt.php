<?php

declare(strict_types=1);

namespace app\models\inventory;

use app\enums\DocumentStatus;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $doc_number
 * @property string $doc_date
 * @property int $supplier_id
 * @property int $warehouse_id
 * @property string $status
 * @property int|null $reversal_of
 * @property int|null $reversed_by
 * @property string|null $reversal_reason
 * @property string|null $total_amount
 * @property int $created_by
 * @property string $created_at
 * @property string|null $posted_at
 */
class Receipt extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%receipts}}';
    }

    public function rules(): array
    {
        return [
            [['doc_number', 'doc_date', 'supplier_id', 'warehouse_id', 'created_by'], 'required'],
            [['supplier_id', 'warehouse_id', 'created_by', 'reversal_of', 'reversed_by'], 'integer'],
            [['doc_date'], 'date', 'format' => 'php:Y-m-d'],
            [['status'], 'in', 'range' => array_column(DocumentStatus::cases(), 'value')],
            [['doc_number'], 'string', 'max' => 50],
            [['total_amount'], 'number'],
        ];
    }

    public function getItems(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ReceiptItem::class, ['receipt_id' => 'id']);
    }

    public function isDraft(): bool
    {
        return $this->status === DocumentStatus::Draft->value;
    }
}