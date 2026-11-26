<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\PageViewSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\models\PageView;
use app\modules\admin\widgets\CrudActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Просмотры страниц';
?>

<?= $this->render('/_partials/page-header', [
    'title' => $this->title,
    'subtitle' => 'Журнал посещений публичных страниц и UTM',
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', ['padded' => false]); ?>

<?php Pjax::begin(['id' => 'page-view-pjax']); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover mb-0'],
    'options' => ['class' => 'cc-grid cc-filters'],
    'layout' => "{items}\n{summary}\n{pager}",
    'emptyText' => '<div class="cc-empty">Просмотров пока нет</div>',
    'columns' => [
        [
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:70px'],
        ],
        [
            'attribute' => 'path',
            'contentOptions' => ['class' => 'text-break', 'style' => 'max-width:220px'],
        ],
        'route',
        'utm_source',
        'utm_medium',
        'ip',
        [
            'attribute' => 'is_bot',
            'filter' => [0 => 'Нет', 1 => 'Да'],
            'format' => 'raw',
            'value' => static fn (PageView $model) => Html::tag(
                'span',
                $model->is_bot ? 'Да' : 'Нет',
                ['class' => 'cc-badge'],
            ),
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
