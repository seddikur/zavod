<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Статусы корректировки (Основа.md §6, без reversed).
 */
enum AdjustmentStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';

    public function canPost(): bool
    {
        return $this === self::Draft;
    }
}