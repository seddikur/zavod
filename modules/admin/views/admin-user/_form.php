<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\AdminUser $model */

use app\models\AdminUser;
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
        <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'autocomplete' => 'username']) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'password')->passwordInput([
            'maxlength' => true,
            'value' => '',
            'autocomplete' => 'new-password',
            'placeholder' => $model->isNewRecord ? '' : 'Оставьте пустым, чтобы не менять',
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'role')->dropDownList(
            AdminUser::roleOptions(),
            ['class' => 'form-select'],
        ) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'status')->dropDownList(
            AdminUser::statusOptions(),
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
