<?php

declare(strict_types=1);

/**
 * Карточка справочника в стиле /admin/user/view.
 *
 * @var yii\web\View $this
 * @var string $title
 * @var string|null $subtitle
 * @var yii\db\ActiveRecord $model
 * @var array<int, mixed> $detailAttributes
 * @var string $deleteConfirm
 * @var array<int, array<string, mixed>>|null $extraActions Доп. кнопки после «Изменить»
 * @var string|null $afterDetail HTML после DetailView
 */

use yii\helpers\Html;
use yii\widgets\DetailView;

$subtitle = $subtitle ?? null;
$extraActions = $extraActions ?? [];
$afterDetail = $afterDetail ?? '';

$actions = [
    ['label' => 'К списку', 'url' => ['index']],
    ['label' => 'Изменить', 'url' => ['update', 'id' => $model->id], 'options' => ['class' => 'cc-btn-primary']],
    ...$extraActions,
    [
        'label' => 'Удалить',
        'url' => ['delete', 'id' => $model->id],
        'options' => [
            'class' => 'cc-btn-danger',
            'data' => [
                'confirm' => $deleteConfirm,
                'method' => 'post',
            ],
        ],
    ],
];
?>

<?= $this->render('/_partials/page-header', [
    'title' => $title,
    'subtitle' => $subtitle,
    'actions' => $actions,
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php', ['padded' => false]); ?>
<div class="cc-detail">
    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-bordered mb-0'],
        'attributes' => $detailAttributes,
    ]) ?>
</div>
<?= $afterDetail ?>
<?php $this->endContent(); ?>