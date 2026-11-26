<?php

declare(strict_types=1);

namespace app\modules\admin\models\inventory;

use app\models\inventory\Recipe;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RecipeSearch extends Model
{
    public ?string $code = null;
    public ?string $name = null;

    public function rules(): array
    {
        return [
            [['code', 'name'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Recipe::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['code' => SORT_ASC, 'recipe_date' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'code', $this->code]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}