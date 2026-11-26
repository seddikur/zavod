<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\inventory\Customer;
use app\modules\admin\models\inventory\CustomerSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CustomerController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Customer(['is_active' => true]);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Покупатель создан.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Покупатель обновлён.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): Response
    {
        try {
            $this->findModel($id)->delete();
            $this->flashSaved('Покупатель удалён.');
        } catch (\Throwable) {
            $this->flashError('Нельзя удалить: есть отгрузки. Отключите is_active.');
        }

        return $this->redirect(['index']);
    }

    private function findModel(int $id): Customer
    {
        $model = Customer::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Покупатель не найден.');
        }

        return $model;
    }
}