<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\AdminUserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\models\AdminUser;
use app\modules\admin\widgets\CrudActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Администраторы';
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'subtitle' => 'Учётные записи админ-панели',
    'actions' => [
        [
            'label' => '+ Добавить',
            'url' => ['create'],
            'options' => ['class' => 'cc-btn-primary'],
        ],
    ],
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', ['padded' => false]); ?>

<?php Pjax::begin(['id' => 'admin-users-pjax']); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover mb-0'],
    'options' => ['class' => 'cc-grid cc-filters'],
    'layout' => "{items}\n{summary}\n{pager}",
    'emptyText' => '<div class="cc-empty">Администраторов пока нет</div>',
    'columns' => [
        [
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:70px'],
        ],
        'username',
        [
            'attribute' => 'role',
            'filter' => AdminUser::roleOptions(),
            'format' => 'raw',
            'value' => static fn (AdminUser $model) => Html::tag(
                'span',
                Html::encode($model->getRoleLabel()),
                ['class' => 'cc-badge'],
            ),
        ],
        [
            'attribute' => 'status',
            'filter' => AdminUser::statusOptions(),
            'format' => 'raw',
            'value' => static fn (AdminUser $model) => Html::tag(
                'span',
                Html::encode($model->getStatusLabel()),
                ['class' => 'cc-badge'],
            ),
        ],
        [
            'attribute' => 'last_login_at',
            'format' => ['datetime', 'php:d.m.Y H:i'],
            'filter' => false,
        ],
        'last_login_ip',
        [
            'class' => CrudActionColumn::class,
            'actions' => ['view', 'update', 'delete'],
        ],
    ],
]) ?>
<?php Pjax::end(); ?>

<?php $this->endContent(); ?>
