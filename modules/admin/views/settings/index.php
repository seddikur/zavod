<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\SettingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\modules\admin\widgets\CrudActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Настройки';
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'subtitle' => 'Ключ-значение настроек сайта',
    'actions' => [
        [
            'label' => '+ Добавить',
            'url' => ['create'],
            'options' => ['class' => 'cc-btn-primary'],
        ],
    ],
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', [
    'padded' => false,
]); ?>

<?php Pjax::begin(['id' => 'settings-pjax']); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover mb-0'],
    'options' => ['class' => 'cc-grid cc-filters'],
    'layout' => "{items}\n{summary}\n{pager}",
    'emptyText' => '<div class="cc-empty">Настроек пока нет</div>',
    'columns' => [
        [
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:70px'],
        ],
        'key',
        [
            'attribute' => 'value',
            'format' => 'ntext',
            'contentOptions' => ['class' => 'text-break', 'style' => 'max-width:280px'],
        ],
        'description',
        [
            'class' => CrudActionColumn::class,
            'actions' => ['view', 'update', 'delete'],
        ],
    ],
]) ?>
<?php Pjax::end(); ?>

<?php $this->endContent(); ?>
