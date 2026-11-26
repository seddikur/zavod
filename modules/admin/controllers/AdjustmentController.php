<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\enums\AdjustmentStatus;
use app\models\inventory\Adjustment;
use app\models\inventory\AdjustmentItem;
use app\models\inventory\Material;
use app\models\inventory\Unit;
use app\models\inventory\Warehouse;
use app\services\inventory\AdjustmentCompensationService;
use app\services\inventory\AdjustmentPostingService;
use app\services\inventory\InventoryException;
use Yii;
use app\modules\admin\models\inventory\AdjustmentSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AdjustmentController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
                'post' => ['POST'],
                'compensate' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new AdjustmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView(int $id): string
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'items' => $model->items,
        ]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Adjustment([
            'doc_date' => date('Y-m-d'),
            'status' => AdjustmentStatus::Draft->value,
            'created_by' => (int) Yii::$app->adminUser->id,
        ]);
        $itemRows = [new AdjustmentItem()];

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->created_by = (int) Yii::$app->adminUser->id;
            $itemRows = $this->loadItemRows(Yii::$app->request->post('AdjustmentItem', []));
            if ($model->validate() && $this->validateItems($itemRows) && $this->saveWithItems($model, $itemRows, true)) {
                Yii::$app->session->setFlash('success', 'Корректировка создана.');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'itemRows' => $itemRows,
            'materials' => $this->materialList(),
            'units' => $this->unitList(),
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

        $itemRows = $model->items;
        if ($itemRows === []) {
            $itemRows = [new AdjustmentItem()];
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $itemRows = $this->loadItemRows(Yii::$app->request->post('AdjustmentItem', []));
            if ($model->validate() && $this->validateItems($itemRows) && $this->saveWithItems($model, $itemRows, false)) {
                Yii::$app->session->setFlash('success', 'Корректировка обновлена.');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'itemRows' => $itemRows,
            'materials' => $this->materialList(),
            'units' => $this->unitList(),
            'warehouses' => $this->warehouseList(),
        ]);
    }

    public function actionPost(int $id): Response
    {
        try {
            (new AdjustmentPostingService())->post($id);
            Yii::$app->session->setFlash('success', 'Корректировка проведена.');
        } catch (InventoryException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionCompensate(int $id): Response
    {
        try {
            $newId = (new AdjustmentCompensationService())->compensate(
                $id,
                (int) Yii::$app->adminUser->id,
                true,
            );
            Yii::$app->session->setFlash('success', 'Создана и проведена компенсирующая корректировка.');

            return $this->redirect(['view', 'id' => $newId]);
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
            AdjustmentItem::deleteAll(['adjustment_id' => $id]);
            $model->delete();
            Yii::$app->session->setFlash('success', 'Корректировка удалена.');
        }

        return $this->redirect(['index']);
    }

    /**
     * @param AdjustmentItem[] $itemRows
     */
    private function saveWithItems(Adjustment $model, array $itemRows, bool $isNew): bool
    {
        $tx = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                $tx->rollBack();

                return false;
            }
            if (!$isNew) {
                AdjustmentItem::deleteAll(['adjustment_id' => $model->id]);
            }
            foreach ($itemRows as $row) {
                $row->adjustment_id = (int) $model->id;
                if (!$row->save()) {
                    $tx->rollBack();

                    return false;
                }
            }
            $tx->commit();

            return true;
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());

            return false;
        }
    }

    private function findModel(int $id): Adjustment
    {
        $model = Adjustment::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Корректировка не найдена.');
        }

        return $model;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return AdjustmentItem[]
     */
    private function loadItemRows(array $rows): array
    {
        $models = [];
        foreach ($rows as $row) {
            if (empty($row['material_id']) && empty($row['quantity'])) {
                continue;
            }
            $item = new AdjustmentItem();
            $item->load($row, '');
            $models[] = $item;
        }

        return $models === [] ? [new AdjustmentItem()] : $models;
    }

    /**
     * @param AdjustmentItem[] $rows
     */
    private function validateItems(array $rows): bool
    {
        foreach ($rows as $row) {
            if (!$row->validate()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, string>
     */
    private function materialList(): array
    {
        return ArrayHelper::map(Material::find()->where(['is_active' => 1])->orderBy('name')->all(), 'id', 'name');
    }

    /**
     * @return array<int, string>
     */
    private function unitList(): array
    {
        return ArrayHelper::map(Unit::find()->orderBy('name')->all(), 'id', 'short_name');
    }

    /**
     * @return array<int, string>
     */
    private function warehouseList(): array
    {
        return ArrayHelper::map(Warehouse::find()->where(['is_active' => 1])->orderBy('name')->all(), 'id', 'name');
    }
}