<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\AdminLoginLogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\modules\admin\widgets\CrudActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Журнал входов';
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'subtitle' => 'История авторизаций администраторов',
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', ['padded' => false]); ?>

<?php Pjax::begin(['id' => 'login-log-pjax']); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover mb-0'],
    'options' => ['class' => 'cc-grid cc-filters'],
    'layout' => "{items}\n{summary}\n{pager}",
    'emptyText' => '<div class="cc-empty">Записей пока нет</div>',
    'columns' => [
        [
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:70px'],
        ],
        'username',
        'ip',
        [
            'attribute' => 'user_agent',
            'format' => 'ntext',
            'contentOptions' => ['class' => 'text-break', 'style' => 'max-width:320px'],
        ],
        [
            'attribute' => 'created_at',
            'format' => ['datetime', 'php:d.m.Y H:i:s'],
            'filter' => false,
        ],
        [
            'class' => CrudActionColumn::class,
            'actions' => ['view', 'delete'],
        ],
    ],
]) ?>
<?php Pjax::end(); ?>

<?php $this->endContent(); ?>
