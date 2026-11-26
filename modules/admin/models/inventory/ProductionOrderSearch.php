<?php

declare(strict_types=1);

namespace app\modules\admin\models\inventory;

use app\models\inventory\ProductionOrder;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductionOrderSearch extends Model
{
    public ?string $order_number = null;
    public ?string $status = null;

    public function rules(): array
    {
        return [
            [['order_number', 'status'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = ProductionOrder::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'order_number', $this->order_number]);

        return $dataProvider;
    }
}