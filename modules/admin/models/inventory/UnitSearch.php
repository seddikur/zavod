<?php

declare(strict_types=1);

namespace app\modules\admin\models\inventory;

use app\models\inventory\Unit;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UnitSearch extends Model
{
    public ?string $name = null;
    public ?string $short_name = null;

    public function rules(): array
    {
        return [
            [['name', 'short_name'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Unit::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'short_name', $this->short_name]);

        return $dataProvider;
    }
}