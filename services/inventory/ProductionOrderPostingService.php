<?php

declare(strict_types=1);

namespace app\services\inventory;

use app\enums\DocumentStatus;
use app\models\inventory\Material;
use app\models\inventory\ProductionConsumption;
use app\models\inventory\ProductionOrder;
use app\models\inventory\Recipe;
use app\models\inventory\RecipeItem;
use Yii;
/**
 * Проведение отгрузки: списание по рецепту (Основа.md §8.3).
 */
final class ProductionOrderPostingService extends TransactionalService
{
    private readonly StockLedger $ledger;

    public function __construct(?StockLedger $ledger = null)
    {
        $this->ledger = $ledger ?? StockLedger::make();
    }

    /**
     * @throws InventoryException
     */
    public function post(int $orderId): void
    {
        $this->transaction(function () use ($orderId): void {
            $order = ProductionOrder::findOne($orderId);
            if ($order === null) {
                throw new InventoryException('Документ отгрузки не найден.');
            }

            $status = DocumentStatus::from($order->status);
            if (!$status->canPost()) {
                throw new InventoryException('Провести можно только заказ в статусе draft.');
            }

            Yii::$app->db->createCommand(
                'SELECT id FROM {{%production_orders}} WHERE id = :id FOR UPDATE',
                [':id' => $orderId],
            )->queryScalar();

            if ($order->getConsumptions()->exists()) {
                throw new InventoryException('Строки списания уже существуют.');
            }

            $recipe = Recipe::findOne((int) $order->recipe_id);
            if ($recipe === null) {
                throw new InventoryException('Рецепт не найден.');
            }

            /** @var RecipeItem[] $recipeItems */
            $recipeItems = $recipe->getItems()->with('material')->all();
            if ($recipeItems === []) {
                throw new InventoryException('В рецепте нет состава.');
            }

            $outputQty = (string) $recipe->output_quantity;
            $scale = InventoryScale::scale();
            if (bccomp($outputQty, '0', $scale) !== 1) {
                throw new InventoryException('Некорректный output_quantity рецепта.');
            }

            $actualQty = (string) $order->actual_quantity;
            $consumptionRows = [];

            foreach ($recipeItems as $ri) {
                $consumeKg = bcmul(
                    (string) $ri->quantity,
                    bcdiv($actualQty, $outputQty, $scale),
                    $scale,
                );
                if (bccomp($consumeKg, '0', $scale) !== 1) {
                    continue;
                }

                $material = $ri->material ?? Material::findOne((int) $ri->material_id);
                $warehouseId = $ri->warehouse_id
                    ?? $material?->default_warehouse_id
                    ?? $order->default_warehouse_id;
                if ($warehouseId === null) {
                    throw new InventoryException(
                        'Не задан склад списания для материала #' . $ri->material_id,
                    );
                }

                $consumptionRows[] = [
                    'production_order_id' => $orderId,
                    'material_id' => (int) $ri->material_id,
                    'warehouse_id' => (int) $warehouseId,
                    'recipe_item_id' => (int) $ri->id,
                    'quantity' => $consumeKg,
                    'unit_id' => (int) $ri->unit_id,
                ];
            }

            if ($consumptionRows === []) {
                throw new InventoryException('Расчёт списания не дал строк.');
            }

            usort(
                $consumptionRows,
                static fn (array $a, array $b): int =>
                    [$a['material_id'], $a['warehouse_id']] <=> [$b['material_id'], $b['warehouse_id']],
            );

            $aggregate = [];
            foreach ($consumptionRows as $row) {
                $key = $row['material_id'] . ':' . $row['warehouse_id'];
                if (!isset($aggregate[$key])) {
                    $aggregate[$key] = InventoryScale::zeroQuantity();
                }
                $aggregate[$key] = bcadd($aggregate[$key], $row['quantity'], $scale);
            }

            foreach ($aggregate as $key => $totalConsume) {
                [$materialId, $warehouseId] = array_map('intval', explode(':', $key, 2));
                $current = $this->ledger->lockAndGetQuantity($materialId, $warehouseId);
                $this->ledger->assertSufficient($current, $totalConsume);
            }

            foreach ($consumptionRows as $row) {
                $consumption = new ProductionConsumption($row);
                if (!$consumption->save(false)) {
                    throw new InventoryException('Не удалось сохранить строку списания.');
                }
                $this->ledger->applyDelta(
                    $row['material_id'],
                    $row['warehouse_id'],
                    bcmul($row['quantity'], '-1', $scale),
                );
            }

            $order->status = DocumentStatus::Posted->value;
            $order->posted_at = date('Y-m-d H:i:s.u');
            if (!$order->save(false, ['status', 'posted_at'])) {
                throw new InventoryException('Не удалось обновить статус отгрузки.');
            }
        });
    }
}