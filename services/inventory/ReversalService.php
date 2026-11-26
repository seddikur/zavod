<?php

declare(strict_types=1);

namespace app\services\inventory;

use app\enums\DocumentStatus;
use app\models\inventory\ProductionConsumption;
use app\models\inventory\ProductionOrder;
use app\models\inventory\Receipt;
use app\models\inventory\ReceiptItem;
use Yii;

/**
 * Сторно прихода и отгрузки (Основа.md §8.4).
 */
final class ReversalService extends TransactionalService
{
    private readonly ReceiptPostingService $receiptPosting;
    private readonly StockLedger $ledger;

    public function __construct(
        ?ReceiptPostingService $receiptPosting = null,
        ?StockLedger $ledger = null,
    ) {
        $this->receiptPosting = $receiptPosting ?? new ReceiptPostingService();
        $this->ledger = $ledger ?? StockLedger::make();
    }

    /**
     * @throws InventoryException
     */
    public function reverseReceipt(int $receiptId, int $adminUserId, string $reason): int
    {
        return $this->transaction(function () use ($receiptId, $adminUserId, $reason): int {
            $original = Receipt::findOne($receiptId);
            if ($original === null) {
                throw new InventoryException('Приход не найден.');
            }
            if ($original->status !== DocumentStatus::Posted->value) {
                throw new InventoryException('Сторнировать можно только проведённый документ.');
            }
            if ($original->reversed_by !== null) {
                throw new InventoryException('Документ уже сторнирован.');
            }

            Yii::$app->db->createCommand(
                'SELECT id FROM {{%receipts}} WHERE id = :id FOR UPDATE',
                [':id' => $receiptId],
            )->queryScalar();

            $storno = new Receipt([
                'doc_number' => $original->doc_number . '-S' . $original->id,
                'doc_date' => date('Y-m-d'),
                'supplier_id' => $original->supplier_id,
                'warehouse_id' => $original->warehouse_id,
                'status' => DocumentStatus::Draft->value,
                'reversal_of' => $original->id,
                'reversal_reason' => $reason,
                'created_by' => $adminUserId,
            ]);
            if (!$storno->save()) {
                throw new InventoryException('Не удалось создать сторно-приход.');
            }

            $total = '0.00';
            foreach ($original->items as $item) {
                $negQty = bcmul((string) $item->quantity, '-1', InventoryScale::scale());
                $negAmount = bcmul((string) $item->amount, '-1', 2);
                $stornoItem = new ReceiptItem([
                    'receipt_id' => $storno->id,
                    'material_id' => $item->material_id,
                    'quantity' => $negQty,
                    'unit_id' => $item->unit_id,
                    'price' => $item->price,
                    'amount' => $negAmount,
                ]);
                if (!$stornoItem->save(false)) {
                    throw new InventoryException('Не удалось создать строку сторно.');
                }
                $total = bcadd($total, $negAmount, 2);
            }
            $storno->total_amount = $total;
            $storno->save(false, ['total_amount']);

            $stornoId = (int) $storno->id;
            $this->receiptPosting->post($stornoId);

            $original->status = DocumentStatus::Reversed->value;
            $original->reversed_by = $stornoId;
            if (!$original->save(false, ['status', 'reversed_by'])) {
                throw new InventoryException('Не удалось пометить исходный приход как reversed.');
            }

            return $stornoId;
        });
    }

    /**
     * @throws InventoryException
     */
    public function reverseProductionOrder(int $orderId, int $adminUserId, string $reason): int
    {
        return $this->transaction(function () use ($orderId, $adminUserId, $reason): int {
            $original = ProductionOrder::findOne($orderId);
            if ($original === null) {
                throw new InventoryException('Отгрузка не найдена.');
            }
            if ($original->status !== DocumentStatus::Posted->value) {
                throw new InventoryException('Сторнировать можно только проведённую отгрузку.');
            }
            if ($original->reversed_by !== null) {
                throw new InventoryException('Документ уже сторнирован.');
            }

            Yii::$app->db->createCommand(
                'SELECT id FROM {{%production_orders}} WHERE id = :id FOR UPDATE',
                [':id' => $orderId],
            )->queryScalar();

            $storno = new ProductionOrder([
                'order_number' => $original->order_number . '-S' . $original->id,
                'doc_date' => date('Y-m-d'),
                'customer_id' => $original->customer_id,
                'recipe_id' => $original->recipe_id,
                'default_warehouse_id' => $original->default_warehouse_id,
                'planned_quantity' => $original->planned_quantity,
                'actual_quantity' => $original->actual_quantity,
                'status' => DocumentStatus::Draft->value,
                'reversal_of' => $original->id,
                'reversal_reason' => $reason,
                'created_by' => $adminUserId,
            ]);
            if (!$storno->save()) {
                throw new InventoryException('Не удалось создать сторно-отгрузку.');
            }

            $rows = [];
            foreach ($original->consumptions as $cons) {
                $negQty = bcmul((string) $cons->quantity, '-1', InventoryScale::scale());
                $rows[] = new ProductionConsumption([
                    'production_order_id' => $storno->id,
                    'material_id' => $cons->material_id,
                    'warehouse_id' => $cons->warehouse_id,
                    'recipe_item_id' => $cons->recipe_item_id,
                    'quantity' => $negQty,
                    'unit_id' => $cons->unit_id,
                ]);
            }
            if ($rows === []) {
                throw new InventoryException('У отгрузки нет строк списания.');
            }

            usort(
                $rows,
                static fn (ProductionConsumption $a, ProductionConsumption $b): int =>
                    [$a->material_id, $a->warehouse_id] <=> [$b->material_id, $b->warehouse_id],
            );

            foreach ($rows as $row) {
                if (!$row->save(false)) {
                    throw new InventoryException('Не удалось сохранить строку сторно-списания.');
                }
                $qty = (string) $row->quantity;
                $this->ledger->applyDelta(
                    (int) $row->material_id,
                    (int) $row->warehouse_id,
                    bcmul($qty, '-1', InventoryScale::scale()),
                );
            }

            $storno->status = DocumentStatus::Posted->value;
            $storno->posted_at = date('Y-m-d H:i:s.u');
            if (!$storno->save(false, ['status', 'posted_at'])) {
                throw new InventoryException('Не удалось провести сторно-отгрузку.');
            }

            $stornoId = (int) $storno->id;
            $original->status = DocumentStatus::Reversed->value;
            $original->reversed_by = $stornoId;
            if (!$original->save(false, ['status', 'reversed_by'])) {
                throw new InventoryException('Не удалось пометить исходную отгрузку как reversed.');
            }

            return $stornoId;
        });
    }
}