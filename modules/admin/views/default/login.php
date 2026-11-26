<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\modules\admin\models\AdminLoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Вход в админ-панель';
$this->params['fullWidth'] = true;
?>
<div class="cc-login">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-7 col-12">
                <div class="cc-login-card">
                    <h1 class="cc-login-title">Админ-панель</h1>
                    <p class="cc-login-sub">Войдите, чтобы продолжить</p>

                    <?php $form = ActiveForm::begin([
                        'id' => 'admin-login-form',
                        'options' => ['class' => 'text-start'],
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}",
                            'labelOptions' => ['class' => 'form-label fw-semibold'],
                            'inputOptions' => ['class' => 'form-control'],
                        ],
                    ]); ?>

                    <?= $form->field($model, 'username')
                        ->textInput([
                            'autofocus' => true,
                            'autocomplete' => 'username',
                            'placeholder' => 'Логин',
                        ])
                        ->label('Логин') ?>

                    <?= $form->field($model, 'password')
                        ->passwordInput([
                            'autocomplete' => 'current-password',
                            'placeholder' => 'Пароль',
                        ])
                        ->label('Пароль') ?>

                    <div class="form-check form-switch d-flex align-items-center mb-3">
                        <?= Html::activeCheckbox($model, 'rememberMe', [
                            'class' => 'form-check-input',
                            'id' => 'adminRememberMe',
                            'label' => false,
                        ]) ?>
                        <label class="form-check-label mb-0 ms-3" for="adminRememberMe">Запомнить меня</label>
                    </div>

                    <div class="text-center">
                        <?= Html::submitButton(
                            'Войти',
                            [
                                'class' => 'btn btn-cc-primary w-100 my-3',
                                'name' => 'admin-login-button',
                            ],
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>

                <div class="cc-login-footer">
                    &copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?> &middot; Только для администраторов
                </div>
            </div>
        </div>
    </div>
</div>
