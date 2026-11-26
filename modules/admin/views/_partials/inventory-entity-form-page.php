<?php

declare(strict_types=1);

/**
 * Обёртка create/update как у /admin/user.
 *
 * @var yii\web\View $this
 * @var string $title
 * @var yii\db\ActiveRecord $model
 * @var string $formView `_form` относительно views/{controllerId} или путь с `/` / `@`
 * @var array<string, mixed>|null $formParams Аргументы для $formView (по умолчанию только model)
 */

$formParams = $formParams ?? ['model' => $model];
$formViewFile = $formView;
if ($formViewFile !== '' && $formViewFile[0] !== '/' && $formViewFile[0] !== '@') {
    $formViewFile = '/' . $this->context->id . '/' . ltrim($formView, '/');
}
$isNew = $model->isNewRecord;
$headerActions = [['label' => 'К списку', 'url' => ['index']]];
if (!$isNew) {
    $headerActions[] = ['label' => 'Просмотр', 'url' => ['view', 'id' => $model->id]];
}
?>

<?= $this->render('/_partials/page-header', [
    'title' => $title,
    'actions' => $headerActions,
]) ?>

<?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php'); ?>
<?= $this->render($formViewFile, $formParams) ?>
<?php $this->endContent(); ?>