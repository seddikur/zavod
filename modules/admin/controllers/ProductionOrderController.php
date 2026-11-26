<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\enums\DocumentStatus;
use app\models\inventory\Customer;
use app\models\inventory\ProductionConsumption;
use app\models\inventory\ProductionOrder;
use app\models\inventory\Recipe;
use app\models\inventory\Warehouse;
use app\services\inventory\InventoryException;
use app\services\inventory\ProductionOrderPostingService;
use app\services\inventory\ReversalService;
use Yii;
use app\modules\admin\models\inventory\ProductionOrderSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProductionOrderController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
                'post' => ['POST'],
                'reverse' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new ProductionOrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView(int $id): string
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'consumptions' => $model->consumptions,
        ]);
    }

    public function actionCreate(): Response|string
    {
        $model = new ProductionOrder([
            'doc_date' => date('Y-m-d'),
            'status' => DocumentStatus::Draft->value,
            'planned_quantity' => '1',
            'actual_quantity' => '1',
            'created_by' => (int) Yii::$app->adminUser->id,
        ]);

        if ($model->load(Yii::$app->request->post())) {
            $model->created_by = (int) Yii::$app->adminUser->id;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Заказ на отгрузку создан.');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'customers' => $this->customerList(),
            'recipes' => $this->recipeList(),
            'warehouses' => $this->warehouseList(),
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        if (!$model->isDraft()) {
            Yii::$app->session->setFlash('warning', 'Редактирование только в статусе draft.');

            return $this->redirect(['view', 'id' => $id]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Заказ обновлён.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'customers' => $this->customerList(),
            'recipes' => $this->recipeList(),
            'warehouses' => $this->warehouseList(),
        ]);
    }

    public function actionPost(int $id): Response
    {
        try {
            (new ProductionOrderPostingService())->post($id);
            Yii::$app->session->setFlash('success', 'Отгрузка проведена.');
        } catch (InventoryException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionReverse(int $id): Response
    {
        $reason = trim((string) Yii::$app->request->post('reason', 'Сторно'));
        if ($reason === '') {
            $reason = 'Сторно';
        }
        try {
            $stornoId = (new ReversalService())->reverseProductionOrder($id, (int) Yii::$app->adminUser->id, $reason);
            Yii::$app->session->setFlash('success', 'Сторно отгрузки проведено (#' . $stornoId . ').');

            return $this->redirect(['view', 'id' => $stornoId]);
        } catch (InventoryException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());

            return $this->redirect(['view', 'id' => $id]);
        }
    }

    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);
        if (!$model->isDraft()) {
            Yii::$app->session->setFlash('error', 'Удалить можно только черновик.');
        } else {
            ProductionConsumption::deleteAll(['production_order_id' => $id]);
            $model->delete();
            Yii::$app->session->setFlash('success', 'Заказ удалён.');
        }

        return $this->redirect(['index']);
    }

    private function findModel(int $id): ProductionOrder
    {
        $model = ProductionOrder::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Заказ не найден.');
        }

        return $model;
    }

    /**
     * @return array<int, string>
     */
    private function customerList(): array
    {
        return ArrayHelper::map(Customer::find()->where(['is_active' => 1])->orderBy('name')->all(), 'id', 'name');
    }

    /**
     * @return array<int, string>
     */
    private function recipeList(): array
    {
        $recipes = Recipe::find()
            ->where(['is_active' => 1])
            ->orderBy(['code' => SORT_ASC])
            ->all();
        $map = [];
        foreach ($recipes as $recipe) {
            $map[$recipe->id] = $recipe->code . ' — ' . $recipe->name;
        }

        return $map;
    }

    /**
     * @return array<int, string>
     */
    private function warehouseList(): array
    {
        return ArrayHelper::map(Warehouse::find()->where(['is_active' => 1])->orderBy('name')->all(), 'id', 'name');
    }
}