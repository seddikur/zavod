<?php

use yii\db\Migration;

/**
 * Таблицы settings / seo_settings.
 */
class m260810_215800_create_settings_and_seo_tables extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(255)->notNull()->unique(),
            'value' => $this->text()->null(),
            'description' => $this->string(512)->null(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%seo_settings}}', [
            'id' => $this->tinyInteger()->unsigned()->notNull(),
            'site_name' => $this->string(255)->notNull(),
            'title_separator' => $this->string(32)->notNull()->defaultValue('|'),
            'title_default' => $this->string(512)->notNull(),
            'title_template' => $this->string(512)->notNull(),
            'description_default' => $this->text()->notNull(),
            'description_template' => $this->text()->null(),
            'keywords' => $this->text()->null(),
            'og_image' => $this->string(1024)->null(),
            'indexable' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk_seo_settings', '{{%seo_settings}}', 'id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%seo_settings}}');
        $this->dropTable('{{%settings}}');
    }
}
