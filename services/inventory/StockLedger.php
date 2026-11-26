<?php

declare(strict_types=1);

namespace app\services\inventory;

use Yii;
use yii\db\Connection;
use yii\db\Exception as DbException;

/**
 * Агрегат material_stock: upsert и блокировки (Основа.md §7, §9.2).
 */
final class StockLedger
{
    public function __construct(
        private readonly Connection $db,
    ) {
    }

    /**
     * @throws InventoryException
     * @throws DbException
     */
    public function applyDelta(int $materialId, int $warehouseId, string $deltaKg): void
    {
        $sql = <<<'SQL'
INSERT INTO {{%material_stock}} (material_id, warehouse_id, quantity)
VALUES (:material_id, :warehouse_id, :delta)
ON DUPLICATE KEY UPDATE
  quantity = quantity + VALUES(quantity),
  updated_at = CURRENT_TIMESTAMP(6)
SQL;

        $this->db->createCommand($sql, [
            ':material_id' => $materialId,
            ':warehouse_id' => $warehouseId,
            ':delta' => $deltaKg,
        ])->execute();
    }

    /**
     * @throws InventoryException
     * @throws DbException
     */
    public function lockAndGetQuantity(int $materialId, int $warehouseId): string
    {
        $row = $this->db->createCommand(
            'SELECT quantity FROM {{%material_stock}} WHERE material_id = :m AND warehouse_id = :w FOR UPDATE',
            [':m' => $materialId, ':w' => $warehouseId],
        )->queryOne();

        if ($row === false) {
            return InventoryScale::zeroQuantity();
        }

        return (string) $row['quantity'];
    }

    /**
     * @throws InventoryException
     */
    public function assertSufficient(string $currentQty, string $consumeQty): void
    {
        $allowNegative = (bool) (Yii::$app->params['inventory']['allowNegativeStock'] ?? false);
        if ($allowNegative) {
            return;
        }

        if (bccomp($currentQty, $consumeQty, InventoryScale::scale()) < 0) {
            throw new InventoryException(
                sprintf('Недостаточно остатка: есть %s кг, требуется %s кг.', $currentQty, $consumeQty),
            );
        }
    }

    public static function make(): self
    {
        return new self(Yii::$app->db);
    }
}