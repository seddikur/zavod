<?php

declare(strict_types=1);

namespace app\services\inventory;

use app\enums\AdjustmentStatus;
use app\models\inventory\Adjustment;
use app\models\inventory\AdjustmentItem;
use Yii;

/**
 * Компенсирующая корректировка для проведённого документа (Основа.md §6.1).
 */
final class AdjustmentCompensationService extends TransactionalService
{
    private readonly AdjustmentPostingService $posting;

    public function __construct(?AdjustmentPostingService $posting = null)
    {
        $this->posting = $posting ?? new AdjustmentPostingService();
    }

    /**
     * @return int id новой корректировки (проведённой)
     * @throws InventoryException
     */
    public function compensate(int $postedAdjustmentId, int $adminUserId, bool $autoPost = true): int
    {
        return $this->transaction(function () use ($postedAdjustmentId, $adminUserId, $autoPost): int {
            $source = Adjustment::findOne($postedAdjustmentId);
            if ($source === null || !$source->isPosted()) {
                throw new InventoryException('Компенсация возможна только для проведённой корректировки.');
            }

            $comp = new Adjustment([
                'doc_number' => $source->doc_number . '-K' . $source->id,
                'doc_date' => date('Y-m-d'),
                'warehouse_id' => $source->warehouse_id,
                'reason' => 'Компенсация корректировки ' . $source->doc_number . ' (#' . $source->id . ')',
                'status' => AdjustmentStatus::Draft->value,
                'created_by' => $adminUserId,
            ]);
            if (!$comp->save()) {
                throw new InventoryException('Не удалось создать компенсирующую корректировку.');
            }

            foreach ($source->items as $item) {
                $line = new AdjustmentItem([
                    'adjustment_id' => $comp->id,
                    'material_id' => $item->material_id,
                    'unit_id' => $item->unit_id,
                    'quantity' => bcmul((string) $item->quantity, '-1', InventoryScale::scale()),
                ]);
                if (!$line->save()) {
                    throw new InventoryException('Не удалось создать строку компенсации.');
                }
            }

            if ($autoPost) {
                $this->posting->post((int) $comp->id);
            }

            return (int) $comp->id;
        });
    }
}