<?php

declare(strict_types=1);

/**
 * Общая конфигурация ActiveForm (как у /admin/user).
 */
return [
    'options' => ['class' => 'cc-form'],
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
    ],
];