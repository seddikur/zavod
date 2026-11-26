<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\inventory\Supplier;
use app\modules\admin\models\inventory\SupplierSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SupplierController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new SupplierSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Supplier(['is_active' => true]);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Поставщик создан.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Поставщик обновлён.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): Response
    {
        try {
            $this->findModel($id)->delete();
            $this->flashSaved('Поставщик удалён.');
        } catch (\Throwable) {
            $this->flashError('Нельзя удалить: есть приходы. Отключите is_active.');
        }

        return $this->redirect(['index']);
    }

    private function findModel(int $id): Supplier
    {
        $model = Supplier::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Поставщик не найден.');
        }

        return $model;
    }
}