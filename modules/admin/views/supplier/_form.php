<?php

declare(strict_types=1);

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var app\models\inventory\Supplier $model */
?>
<?php $form = ActiveForm::begin(require Yii::getAlias('@app/modules/admin/views/_partials/cc-form-config.php')); ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'inn')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'is_active')->dropDownList([1 => 'Да', 0 => 'Нет'], ['class' => 'form-select']) ?>
<div class="d-flex gap-2 mt-4">
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn cc-btn cc-btn-primary']) ?>
    <?= Html::a('Отмена', ['index'], ['class' => 'btn cc-btn']) ?>
</div>
<?php ActiveForm::end(); ?>