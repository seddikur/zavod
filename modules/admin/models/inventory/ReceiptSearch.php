<?php

declare(strict_types=1);

namespace app\modules\admin\models\inventory;

use app\models\inventory\Receipt;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ReceiptSearch extends Model
{
    public ?string $doc_number = null;
    public ?string $status = null;

    public function rules(): array
    {
        return [
            [['doc_number', 'status'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Receipt::find();
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
        $query->andFilterWhere(['like', 'doc_number', $this->doc_number]);

        return $dataProvider;
    }
}