<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\inventory\MaterialCategory;
use app\modules\admin\models\inventory\MaterialCategorySearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MaterialCategoryController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new MaterialCategorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate(): Response|string
    {
        $model = new MaterialCategory();
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Категория создана.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Категория обновлена.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): Response
    {
        try {
            $this->findModel($id)->delete();
            $this->flashSaved('Категория удалена.');
        } catch (\Throwable) {
            $this->flashError('Нельзя удалить: есть связанные материалы.');
        }

        return $this->redirect(['index']);
    }

    private function findModel(int $id): MaterialCategory
    {
        $model = MaterialCategory::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        return $model;
    }
}