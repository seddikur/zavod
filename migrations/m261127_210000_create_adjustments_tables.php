<?php

use yii\db\Migration;

/**
 * Этап 6: корректировки остатков (Основа.md §6).
 */
class m261127_210000_create_adjustments_tables extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%adjustments}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'doc_number' => $this->string(50)->notNull(),
            'doc_date' => $this->date()->notNull(),
            'warehouse_id' => $this->bigInteger()->unsigned()->notNull(),
            'reason' => $this->text()->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('draft'),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(6)->notNull()->defaultExpression('CURRENT_TIMESTAMP(6)'),
            'posted_at' => $this->dateTime(6)->null(),
        ], $tableOptions);
        $this->createIndex('ux_adjustments_doc_number', '{{%adjustments}}', 'doc_number', true);

        $this->addForeignKey(
            'fk_adjustments_warehouse',
            '{{%adjustments}}',
            'warehouse_id',
            '{{%warehouses}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_adjustments_created_by',
            '{{%adjustments}}',
            'created_by',
            '{{%admin_users}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );

        $this->createTable('{{%adjustment_items}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'adjustment_id' => $this->bigInteger()->unsigned()->notNull(),
            'material_id' => $this->bigInteger()->unsigned()->notNull(),
            'quantity' => $this->decimal(18, 2)->notNull(),
            'unit_id' => $this->bigInteger()->unsigned()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx_adjustment_items_adjustment', '{{%adjustment_items}}', 'adjustment_id');
        $this->createIndex('idx_adjustment_items_material', '{{%adjustment_items}}', 'material_id');
        $this->addForeignKey(
            'fk_adjustment_items_adjustment',
            '{{%adjustment_items}}',
            'adjustment_id',
            '{{%adjustments}}',
            'id',
            'RESTRICT',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_adjustment_items_material',
            '{{%adjustment_items}}',
            'material_id',
            '{{%materials}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_adjustment_items_unit',
            '{{%adjustment_items}}',
            'unit_id',
            '{{%units}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );

        if ($this->db->driverName === 'mysql') {
            $this->execute(<<<'SQL'
CREATE TRIGGER trg_no_delete_adjustment_items
BEFORE DELETE ON {{%adjustment_items}}
FOR EACH ROW
BEGIN
    DECLARE parent_status VARCHAR(20);
    SELECT status INTO parent_status FROM {{%adjustments}} WHERE id = OLD.adjustment_id;
    IF parent_status = 'posted' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Deleting movements is forbidden. Use compensating adjustment.';
    END IF;
END
SQL);
        }
    }

    public function safeDown(): void
    {
        if ($this->db->driverName === 'mysql') {
            $this->execute('DROP TRIGGER IF EXISTS trg_no_delete_adjustment_items');
        }
        $this->dropTable('{{%adjustment_items}}');
        $this->dropTable('{{%adjustments}}');
    }
}