<?php

declare(strict_types=1);

namespace app\commands;

use app\services\inventory\AsphaltPresetSeeder;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Пресеты завода (АБЗ / РБУ).
 */
class PlantController extends Controller
{
    public $force = false;

    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), ['force']);
    }

    public function optionAliases(): array
    {
        return ['f' => 'force'];
    }

    /**
     * Загрузить справочники пресета «асфальт» (этап 1).
     */
    public function actionSeedAsphalt(): int
    {
        (new AsphaltPresetSeeder())->seed((bool) $this->force);

        return ExitCode::OK;
    }
}