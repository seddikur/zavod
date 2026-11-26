<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\models\User;
use app\modules\admin\widgets\CrudActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Пользователи';
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'subtitle' => 'Пользователи фронтового сайта',
    'actions' => [
        [
            'label' => '+ Добавить',
            'url' => ['create'],
            'options' => ['class' => 'cc-btn-primary'],
        ],
    ],
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', ['padded' => false]); ?>

<?php Pjax::begin(['id' => 'users-pjax']); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover mb-0'],
    'options' => ['class' => 'cc-grid cc-filters'],
    'layout' => "{items}\n{summary}\n{pager}",
    'emptyText' => '<div class="cc-empty">Пользователей пока нет</div>',
    'columns' => [
        [
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:70px'],
        ],
        'phone',
        'email',
        [
            'attribute' => 'first_name',
            'label' => 'Имя',
            'value' => static fn (User $model) => $model->getFullName(),
        ],
        [
            'attribute' => 'status',
            'filter' => User::statusOptions(),
            'format' => 'raw',
            'value' => static fn (User $model) => Html::tag(
                'span',
                Html::encode($model->getStatusLabel()),
                ['class' => 'cc-badge'],
            ),
        ],
        [
            'attribute' => 'created_at',
            'format' => ['datetime', 'php:d.m.Y H:i'],
            'filter' => false,
        ],
        [
            'class' => CrudActionColumn::class,
            'actions' => ['view', 'update', 'delete'],
        ],
    ],
]) ?>
<?php Pjax::end(); ?>

<?php $this->endContent(); ?>
