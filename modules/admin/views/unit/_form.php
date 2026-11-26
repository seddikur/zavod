<?php

declare(strict_types=1);

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var app\models\inventory\Unit $model */
/** @var array<int, string> $baseUnits */
?>
<?php $form = ActiveForm::begin(require Yii::getAlias('@app/modules/admin/views/_partials/cc-form-config.php')); ?>
<div class="row g-3">
    <div class="col-md-6"><?= $form->field($model, 'name')->textInput() ?></div>
    <div class="col-md-6"><?= $form->field($model, 'short_name')->textInput() ?></div>
    <div class="col-md-6"><?= $form->field($model, 'base_unit_id')->dropDownList($baseUnits, ['class' => 'form-select', 'prompt' => '— базовая (кг) —']) ?></div>
    <div class="col-md-6"><?= $form->field($model, 'factor')->textInput() ?></div>
</div>
<div class="d-flex gap-2 mt-4">
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn cc-btn cc-btn-primary']) ?>
    <?= Html::a('Отмена', ['index'], ['class' => 'btn cc-btn']) ?>
</div>
<?php ActiveForm::end(); ?>