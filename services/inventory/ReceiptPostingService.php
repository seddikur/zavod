<?php

declare(strict_types=1);

namespace app\services\inventory;

use app\enums\DocumentStatus;
use app\models\inventory\Receipt;
use app\models\inventory\ReceiptItem;
use Yii;

/**
 * Проведение приходных документов (Основа.md §8.2).
 */
final class ReceiptPostingService extends TransactionalService
{
    private readonly StockLedger $ledger;

    public function __construct(?StockLedger $ledger = null)
    {
        $this->ledger = $ledger ?? StockLedger::make();
    }

    /**
     * @throws InventoryException
     */
    public function post(int $receiptId): void
    {
        $this->transaction(function () use ($receiptId): void {
            $receipt = Receipt::findOne($receiptId);
            if ($receipt === null) {
                throw new InventoryException('Документ прихода не найден.');
            }

            $status = DocumentStatus::from($receipt->status);
            if (!$status->canPost()) {
                throw new InventoryException('Провести можно только документ в статусе draft.');
            }

            /** @var ReceiptItem[] $items */
            $items = $receipt->getItems()->all();
            if ($items === []) {
                throw new InventoryException('Нельзя провести приход без строк.');
            }

            Yii::$app->db->createCommand(
                'SELECT id FROM {{%receipts}} WHERE id = :id FOR UPDATE',
                [':id' => $receiptId],
            )->queryScalar();

            foreach ($items as $item) {
                $this->ledger->applyDelta(
                    (int) $item->material_id,
                    (int) $receipt->warehouse_id,
                    (string) $item->quantity,
                );
            }

            $receipt->status = DocumentStatus::Posted->value;
            $receipt->posted_at = date('Y-m-d H:i:s.u');
            if (!$receipt->save(false, ['status', 'posted_at'])) {
                throw new InventoryException('Не удалось обновить статус прихода.');
            }
        });
    }
}