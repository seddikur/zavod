<?php

use yii\db\Migration;

/**
 * Этапы 2–5: остатки, рецепты, приход, отгрузка (Основа.md).
 */
class m261127_200000_create_inventory_operations_tables extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%material_stock}}', [
            'material_id' => $this->bigInteger()->unsigned()->notNull(),
            'warehouse_id' => $this->bigInteger()->unsigned()->notNull(),
            'quantity' => $this->decimal(18, 2)->notNull()->defaultValue('0.00'),
            'updated_at' => $this->dateTime(6)->notNull()->defaultExpression('CURRENT_TIMESTAMP(6)'),
        ], $tableOptions);
        $this->addPrimaryKey('pk_material_stock', '{{%material_stock}}', ['material_id', 'warehouse_id']);
        $this->addForeignKey(
            'fk_material_stock_material',
            '{{%material_stock}}',
            'material_id',
            '{{%materials}}',
            'id',
            'RESTRICT',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_material_stock_warehouse',
            '{{%material_stock}}',
            'warehouse_id',
            '{{%warehouses}}',
            'id',
            'RESTRICT',
            'CASCADE',
        );

        $this->createTable('{{%recipes}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'code' => $this->string(50)->notNull(),
            'name' => $this->string(200)->notNull(),
            'output_quantity' => $this->decimal(18, 2)->notNull(),
            'output_unit_id' => $this->bigInteger()->unsigned()->notNull(),
            'recipe_date' => $this->date()->notNull(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime(6)->notNull()->defaultExpression('CURRENT_TIMESTAMP(6)'),
        ], $tableOptions);
        $this->createIndex('idx_recipes_code', '{{%recipes}}', 'code');
        $this->addForeignKey(
            'fk_recipes_output_unit',
            '{{%recipes}}',
            'output_unit_id',
            '{{%units}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );

        $this->createTable('{{%recipe_items}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'recipe_id' => $this->bigInteger()->unsigned()->notNull(),
            'material_id' => $this->bigInteger()->unsigned()->notNull(),
            'warehouse_id' => $this->bigInteger()->unsigned()->null(),
            'quantity' => $this->decimal(18, 2)->notNull(),
            'unit_id' => $this->bigInteger()->unsigned()->notNull(),
        ], $tableOptions);
        $this->createIndex('ux_recipe_items_pair', '{{%recipe_items}}', ['recipe_id', 'material_id'], true);
        $this->addForeignKey(
            'fk_recipe_items_recipe',
            '{{%recipe_items}}',
            'recipe_id',
            '{{%recipes}}',
            'id',
            'RESTRICT',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_recipe_items_material',
            '{{%recipe_items}}',
            'material_id',
            '{{%materials}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_recipe_items_warehouse',
            '{{%recipe_items}}',
            'warehouse_id',
            '{{%warehouses}}',
            'id',
            'SET NULL',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_recipe_items_unit',
            '{{%recipe_items}}',
            'unit_id',
            '{{%units}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );

        $this->createTable('{{%receipts}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'doc_number' => $this->string(50)->notNull(),
            'doc_date' => $this->date()->notNull(),
            'supplier_id' => $this->bigInteger()->unsigned()->notNull(),
            'warehouse_id' => $this->bigInteger()->unsigned()->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('draft'),
            'reversal_of' => $this->bigInteger()->unsigned()->null(),
            'reversed_by' => $this->bigInteger()->unsigned()->null(),
            'reversal_reason' => $this->text()->null(),
            'total_amount' => $this->decimal(18, 2)->null(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(6)->notNull()->defaultExpression('CURRENT_TIMESTAMP(6)'),
            'posted_at' => $this->dateTime(6)->null(),
        ], $tableOptions);
        $this->createIndex('ux_receipts_doc_number', '{{%receipts}}', 'doc_number', true);
        $this->addForeignKey(
            'fk_receipts_supplier',
            '{{%receipts}}',
            'supplier_id',
            '{{%suppliers}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_receipts_warehouse',
            '{{%receipts}}',
            'warehouse_id',
            '{{%warehouses}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_receipts_created_by',
            '{{%receipts}}',
            'created_by',
            '{{%admin_users}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_receipts_reversal_of',
            '{{%receipts}}',
            'reversal_of',
            '{{%receipts}}',
            'id',
            'SET NULL',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_receipts_reversed_by',
            '{{%receipts}}',
            'reversed_by',
            '{{%receipts}}',
            'id',
            'SET NULL',
            'RESTRICT',
        );

        $this->createTable('{{%receipt_items}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'receipt_id' => $this->bigInteger()->unsigned()->notNull(),
            'material_id' => $this->bigInteger()->unsigned()->notNull(),
            'quantity' => $this->decimal(18, 2)->notNull(),
            'unit_id' => $this->bigInteger()->unsigned()->notNull(),
            'price' => $this->decimal(18, 2)->notNull()->defaultValue('0.00'),
            'amount' => $this->decimal(18, 2)->notNull()->defaultValue('0.00'),
        ], $tableOptions);
        $this->createIndex('idx_receipt_items_material', '{{%receipt_items}}', 'material_id');
        $this->createIndex('idx_receipt_items_receipt', '{{%receipt_items}}', 'receipt_id');
        $this->addForeignKey(
            'fk_receipt_items_receipt',
            '{{%receipt_items}}',
            'receipt_id',
            '{{%receipts}}',
            'id',
            'RESTRICT',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_receipt_items_material',
            '{{%receipt_items}}',
            'material_id',
            '{{%materials}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_receipt_items_unit',
            '{{%receipt_items}}',
            'unit_id',
            '{{%units}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );

        $this->createTable('{{%production_orders}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'order_number' => $this->string(50)->notNull(),
            'doc_date' => $this->date()->notNull(),
            'customer_id' => $this->bigInteger()->unsigned()->notNull(),
            'recipe_id' => $this->bigInteger()->unsigned()->notNull(),
            'default_warehouse_id' => $this->bigInteger()->unsigned()->notNull(),
            'planned_quantity' => $this->decimal(18, 2)->notNull(),
            'actual_quantity' => $this->decimal(18, 2)->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('draft'),
            'reversal_of' => $this->bigInteger()->unsigned()->null(),
            'reversed_by' => $this->bigInteger()->unsigned()->null(),
            'reversal_reason' => $this->text()->null(),
            'price' => $this->decimal(18, 2)->null(),
            'amount' => $this->decimal(18, 2)->null(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(6)->notNull()->defaultExpression('CURRENT_TIMESTAMP(6)'),
            'posted_at' => $this->dateTime(6)->null(),
        ], $tableOptions);
        $this->createIndex('ux_production_orders_number', '{{%production_orders}}', 'order_number', true);
        $this->addForeignKey(
            'fk_production_orders_customer',
            '{{%production_orders}}',
            'customer_id',
            '{{%customers}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_production_orders_recipe',
            '{{%production_orders}}',
            'recipe_id',
            '{{%recipes}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_production_orders_default_wh',
            '{{%production_orders}}',
            'default_warehouse_id',
            '{{%warehouses}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_production_orders_created_by',
            '{{%production_orders}}',
            'created_by',
            '{{%admin_users}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_production_orders_reversal_of',
            '{{%production_orders}}',
            'reversal_of',
            '{{%production_orders}}',
            'id',
            'SET NULL',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_production_orders_reversed_by',
            '{{%production_orders}}',
            'reversed_by',
            '{{%production_orders}}',
            'id',
            'SET NULL',
            'RESTRICT',
        );

        $this->createTable('{{%production_consumptions}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'production_order_id' => $this->bigInteger()->unsigned()->notNull(),
            'material_id' => $this->bigInteger()->unsigned()->notNull(),
            'warehouse_id' => $this->bigInteger()->unsigned()->notNull(),
            'recipe_item_id' => $this->bigInteger()->unsigned()->null(),
            'quantity' => $this->decimal(18, 2)->notNull(),
            'unit_id' => $this->bigInteger()->unsigned()->notNull(),
        ], $tableOptions);
        $this->createIndex('idx_prod_cons_material', '{{%production_consumptions}}', 'material_id');
        $this->createIndex('idx_prod_cons_order', '{{%production_consumptions}}', 'production_order_id');
        $this->createIndex('idx_prod_cons_warehouse', '{{%production_consumptions}}', 'warehouse_id');
        $this->addForeignKey(
            'fk_prod_cons_order',
            '{{%production_consumptions}}',
            'production_order_id',
            '{{%production_orders}}',
            'id',
            'RESTRICT',
            'CASCADE',
        );
        $this->addForeignKey(
            'fk_prod_cons_material',
            '{{%production_consumptions}}',
            'material_id',
            '{{%materials}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_prod_cons_warehouse',
            '{{%production_consumptions}}',
            'warehouse_id',
            '{{%warehouses}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_prod_cons_recipe_item',
            '{{%production_consumptions}}',
            'recipe_item_id',
            '{{%recipe_items}}',
            'id',
            'SET NULL',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_prod_cons_unit',
            '{{%production_consumptions}}',
            'unit_id',
            '{{%units}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );

        if ($this->db->driverName === 'mysql') {
            $this->createMovementDeleteTriggers();
        }
    }

    public function safeDown(): void
    {
        if ($this->db->driverName === 'mysql') {
            $this->dropMovementDeleteTriggers();
        }

        $this->dropTable('{{%production_consumptions}}');
        $this->dropTable('{{%production_orders}}');
        $this->dropTable('{{%receipt_items}}');
        $this->dropTable('{{%receipts}}');
        $this->dropTable('{{%recipe_items}}');
        $this->dropTable('{{%recipes}}');
        $this->dropTable('{{%material_stock}}');
    }

    private function createMovementDeleteTriggers(): void
    {
        $this->execute(<<<'SQL'
CREATE TRIGGER trg_no_delete_receipt_items
BEFORE DELETE ON {{%receipt_items}}
FOR EACH ROW
BEGIN
    DECLARE parent_status VARCHAR(20);
    SELECT status INTO parent_status FROM {{%receipts}} WHERE id = OLD.receipt_id;
    IF parent_status IN ('posted', 'reversed') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Deleting movements is forbidden. Use reversal document.';
    END IF;
END
SQL);

        $this->execute(<<<'SQL'
CREATE TRIGGER trg_no_delete_production_consumptions
BEFORE DELETE ON {{%production_consumptions}}
FOR EACH ROW
BEGIN
    DECLARE parent_status VARCHAR(20);
    SELECT status INTO parent_status FROM {{%production_orders}} WHERE id = OLD.production_order_id;
    IF parent_status IN ('posted', 'reversed') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Deleting movements is forbidden. Use reversal document.';
    END IF;
END
SQL);
    }

    private function dropMovementDeleteTriggers(): void
    {
        $this->execute('DROP TRIGGER IF EXISTS trg_no_delete_receipt_items');
        $this->execute('DROP TRIGGER IF EXISTS trg_no_delete_production_consumptions');
    }
}