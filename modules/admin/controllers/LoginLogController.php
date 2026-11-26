<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\AdminLoginLog;
use app\modules\admin\models\AdminLoginLogSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Журнал входов администраторов ({{%admin_login_log}}).
 * Только просмотр и удаление записей.
 */
class LoginLogController extends AdminController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex(): string
    {
        $searchModel = new AdminLoginLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Запись журнала удалена.');

        return $this->redirect(['index']);
    }

    protected function findModel(int $id): AdminLoginLog
    {
        $model = AdminLoginLog::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Запись не найдена.');
        }

        return $model;
    }
}
