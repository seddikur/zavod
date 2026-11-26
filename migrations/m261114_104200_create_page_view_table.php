<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Журнал просмотров публичных страниц + UTM.
 */
class m261114_104200_create_page_view_table extends Migration
{
    private const TABLE = '{{%page_view}}';

    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TABLE, [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'created_at' => $this->integer()->notNull(),
            'path' => $this->string(255)->notNull()->comment('Путь без query, напр. /cases'),
            'route' => $this->string(64)->null()->comment('Yii route, напр. site/cases'),
            'query_string' => $this->string(1024)->null(),
            'utm_source' => $this->string(128)->null(),
            'utm_medium' => $this->string(128)->null(),
            'referrer' => $this->string(1024)->null(),
            'visitor_id' => $this->char(36)->null()->comment('UUID из cookie'),
            'ip' => $this->string(45)->null(),
            'user_agent' => $this->string(512)->null(),
            'is_bot' => $this->boolean()->notNull()->defaultValue(false),
        ], $tableOptions);

        $this->createIndex('idx_page_view_created', self::TABLE, 'created_at');
        $this->createIndex('idx_page_view_path_created', self::TABLE, ['path', 'created_at']);
        $this->createIndex('idx_page_view_utm_created', self::TABLE, ['utm_source', 'utm_medium', 'created_at']);
        $this->createIndex('idx_page_view_visitor_created', self::TABLE, ['visitor_id', 'created_at']);
    }

    public function safeDown(): void
    {
        $this->dropTable(self::TABLE);
    }
}
