<?php

declare(strict_types=1);

namespace app\services\inventory;

use Yii;
use yii\db\Exception as DbException;

/**
 * Обёртка транзакций для операций учёта.
 */
abstract class TransactionalService
{
    /**
     * @template T
     * @param callable(): T $fn
     * @return T
     * @throws DbException
     */
    protected function transaction(callable $fn): mixed
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $result = $fn();
            $transaction->commit();

            return $result;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}