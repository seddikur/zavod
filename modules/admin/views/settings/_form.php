<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Setting $model */
/** @var yii\widgets\ActiveForm $form */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'cc-form'],
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
    ],
]); ?>

<div class="row g-3">
    <div class="col-md-6">
        <?= $form->field($model, 'key')->textInput([
            'maxlength' => true,
            'readonly' => !$model->isNewRecord,
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-12">
        <?php if ($model->isBoolean()): ?>
            <?= $form->field($model, 'value')->dropDownList(
                ['1' => 'Включено', '0' => 'Выключено'],
                ['class' => 'form-select'],
            ) ?>
        <?php else: ?>
            <?= $form->field($model, 'value')->textarea(['rows' => 4]) ?>
        <?php endif; ?>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <?= Html::submitButton(
        $model->isNewRecord ? 'Создать' : 'Сохранить',
        ['class' => 'btn cc-btn cc-btn-primary'],
    ) ?>
    <?= Html::a('Отмена', ['index'], ['class' => 'btn cc-btn']) ?>
</div>

<?php ActiveForm::end(); ?>
