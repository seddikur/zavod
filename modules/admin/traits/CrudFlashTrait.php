<?php

declare(strict_types=1);

namespace app\modules\admin\traits;

use Yii;

trait CrudFlashTrait
{
    protected function flashSaved(string $message): void
    {
        Yii::$app->session->setFlash('success', $message);
    }

    protected function flashError(string $message): void
    {
        Yii::$app->session->setFlash('error', $message);
    }
}