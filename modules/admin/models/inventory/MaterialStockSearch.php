<?php

declare(strict_types=1);

namespace app\modules\admin\models\inventory;

use app\models\inventory\MaterialStock;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class MaterialStockSearch extends Model
{
    public ?int $material_id = null;
    public ?int $warehouse_id = null;

    public function rules(): array
    {
        return [
            [['material_id', 'warehouse_id'], 'integer'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = MaterialStock::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['material_id' => SORT_ASC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }
        $query->andFilterWhere([
            'material_id' => $this->material_id,
            'warehouse_id' => $this->warehouse_id,
        ]);

        return $dataProvider;
    }
}