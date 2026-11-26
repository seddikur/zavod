<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\inventory\Unit;
use app\modules\admin\models\inventory\UnitSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UnitController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = ['class' => VerbFilter::class, 'actions' => ['delete' => ['POST']]];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new UnitSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Unit(['factor' => '1']);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Единица создана.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'baseUnits' => $this->baseUnitList(),
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);
        if ($model->load($this->request->post()) && $model->save()) {
            $this->flashSaved('Единица обновлена.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'baseUnits' => $this->baseUnitList(),
        ]);
    }

    public function actionDelete(int $id): Response
    {
        try {
            $this->findModel($id)->delete();
            $this->flashSaved('Единица удалена.');
        } catch (\Throwable) {
            $this->flashError('Нельзя удалить: единица используется в справочниках.');
        }

        return $this->redirect(['index']);
    }

    /**
     * @return array<int, string>
     */
    private function baseUnitList(): array
    {
        return ArrayHelper::map(Unit::find()->orderBy('name')->all(), 'id', 'short_name');
    }

    private function findModel(int $id): Unit
    {
        $model = Unit::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Единица не найдена.');
        }

        return $model;
    }
}