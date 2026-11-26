<?php

declare(strict_types=1);

namespace app\modules\admin\models\inventory;

use app\models\inventory\Warehouse;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class WarehouseSearch extends Model
{
    public ?string $name = null;
    public ?int $is_active = null;

    public function rules(): array
    {
        return [
            [['is_active'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Warehouse::find();
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

        return $dataProvider;
    }
}