<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;
use yii\web\HttpException;

$this->title = $name;
$statusCode = $exception instanceof HttpException ? $exception->statusCode : 500;
?>
<div class="site-error">
    <div class="site-error-content">
        <h1 style="font-size: clamp(48px, 8vw, 96px); font-weight: 700; color: var(--text-muted); margin-bottom: var(--space-3);">
            <?= Html::encode((string) $statusCode) ?>
        </h1>
        <h2 style="font-size: clamp(22px, 3vw, 32px); margin-bottom: var(--space-4);">
            <?= Html::encode($message) ?>
        </h2>
        <p style="color: var(--text-muted); margin-bottom: var(--space-6);">
            На сервере произошла ошибка при обработке запроса.
            Если считаете, что это сбой — напишите нам.
        </p>
        <?= Html::a('На главную', Yii::$app->homeUrl, ['class' => 'btn btn--accent btn--lg']) ?>
    </div>
</div>
