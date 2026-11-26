<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\enums\DocumentStatus;
use app\models\inventory\Material;
use app\models\inventory\Receipt;
use app\models\inventory\ReceiptItem;
use app\models\inventory\Supplier;
use app\models\inventory\Unit;
use app\models\inventory\Warehouse;
use app\services\inventory\InventoryException;
use app\services\inventory\ReceiptPostingService;
use app\services\inventory\ReversalService;
use Yii;
use app\modules\admin\models\inventory\ReceiptSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ReceiptController extends AdminController
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
        $searchModel = new ReceiptSearch();
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
        $model = new Receipt([
            'doc_date' => date('Y-m-d'),
            'status' => DocumentStatus::Draft->value,
            'created_by' => (int) Yii::$app->adminUser->id,
        ]);
        $itemRows = [new ReceiptItem()];

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->created_by = (int) Yii::$app->adminUser->id;
            $itemRows = $this->loadItemRows(Yii::$app->request->post('ReceiptItem', []));
            if ($model->validate() && $this->validateItems($itemRows) && $this->saveWithItems($model, $itemRows, true)) {
                Yii::$app->session->setFlash('success', 'Приход создан.');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'itemRows' => $itemRows,
            'materials' => $this->materialList(),
            'units' => $this->unitList(),
            'suppliers' => $this->supplierList(),
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
            $itemRows = [new ReceiptItem()];
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $itemRows = $this->loadItemRows(Yii::$app->request->post('ReceiptItem', []));
            if ($model->validate() && $this->validateItems($itemRows) && $this->saveWithItems($model, $itemRows, false)) {
                Yii::$app->session->setFlash('success', 'Приход обновлён.');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'itemRows' => $itemRows,
            'materials' => $this->materialList(),
            'units' => $this->unitList(),
            'suppliers' => $this->supplierList(),
            'warehouses' => $this->warehouseList(),
        ]);
    }

    public function actionPost(int $id): Response
    {
        try {
            (new ReceiptPostingService())->post($id);
            Yii::$app->session->setFlash('success', 'Приход проведён.');
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
            $stornoId = (new ReversalService())->reverseReceipt($id, (int) Yii::$app->adminUser->id, $reason);
            Yii::$app->session->setFlash('success', 'Сторно проведено (#' . $stornoId . ').');

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
            ReceiptItem::deleteAll(['receipt_id' => $id]);
            $model->delete();
            Yii::$app->session->setFlash('success', 'Приход удалён.');
        }

        return $this->redirect(['index']);
    }

    /**
     * @param ReceiptItem[] $itemRows
     */
    private function saveWithItems(Receipt $model, array $itemRows, bool $isNew): bool
    {
        $tx = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save()) {
                $tx->rollBack();

                return false;
            }
            if (!$isNew) {
                ReceiptItem::deleteAll(['receipt_id' => $model->id]);
            }
            $total = '0.00';
            foreach ($itemRows as $row) {
                $row->receipt_id = (int) $model->id;
                $row->beforeValidate();
                if (!$row->save()) {
                    $tx->rollBack();

                    return false;
                }
                $total = bcadd($total, (string) $row->amount, 2);
            }
            $model->total_amount = $total;
            $model->save(false, ['total_amount']);
            $tx->commit();

            return true;
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());

            return false;
        }
    }

    private function findModel(int $id): Receipt
    {
        $model = Receipt::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Документ не найден.');
        }

        return $model;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return ReceiptItem[]
     */
    private function loadItemRows(array $rows): array
    {
        $models = [];
        foreach ($rows as $row) {
            if (empty($row['material_id']) && empty($row['quantity'])) {
                continue;
            }
            $item = new ReceiptItem();
            $item->load($row, '');
            $models[] = $item;
        }

        return $models === [] ? [new ReceiptItem()] : $models;
    }

    /**
     * @param ReceiptItem[] $rows
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
    private function supplierList(): array
    {
        return ArrayHelper::map(Supplier::find()->where(['is_active' => 1])->orderBy('name')->all(), 'id', 'name');
    }

    /**
     * @return array<int, string>
     */
    private function warehouseList(): array
    {
        return ArrayHelper::map(Warehouse::find()->where(['is_active' => 1])->orderBy('name')->all(), 'id', 'name');
    }
}