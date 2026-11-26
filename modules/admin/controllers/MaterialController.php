<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\inventory\Material;
use app\models\inventory\MaterialCategory;
use app\models\inventory\Unit;
use app\models\inventory\Warehouse;
use app\modules\admin\models\inventory\MaterialSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MaterialController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new MaterialSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Material(['is_active' => true]);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Материал создан.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model] + $this->formLists());
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Материал обновлён.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model] + $this->formLists());
    }

    public function actionDelete(int $id): Response
    {
        try {
            $this->findModel($id)->delete();
            $this->flashSaved('Материал удалён.');
        } catch (\Throwable) {
            $this->flashError('Нельзя удалить: есть движения. Отключите is_active.');
        }

        return $this->redirect(['index']);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function formLists(): array
    {
        return [
            'categories' => ArrayHelper::map(MaterialCategory::find()->orderBy('name')->all(), 'id', 'name'),
            'units' => ArrayHelper::map(Unit::find()->orderBy('name')->all(), 'id', 'short_name'),
            'warehouses' => ArrayHelper::map(Warehouse::find()->orderBy('name')->all(), 'id', 'name'),
        ];
    }

    private function findModel(int $id): Material
    {
        $model = Material::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Материал не найден.');
        }

        return $model;
    }
}