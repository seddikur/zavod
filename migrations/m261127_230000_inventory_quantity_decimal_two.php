<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Количества и веса: DECIMAL(18,2) вместо (18,6).
 */
class m261127_230000_inventory_quantity_decimal_two extends Migration
{
    public function safeUp(): void
    {
        $dec = $this->decimal(18, 2)->notNull();
        $decNull = $this->decimal(18, 2);

        $pairs = [
            ['{{%material_stock}}', 'quantity', true, '0.00'],
            ['{{%units}}', 'factor', true, '1.00'],
            ['{{%recipes}}', 'output_quantity', true, null],
            ['{{%recipe_items}}', 'quantity', true, null],
            ['{{%receipt_items}}', 'quantity', true, null],
            ['{{%receipt_items}}', 'price', true, '0.00'],
            ['{{%production_orders}}', 'planned_quantity', true, null],
            ['{{%production_orders}}', 'actual_quantity', true, null],
            ['{{%production_consumptions}}', 'quantity', true, null],
            ['{{%adjustment_items}}', 'quantity', true, null],
        ];

        foreach ($pairs as [$table, $column, $notNull, $default]) {
            $schema = $this->db->getTableSchema($table, true);
            if ($schema === null || $schema->getColumn($column) === null) {
                continue;
            }
            $this->execute(sprintf(
                'UPDATE %s SET %s = ROUND(%s, 2)',
                $table,
                $column,
                $column,
            ));
            $col = $notNull ? $dec : $decNull;
            if ($default !== null) {
                $col = $col->defaultValue($default);
            }
            $this->alterColumn($table, $column, $col);
        }
    }

    public function safeDown(): void
    {
        // откат к 6 знакам не требуется для прода; при необходимости — вручную
    }
}