<?php

declare(strict_types=1);

namespace app\services\inventory;

use Yii;

/** Точность количеств и весов в учёте (кг, т и т.д.). */
final class InventoryScale
{
    public const SCALE = 2;

    public static function scale(): int
    {
        return (int) (Yii::$app->params['inventory']['quantityScale'] ?? self::SCALE);
    }

    public static function minQuantity(): float
    {
        return 10 ** -self::scale();
    }

    public static function zeroQuantity(): string
    {
        return number_format(0, self::scale(), '.', '');
    }
}