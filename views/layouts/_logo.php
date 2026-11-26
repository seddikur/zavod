<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var bool $small */
/** @var string $brand */

use yii\helpers\Html;
use yii\helpers\Url;

$small = $small ?? false;
$brand = $brand ?? (Yii::$app->params['site']['common']['brand'] ?? Yii::$app->name);
$classes = 'logo' . ($small ? ' logo--small' : '');
?>
<a href="<?= Url::home() ?>" class="<?= Html::encode($classes) ?>">
    <?php if ($small): ?>
        <span class="logo__mark" aria-hidden="true">
            <span></span><span></span><span></span>
            <span></span><span class="is-accent"></span><span></span>
            <span></span><span></span><span></span>
        </span>
    <?php else: ?>
        <svg class="logo__svg" width="26" height="26" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <g>
                <rect x="0.5" y="0.5" width="7" height="7" rx="1.5" fill="#A0A6B1"/>
                <rect x="0" y="0" width="7" height="7" rx="1.5" fill="#CDD3DB"/>
            </g>
            <g>
                <rect x="9.5" y="0.5" width="7" height="7" rx="1.5" fill="#A0A6B1"/>
                <rect x="9" y="0" width="7" height="7" rx="1.5" fill="#CDD3DB"/>
            </g>
            <g>
                <rect x="18.5" y="0.5" width="7" height="7" rx="1.5" fill="#A0A6B1"/>
                <rect x="18" y="0" width="7" height="7" rx="1.5" fill="#CDD3DB"/>
            </g>
            <g>
                <rect x="0.5" y="9.5" width="7" height="7" rx="1.5" fill="#A0A6B1"/>
                <rect x="0" y="9" width="7" height="7" rx="1.5" fill="#CDD3DB"/>
            </g>
            <g>
                <rect x="9.5" y="9.5" width="7" height="7" rx="1.5" fill="#8DBD2A"/>
                <rect x="9" y="9" width="7" height="7" rx="1.5" fill="#C6F24E"/>
            </g>
            <g>
                <rect x="18.5" y="9.5" width="7" height="7" rx="1.5" fill="#A0A6B1"/>
                <rect x="18" y="9" width="7" height="7" rx="1.5" fill="#CDD3DB"/>
            </g>
            <g>
                <rect x="0.5" y="18.5" width="7" height="7" rx="1.5" fill="#A0A6B1"/>
                <rect x="0" y="18" width="7" height="7" rx="1.5" fill="#CDD3DB"/>
            </g>
            <g>
                <rect x="9.5" y="18.5" width="7" height="7" rx="1.5" fill="#A0A6B1"/>
                <rect x="9" y="18" width="7" height="7" rx="1.5" fill="#CDD3DB"/>
            </g>
            <g>
                <rect x="18.5" y="18.5" width="7" height="7" rx="1.5" fill="#A0A6B1"/>
                <rect x="18" y="18" width="7" height="7" rx="1.5" fill="#CDD3DB"/>
            </g>
        </svg>
    <?php endif ?>
    <span class="logo__text"><?= Html::encode($brand) ?></span>
</a>
