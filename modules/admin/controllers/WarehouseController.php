<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\inventory\Warehouse;
use app\modules\admin\models\inventory\WarehouseSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class WarehouseController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new WarehouseSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Warehouse(['is_active' => true]);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Склад создан.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Склад обновлён.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): Response
    {
        try {
            $this->findModel($id)->delete();
            $this->flashSaved('Склад удалён.');
        } catch (\Throwable) {
            $this->flashError('Нельзя удалить: склад используется в движениях. Отключите is_active.');
        }

        return $this->redirect(['index']);
    }

    private function findModel(int $id): Warehouse
    {
        $model = Warehouse::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Склад не найден.');
        }

        return $model;
    }
}