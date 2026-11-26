<?php

use yii\db\Migration;

/**
 * Этап 1: справочники учёта материалов (Основа.md §2).
 */
class m261127_120000_create_inventory_reference_tables extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%warehouses}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'name' => $this->string(100)->notNull(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions);

        $this->createTable('{{%units}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'name' => $this->string(50)->notNull(),
            'short_name' => $this->string(10)->notNull(),
            'base_unit_id' => $this->bigInteger()->unsigned()->null(),
            'factor' => $this->decimal(18, 2)->notNull()->defaultValue('1.00'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_units_base_unit',
            '{{%units}}',
            'base_unit_id',
            '{{%units}}',
            'id',
            'SET NULL',
            'RESTRICT',
        );

        $this->createTable('{{%material_categories}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'name' => $this->string(100)->notNull(),
        ], $tableOptions);
        $this->createIndex('ux_material_categories_name', '{{%material_categories}}', 'name', true);

        $this->createTable('{{%materials}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'code' => $this->string(50)->notNull(),
            'name' => $this->string(200)->notNull(),
            'category_id' => $this->bigInteger()->unsigned()->notNull(),
            'unit_id' => $this->bigInteger()->unsigned()->notNull(),
            'default_warehouse_id' => $this->bigInteger()->unsigned()->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime(6)->notNull()->defaultExpression('CURRENT_TIMESTAMP(6)'),
        ], $tableOptions);

        $this->createIndex('ux_materials_code', '{{%materials}}', 'code', true);
        $this->createIndex('idx_materials_category', '{{%materials}}', 'category_id');
        $this->createIndex('idx_materials_default_wh', '{{%materials}}', 'default_warehouse_id');

        $this->addForeignKey(
            'fk_materials_category',
            '{{%materials}}',
            'category_id',
            '{{%material_categories}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_materials_unit',
            '{{%materials}}',
            'unit_id',
            '{{%units}}',
            'id',
            'RESTRICT',
            'RESTRICT',
        );
        $this->addForeignKey(
            'fk_materials_default_warehouse',
            '{{%materials}}',
            'default_warehouse_id',
            '{{%warehouses}}',
            'id',
            'SET NULL',
            'RESTRICT',
        );

        $this->createTable('{{%customers}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'name' => $this->string(200)->notNull(),
            'inn' => $this->string(12)->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions);

        $this->createTable('{{%suppliers}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'name' => $this->string(200)->notNull(),
            'inn' => $this->string(12)->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions);
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_materials_default_warehouse', '{{%materials}}');
        $this->dropForeignKey('fk_materials_unit', '{{%materials}}');
        $this->dropForeignKey('fk_materials_category', '{{%materials}}');
        $this->dropTable('{{%materials}}');
        $this->dropTable('{{%material_categories}}');
        $this->dropForeignKey('fk_units_base_unit', '{{%units}}');
        $this->dropTable('{{%units}}');
        $this->dropTable('{{%warehouses}}');
        $this->dropTable('{{%customers}}');
        $this->dropTable('{{%suppliers}}');
    }
}