<?php

declare(strict_types=1);

namespace app\modules\admin\models\inventory;

use app\models\inventory\MaterialCategory;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MaterialCategorySearch extends Model
{
    public ?string $name = null;

    public function rules(): array
    {
        return [
            [['name'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = MaterialCategory::find();
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
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}