<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Просмотр публичной страницы.
 *
 * @property int $id
 * @property int $created_at
 * @property string $path
 * @property string|null $route
 * @property string|null $query_string
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $referrer
 * @property string|null $visitor_id
 * @property string|null $ip
 * @property string|null $user_agent
 * @property bool|int $is_bot
 */
class PageView extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%page_view}}';
    }

    public function rules(): array
    {
        return [
            [['created_at', 'path'], 'required'],
            [['created_at'], 'integer'],
            [['is_bot'], 'boolean'],
            [['path'], 'string', 'max' => 255],
            [['route'], 'string', 'max' => 64],
            [['query_string', 'referrer'], 'string', 'max' => 1024],
            [['utm_source', 'utm_medium'], 'string', 'max' => 128],
            [['visitor_id'], 'string', 'max' => 36],
            [['ip'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 512],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Время',
            'path' => 'Путь',
            'route' => 'Маршрут',
            'query_string' => 'Query',
            'utm_source' => 'UTM source',
            'utm_medium' => 'UTM medium',
            'referrer' => 'Referrer',
            'visitor_id' => 'Visitor',
            'ip' => 'IP',
            'user_agent' => 'User-Agent',
            'is_bot' => 'Бот',
        ];
    }
}
