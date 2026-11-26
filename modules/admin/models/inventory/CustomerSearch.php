<?php

declare(strict_types=1);

namespace app\modules\admin\models\inventory;

use app\models\inventory\Customer;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CustomerSearch extends Model
{
    public ?string $name = null;
    public ?string $inn = null;
    public ?int $is_active = null;

    public function rules(): array
    {
        return [
            [['is_active'], 'integer'],
            [['name', 'inn'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Customer::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['name' => SORT_ASC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }
        $query->andFilterWhere(['is_active' => $this->is_active]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'inn', $this->inn]);

        return $dataProvider;
    }
}