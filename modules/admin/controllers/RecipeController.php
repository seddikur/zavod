<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\inventory\Material;
use app\models\inventory\Recipe;
use app\models\inventory\RecipeItem;
use app\models\inventory\Unit;
use app\models\inventory\Warehouse;
use app\services\inventory\InventoryException;
use app\services\inventory\RecipeBatchConfig;
use app\services\inventory\RecipeVersionService;
use Yii;
use app\modules\admin\models\inventory\RecipeSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RecipeController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
                'new-version' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new RecipeSearch();
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
        $model = new Recipe([
            'recipe_date' => date('Y-m-d'),
            'is_active' => true,
        ]);
        RecipeBatchConfig::applyOutputDefaults($model);

        $itemRows = $this->emptyRecipeItems();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            RecipeBatchConfig::applyOutputDefaults($model);
            $itemRows = $this->loadItemRows(Yii::$app->request->post('RecipeItem', []));
            if ($model->validate() && $this->validateItems($itemRows)) {
                try {
                    $service = new RecipeVersionService();
                    $recipe = $service->createVersion($model->getAttributes(), $this->itemsPayload($itemRows));
                    Yii::$app->session->setFlash('success', 'Рецепт создан.');

                    return $this->redirect(['view', 'id' => $recipe->id]);
                } catch (InventoryException $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'itemRows' => $itemRows,
            'materials' => $this->materialList(),
            'units' => $this->unitList(),
            'warehouses' => $this->warehouseList(),
            'defaultKgUnitId' => $this->defaultKgUnitId(),
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        if (!$model->isCurrentVersion()) {
            Yii::$app->session->setFlash('warning', 'Редактируется только текущая версия. Создайте новую версию.');

            return $this->redirect(['view', 'id' => $id]);
        }

        $itemRows = $model->items;
        if ($itemRows === []) {
            $itemRows = $this->emptyRecipeItems(1);
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            RecipeBatchConfig::applyOutputDefaults($model);
            $itemRows = $this->loadItemRows(Yii::$app->request->post('RecipeItem', []));
            if ($model->validate() && $this->validateItems($itemRows)) {
                $tx = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save()) {
                        throw new InventoryException('Не удалось сохранить рецепт.');
                    }
                    RecipeItem::deleteAll(['recipe_id' => $model->id]);
                    foreach ($itemRows as $row) {
                        $row->recipe_id = (int) $model->id;
                        if (!$row->save()) {
                            throw new InventoryException('Ошибка строки рецепта.');
                        }
                    }
                    $tx->commit();
                    Yii::$app->session->setFlash('success', 'Рецепт обновлён.');

                    return $this->redirect(['view', 'id' => $model->id]);
                } catch (\Throwable $e) {
                    $tx->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'itemRows' => $itemRows,
            'materials' => $this->materialList(),
            'units' => $this->unitList(),
            'warehouses' => $this->warehouseList(),
            'defaultKgUnitId' => $this->defaultKgUnitId(),
        ]);
    }

    public function actionNewVersion(int $id): Response|string
    {
        $current = $this->findModel($id);
        $model = new Recipe($current->getAttributes(['code', 'name']));
        $model->recipe_date = date('Y-m-d');
        $model->is_active = true;
        RecipeBatchConfig::applyOutputDefaults($model);
        $itemRows = $current->items;

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            RecipeBatchConfig::applyOutputDefaults($model);
            $itemRows = $this->loadItemRows(Yii::$app->request->post('RecipeItem', []));
            if ($model->validate() && $this->validateItems($itemRows)) {
                try {
                    $service = new RecipeVersionService();
                    $recipe = $service->publishNewVersion(
                        $id,
                        $model->getAttributes(),
                        $this->itemsPayload($itemRows),
                    );
                    Yii::$app->session->setFlash('success', 'Новая версия рецепта создана.');

                    return $this->redirect(['view', 'id' => $recipe->id]);
                } catch (InventoryException $e) {
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('new-version', [
            'model' => $model,
            'itemRows' => $itemRows,
            'fromId' => $id,
            'materials' => $this->materialList(),
            'units' => $this->unitList(),
            'warehouses' => $this->warehouseList(),
            'defaultKgUnitId' => $this->defaultKgUnitId(),
        ]);
    }

    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);
        if (\app\models\inventory\ProductionOrder::find()->where(['recipe_id' => $id])->exists()) {
            Yii::$app->session->setFlash('error', 'Рецепт используется в отгрузках — удаление запрещено.');
        } else {
            RecipeItem::deleteAll(['recipe_id' => $id]);
            $model->delete();
            Yii::$app->session->setFlash('success', 'Рецепт удалён.');
        }

        return $this->redirect(['index']);
    }

    private function findModel(int $id): Recipe
    {
        $model = Recipe::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Рецепт не найден.');
        }

        return $model;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return RecipeItem[]
     */
    private function loadItemRows(array $rows): array
    {
        $models = [];
        foreach ($rows as $row) {
            if (empty($row['material_id']) && empty($row['quantity'])) {
                continue;
            }
            $item = new RecipeItem(['unit_id' => $this->defaultKgUnitId()]);
            $item->load($row, '');
            if (empty($item->unit_id)) {
                $item->unit_id = $this->defaultKgUnitId();
            }
            $models[] = $item;
        }

        return $models === [] ? $this->emptyRecipeItems(1) : $models;
    }

    /**
     * @param RecipeItem[] $rows
     */
    private function validateItems(array $rows): bool
    {
        $valid = true;
        foreach ($rows as $row) {
            if (!$row->validate(['material_id', 'warehouse_id', 'quantity', 'unit_id'])) {
                $valid = false;
            }
        }
        if ($rows === [] || ($rows[0]->material_id === null && $rows[0]->quantity === null)) {
            Yii::$app->session->setFlash('error', 'Добавьте хотя бы одну строку состава.');

            return false;
        }
        if (!$valid) {
            Yii::$app->session->setFlash('error', 'Заполните материал, склад и количество в каждой строке.');

            return false;
        }

        $sumError = RecipeBatchConfig::validateComponentSum($rows);
        if ($sumError !== null) {
            Yii::$app->session->setFlash('error', $sumError);

            return false;
        }

        return true;
    }

    /**
     * @param RecipeItem[] $rows
     * @return list<array<string, mixed>>
     */
    private function itemsPayload(array $rows): array
    {
        $payload = [];
        foreach ($rows as $row) {
            $payload[] = [
                'material_id' => (int) $row->material_id,
                'warehouse_id' => (int) $row->warehouse_id,
                'quantity' => (string) $row->quantity,
                'unit_id' => (int) $row->unit_id,
            ];
        }

        return $payload;
    }

    private function defaultKgUnitId(): int
    {
        $id = Unit::find()->select('id')->where(['short_name' => 'кг'])->scalar();
        if ($id !== false && $id !== null) {
            return (int) $id;
        }
        $id = Unit::find()->select('id')->orderBy(['id' => SORT_ASC])->scalar();

        return (int) $id;
    }

    /**
     * @return RecipeItem[]
     */
    private function emptyRecipeItems(int $count = 3): array
    {
        $kg = $this->defaultKgUnitId();
        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $rows[] = new RecipeItem(['unit_id' => $kg]);
        }

        return $rows;
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