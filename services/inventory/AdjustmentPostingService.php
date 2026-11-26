<?php

declare(strict_types=1);

namespace app\services\inventory;

use app\enums\AdjustmentStatus;
use app\models\inventory\Adjustment;
use app\models\inventory\AdjustmentItem;
use Yii;

/**
 * Проведение корректировок (Основа.md §8.6 adjustments draft → posted).
 */
final class AdjustmentPostingService extends TransactionalService
{
    private readonly StockLedger $ledger;

    public function __construct(?StockLedger $ledger = null)
    {
        $this->ledger = $ledger ?? StockLedger::make();
    }

    /**
     * @throws InventoryException
     */
    public function post(int $adjustmentId): void
    {
        $this->transaction(function () use ($adjustmentId): void {
            $adjustment = Adjustment::findOne($adjustmentId);
            if ($adjustment === null) {
                throw new InventoryException('Корректировка не найдена.');
            }

            if (!AdjustmentStatus::from($adjustment->status)->canPost()) {
                throw new InventoryException('Провести можно только черновик.');
            }

            /** @var AdjustmentItem[] $items */
            $items = $adjustment->getItems()->all();
            if ($items === []) {
                throw new InventoryException('Нельзя провести корректировку без строк.');
            }

            Yii::$app->db->createCommand(
                'SELECT id FROM {{%adjustments}} WHERE id = :id FOR UPDATE',
                [':id' => $adjustmentId],
            )->queryScalar();

            $warehouseId = (int) $adjustment->warehouse_id;
            foreach ($items as $item) {
                $qty = (string) $item->quantity;
                $scale = InventoryScale::scale();
                if (bccomp($qty, '0', $scale) === 0) {
                    throw new InventoryException('Строка с нулевым количеством недопустима.');
                }
                if (bccomp($qty, '0', $scale) < 0) {
                    $current = $this->ledger->lockAndGetQuantity((int) $item->material_id, $warehouseId);
                    $this->ledger->assertSufficient($current, bcmul($qty, '-1', $scale));
                }
                $this->ledger->applyDelta((int) $item->material_id, $warehouseId, $qty);
            }

            $adjustment->status = AdjustmentStatus::Posted->value;
            $adjustment->posted_at = date('Y-m-d H:i:s.u');
            if (!$adjustment->save(false, ['status', 'posted_at'])) {
                throw new InventoryException('Не удалось обновить статус корректировки.');
            }
        });
    }
}