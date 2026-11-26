<?php

declare(strict_types=1);

namespace app\modules\admin\models;

use app\models\Setting;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SettingSearch extends Setting
{
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['key', 'value', 'description'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Setting::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['key' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'key', $this->key]);
        $query->andFilterWhere(['like', 'value', $this->value]);
        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
