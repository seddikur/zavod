<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;
use yii\web\YiiAsset;

/**
 * Публичный фронт действующего АБЗ.
 */
class SiteAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/landing.css',
    ];
    public $js = [];
    public $jsOptions = [
        'position' => View::POS_END,
    ];
    public $depends = [
        YiiAsset::class,
    ];
}
