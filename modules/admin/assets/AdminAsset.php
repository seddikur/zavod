<?php

declare(strict_types=1);

namespace app\modules\admin\assets;

use app\assets\AppAsset;
use yii\web\AssetBundle;

/**
 * Asset bundle админ-панели (Clean Card UI).
 */
class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'vendor/fonts/nunito/nunito.css',
        'vendor/material-symbols/material-symbols-rounded.css',
        'css/admin-tokens.css',
        'css/admin.css',
        'css/admin-crud.css',
    ];
    public $js = [
        'js/admin.js',
    ];
    public $depends = [
        AppAsset::class,
    ];
}

