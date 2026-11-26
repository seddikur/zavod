<?php

declare(strict_types=1);

/**
 * Сетка списка в стиле /admin/user (Pjax + фильтры).
 *
 * @var yii\web\View $this
 * @var string $title
 * @var string|null $subtitle
 * @var string $pjaxId
 * @var string $emptyText
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var yii\base\Model $searchModel
 * @var array<int, mixed> $gridColumns
 */

use app\modules\admin\widgets\CrudActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

$subtitle = $subtitle ?? null;
$showCreate = $showCreate ?? true;
$createLabel = $createLabel ?? '+ Добавить';
$crudActions = $crudActions ?? ['view', 'update', 'delete'];
?>

<?= $this->render('/_partials/page-header', [
    'title' => $title,
    'subtitle' => $subtitle,
    'actions' => $showCreate ? [
        ['label' => $createLabel, 'url' => ['create'], 'options' => ['class' => 'cc-btn-primary']],
    ] : [],
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', ['padded' => false]); ?>

<?php Pjax::begin(['id' => $pjaxId]); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => ['class' => 'table table-hover mb-0'],
    'options' => ['class' => 'cc-grid cc-filters'],
    'layout' => "{items}\n{summary}\n{pager}",
    'emptyText' => '<div class="cc-empty">' . htmlspecialchars($emptyText, ENT_QUOTES, 'UTF-8') . '</div>',
    'columns' => array_merge(
        $gridColumns,
        $crudActions === [] ? [] : [[
            'class' => CrudActionColumn::class,
            'actions' => $crudActions,
        ]],
    ),
]) ?>
<?php Pjax::end(); ?>

<?php $this->endContent(); ?>