<?php

declare(strict_types=1);

namespace app\modules\admin\controllers;

use app\models\AdminUser;
use app\modules\admin\models\AdminUserSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * CRUD администраторов панели ({{%admin_users}}).
 */
class AdminUserController extends AdminController
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
        $searchModel = new AdminUserSearch();
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

    public function actionCreate(): Response|string
    {
        $model = new AdminUser(['scenario' => 'create']);

        if ($model->load($this->request->post())) {
            $model->generateAuthKey();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Администратор создан.');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        if ($model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Администратор обновлён.');

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);
        $currentId = (int) (Yii::$app->adminUser->id ?? 0);

        if ((int) $model->id === $currentId) {
            throw new ForbiddenHttpException('Нельзя отключить свою учётную запись.');
        }

        $model->status = AdminUser::STATUS_DISABLED;
        $model->save(false, ['status', 'updated_at']);
        Yii::$app->session->setFlash('success', 'Администратор отключён.');

        return $this->redirect(['index']);
    }

    protected function findModel(int $id): AdminUser
    {
        $model = AdminUser::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Администратор не найден.');
        }

        return $model;
    }
}
