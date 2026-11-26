<?php

declare(strict_types=1);

namespace app\enums;

/**
 * Статусы документов прихода и отгрузки (Основа.md §8.6).
 */
enum DocumentStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';
    case Reversed = 'reversed';

    public function canPost(): bool
    {
        return $this === self::Draft;
    }

    public function canDeleteDraft(): bool
    {
        return $this === self::Draft;
    }
}