<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\inventory\Recipe $model */
/** @var app\models\inventory\RecipeItem[] $itemRows */
/** @var array<int, string> $materials */
/** @var array<int, string> $units */
/** @var array<int, string> $warehouses */
/** @var int $defaultKgUnitId */

use app\services\inventory\RecipeBatchConfig;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$recipeBatchLabel = RecipeBatchConfig::batchKgLabel();

$formConfig = require Yii::getAlias('@app/modules/admin/views/_partials/cc-form-config.php');
$formConfig['options']['id'] = 'recipe-form';

?>
<?php $form = ActiveForm::begin($formConfig); ?>

<div class="row g-3">
    <div class="col-md-3">
        <?= $form->field($model, 'code')->textInput(['maxlength' => true])
            ->hint('Короткий код, например MA-22') ?>
    </div>
    <div class="col-md-5">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'recipe_date')->input('date') ?>
    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'is_active')->dropDownList([1 => 'Да', 0 => 'Нет'], ['class' => 'form-select']) ?>
    </div>
</div>

<p class="text-muted small mb-0 mt-2">
    Состав задаётся на партию <strong><?= Html::encode($recipeBatchLabel) ?></strong>
    (параметр <code>recipeComponentBatchKg</code> в <code>config/inventory.php</code>).
    Сумма килограмм по всем материалам должна быть ровно <?= Html::encode($recipeBatchLabel) ?>.
</p>

<h3 class="h6 mt-4 mb-0">Состав смеси</h3>

<?= $this->render('/_partials/recipe-items-table', [
    'itemRows' => $itemRows,
    'materials' => $materials,
    'warehouses' => $warehouses,
    'defaultKgUnitId' => $defaultKgUnitId,
    'recipeBatchLabel' => $recipeBatchLabel,
]) ?>

<div class="d-flex gap-2 mt-4">
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn cc-btn cc-btn-primary']) ?>
    <?= Html::a('Отмена', ['index'], ['class' => 'btn cc-btn']) ?>
</div>

<?php ActiveForm::end(); ?>