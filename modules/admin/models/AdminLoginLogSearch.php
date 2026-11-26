<?php

declare(strict_types=1);

namespace app\modules\admin\models;

use app\models\AdminLoginLog;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AdminLoginLogSearch extends AdminLoginLog
{
    public function rules(): array
    {
        return [
            [['id', 'admin_user_id'], 'integer'],
            [['username', 'ip', 'user_agent'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = AdminLoginLog::find()->with('adminUser');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 30],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'admin_user_id' => $this->admin_user_id,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username]);
        $query->andFilterWhere(['like', 'ip', $this->ip]);
        $query->andFilterWhere(['like', 'user_agent', $this->user_agent]);

        return $dataProvider;
    }
}
