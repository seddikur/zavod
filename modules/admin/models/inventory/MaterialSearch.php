<?php

declare(strict_types=1);

namespace app\modules\admin\models\inventory;

use app\models\inventory\Material;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MaterialSearch extends Model
{
    public ?string $code = null;
    public ?string $name = null;
    public ?int $category_id = null;
    public ?int $is_active = null;

    public function rules(): array
    {
        return [
            [['category_id', 'is_active'], 'integer'],
            [['code', 'name'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Material::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['code' => SORT_ASC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }
        $query->andFilterWhere(['category_id' => $this->category_id, 'is_active' => $this->is_active]);
        $query->andFilterWhere(['like', 'code', $this->code]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}