<?php

declare(strict_types=1);

namespace app\models\inventory;

use app\enums\AdjustmentStatus;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $doc_number
 * @property string $doc_date
 * @property int $warehouse_id
 * @property string $reason
 * @property string $status
 * @property int $created_by
 * @property string $created_at
 * @property string|null $posted_at
 */
class Adjustment extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%adjustments}}';
    }

    public function rules(): array
    {
        return [
            [['doc_number', 'doc_date', 'warehouse_id', 'reason', 'created_by'], 'required'],
            [['warehouse_id', 'created_by'], 'integer'],
            [['doc_date'], 'date', 'format' => 'php:Y-m-d'],
            [['reason'], 'string'],
            [['status'], 'in', 'range' => array_column(AdjustmentStatus::cases(), 'value')],
            [['doc_number'], 'string', 'max' => 50],
        ];
    }

    public function getItems(): \yii\db\ActiveQuery
    {
        return $this->hasMany(AdjustmentItem::class, ['adjustment_id' => 'id']);
    }

    public function isDraft(): bool
    {
        return $this->status === AdjustmentStatus::Draft->value;
    }

    public function isPosted(): bool
    {
        return $this->status === AdjustmentStatus::Posted->value;
    }
}