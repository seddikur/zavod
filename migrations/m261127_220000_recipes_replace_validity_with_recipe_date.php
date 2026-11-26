<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Рецепт: одна дата recipe_date вместо valid_from / valid_to.
 */
class m261127_220000_recipes_replace_validity_with_recipe_date extends Migration
{
    public function safeUp(): void
    {
        $table = '{{%recipes}}';
        $schema = $this->db->getTableSchema($table, true);
        if ($schema === null) {
            return;
        }

        if ($schema->getColumn('recipe_date') === null) {
            $this->addColumn($table, 'recipe_date', $this->date()->notNull()->defaultValue('1970-01-01'));
        }

        if ($schema->getColumn('valid_from') !== null) {
            $this->execute(
                'UPDATE {{%recipes}} SET recipe_date = valid_from WHERE recipe_date = :sentinel OR recipe_date IS NULL',
                [':sentinel' => '1970-01-01'],
            );
        }

        try {
            $this->dropIndex('idx_recipes_valid', $table);
        } catch (\Throwable) {
            // индекс мог отсутствовать
        }

        if ($schema->getColumn('valid_from') !== null) {
            $this->dropColumn($table, 'valid_from');
        }
        if ($schema->getColumn('valid_to') !== null) {
            $this->dropColumn($table, 'valid_to');
        }

        $this->alterColumn($table, 'recipe_date', $this->date()->notNull());
    }

    public function safeDown(): void
    {
        $table = '{{%recipes}}';
        $schema = $this->db->getTableSchema($table, true);
        if ($schema === null || $schema->getColumn('recipe_date') === null) {
            return;
        }

        $this->addColumn($table, 'valid_from', $this->date()->notNull()->defaultValue('1970-01-01'));
        $this->addColumn($table, 'valid_to', $this->date()->null());
        $this->execute('UPDATE {{%recipes}} SET valid_from = recipe_date');
        $this->createIndex('idx_recipes_valid', $table, ['code', 'valid_to']);
        $this->dropColumn($table, 'recipe_date');
    }
}