<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\inventory\MaterialStock;
use app\modules\admin\models\inventory\MaterialStockSearch;

class MaterialStockController extends AdminController
{
    public function actionIndex(): string
    {
        $searchModel = new MaterialStockSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }
}