<?php

declare(strict_types=1);

namespace app\modules\admin\models;

use app\models\PageView;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PageViewSearch extends PageView
{
    public function rules(): array
    {
        return [
            [['id', 'is_bot'], 'integer'],
            [[
                'path',
                'route',
                'query_string',
                'utm_source',
                'utm_medium',
                'referrer',
                'visitor_id',
                'ip',
                'user_agent',
            ], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = PageView::find();

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
            'is_bot' => $this->is_bot,
        ]);

        $query->andFilterWhere(['like', 'path', $this->path]);
        $query->andFilterWhere(['like', 'route', $this->route]);
        $query->andFilterWhere(['like', 'query_string', $this->query_string]);
        $query->andFilterWhere(['like', 'utm_source', $this->utm_source]);
        $query->andFilterWhere(['like', 'utm_medium', $this->utm_medium]);
        $query->andFilterWhere(['like', 'referrer', $this->referrer]);
        $query->andFilterWhere(['like', 'visitor_id', $this->visitor_id]);
        $query->andFilterWhere(['like', 'ip', $this->ip]);
        $query->andFilterWhere(['like', 'user_agent', $this->user_agent]);

        return $dataProvider;
    }
}
