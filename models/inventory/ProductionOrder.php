<?php

declare(strict_types=1);

namespace app\models\inventory;

use app\enums\DocumentStatus;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $order_number
 * @property string $doc_date
 * @property int $customer_id
 * @property int $recipe_id
 * @property int $default_warehouse_id
 * @property string $planned_quantity
 * @property string $actual_quantity
 * @property string $status
 * @property int|null $reversal_of
 * @property int|null $reversed_by
 * @property string|null $reversal_reason
 * @property string|null $price
 * @property string|null $amount
 * @property int $created_by
 * @property string $created_at
 * @property string|null $posted_at
 */
class ProductionOrder extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%production_orders}}';
    }

    public function rules(): array
    {
        return [
            [
                [
                    'order_number',
                    'doc_date',
                    'customer_id',
                    'recipe_id',
                    'default_warehouse_id',
                    'planned_quantity',
                    'actual_quantity',
                    'created_by',
                ],
                'required',
            ],
            [
                ['customer_id', 'recipe_id', 'default_warehouse_id', 'created_by', 'reversal_of', 'reversed_by'],
                'integer',
            ],
            [['doc_date'], 'date', 'format' => 'php:Y-m-d'],
            [['planned_quantity', 'actual_quantity'], 'number', 'min' => 0.01],
            [['status'], 'in', 'range' => array_column(DocumentStatus::cases(), 'value')],
            [['order_number'], 'string', 'max' => 50],
            [['price', 'amount'], 'number'],
        ];
    }

    public function getConsumptions(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ProductionConsumption::class, ['production_order_id' => 'id']);
    }

    public function isDraft(): bool
    {
        return $this->status === DocumentStatus::Draft->value;
    }
}