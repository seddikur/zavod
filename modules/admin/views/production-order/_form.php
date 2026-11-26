<?php

declare(strict_types=1);

/** @var app\models\inventory\ProductionOrder $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>
<?php $form = ActiveForm::begin(require Yii::getAlias('@app/modules/admin/views/_partials/cc-form-config.php')); ?>
<div class="row g-3">
    <div class="col-md-3"><?= $form->field($model, 'order_number')->textInput() ?></div>
    <div class="col-md-3"><?= $form->field($model, 'doc_date')->input('date') ?></div>
    <div class="col-md-3"><?= $form->field($model, 'customer_id')->dropDownList($customers, ['class' => 'form-select']) ?></div>
    <div class="col-md-3"><?= $form->field($model, 'recipe_id')->dropDownList($recipes, ['class' => 'form-select']) ?></div>
    <div class="col-md-4"><?= $form->field($model, 'default_warehouse_id')->dropDownList($warehouses, ['class' => 'form-select']) ?></div>
    <div class="col-md-4"><?= $form->field($model, 'planned_quantity')->textInput(['step' => '0.01', 'inputmode' => 'decimal']) ?></div>
    <div class="col-md-4"><?= $form->field($model, 'actual_quantity')->textInput(['step' => '0.01', 'inputmode' => 'decimal']) ?></div>
</div>
<div class="d-flex gap-2 mt-4">
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn cc-btn cc-btn-primary']) ?>
    <?= Html::a('Отмена', ['index'], ['class' => 'btn cc-btn']) ?>
</div>
<?php ActiveForm::end(); ?>