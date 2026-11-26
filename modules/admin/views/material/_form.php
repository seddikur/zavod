<?php

declare(strict_types=1);

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var app\models\inventory\Material $model */
/** @var array<int, string> $categories */
/** @var array<int, string> $units */
/** @var array<int, string> $warehouses */
?>
<?php $form = ActiveForm::begin(require Yii::getAlias('@app/modules/admin/views/_partials/cc-form-config.php')); ?>
<div class="row g-3">
    <div class="col-md-3"><?= $form->field($model, 'code')->textInput() ?></div>
    <div class="col-md-5"><?= $form->field($model, 'name')->textInput() ?></div>
    <div class="col-md-4"><?= $form->field($model, 'category_id')->dropDownList($categories, ['class' => 'form-select']) ?></div>
    <div class="col-md-4"><?= $form->field($model, 'unit_id')->dropDownList($units, ['class' => 'form-select']) ?></div>
    <div class="col-md-4"><?= $form->field($model, 'default_warehouse_id')->dropDownList($warehouses, ['class' => 'form-select', 'prompt' => '']) ?></div>
    <div class="col-md-4"><?= $form->field($model, 'is_active')->dropDownList([1 => 'Да', 0 => 'Нет'], ['class' => 'form-select']) ?></div>
</div>
<div class="d-flex gap-2 mt-4">
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn cc-btn cc-btn-primary']) ?>
    <?= Html::a('Отмена', ['index'], ['class' => 'btn cc-btn']) ?>
</div>
<?php ActiveForm::end(); ?>