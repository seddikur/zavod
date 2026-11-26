<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\User $model */

use app\models\User;
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
        <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'password')->passwordInput([
            'maxlength' => true,
            'value' => '',
            'placeholder' => $model->isNewRecord ? '' : 'Оставьте пустым, чтобы не менять',
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'status')->dropDownList(
            User::statusOptions(),
            ['class' => 'form-select'],
        ) ?>
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
