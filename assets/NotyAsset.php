<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Локальные ассеты Noty + Animate.css.
 */
class NotyAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/noty.min.css',
        'css/animate.min.css',
        'css/noty-theme.css',
    ];

    public $js = [
        'js/noty.min.js',
    ];

    public $depends = [
        YiiAsset::class,
    ];
}
