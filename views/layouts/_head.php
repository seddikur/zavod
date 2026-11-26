<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use app\assets\AppAsset;
use app\assets\SiteAsset;

if (!empty($this->params['authLayout'])) {
    AppAsset::register($this);
} else {
    SiteAsset::register($this);
}

$this->registerCsrfMetaTags();
$this->registerMetaTag(
    ['charset' => Yii::$app->charset],
    'charset',
);
$this->registerMetaTag(
    [
        'name' => 'viewport',
        'content' => 'width=device-width, initial-scale=1.0',
    ],
);
if (!empty($this->params['meta_description'])) {
    $this->registerMetaTag(
        [
            'name' => 'description',
            'content' => $this->params['meta_description'],
        ],
    );
}
if (!empty($this->params['meta_keywords'])) {
    $this->registerMetaTag(
        [
            'name' => 'keywords',
            'content' => $this->params['meta_keywords'],
        ],
    );
}
$this->registerLinkTag(
    [
        'rel' => 'icon',
        'type' => 'image/x-icon',
        'href' => Yii::getAlias('@web/favicon.ico'),
    ],
);

if (empty($this->params['authLayout'])) {
    $this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.googleapis.com']);
    $this->registerLinkTag(
        [
            'rel' => 'preconnect',
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => true,
        ],
    );
    $this->registerCssFile(
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap',
        ['depends' => [SiteAsset::class]],
    );
}
