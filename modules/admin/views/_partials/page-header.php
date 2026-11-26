<?php

declare(strict_types=1);

/**
 * Общий заголовок CRUD-страницы (Clean Card UI).
 *
 * @var yii\web\View $this
 * @var string $title
 * @var string|null $subtitle
 * @var array<int, array{label:string,url?:array|string,options?:array,type?:string}> $actions
 */

use yii\helpers\Html;

$subtitle = $subtitle ?? null;
$actions = $actions ?? [];
?>
<div class="cc-page-header">
    <div class="cc-page-header__text">
        <h1 class="cc-page-title"><?= Html::encode($title) ?></h1>
        <?php if ($subtitle !== null && $subtitle !== ''): ?>
            <p class="cc-page-subtitle"><?= Html::encode($subtitle) ?></p>
        <?php endif; ?>
    </div>

    <?php if ($actions !== []): ?>
        <div class="cc-page-actions">
            <?php foreach ($actions as $action): ?>
                <?php
                $type = $action['type'] ?? 'link';
                $options = $action['options'] ?? [];
                $class = trim(($options['class'] ?? '') . ' btn cc-btn');
                $options['class'] = $class;
                $label = $action['label'];
                ?>
                <?php if ($type === 'submit'): ?>
                    <?= Html::submitButton($label, $options) ?>
                <?php else: ?>
                    <?= Html::a($label, $action['url'] ?? '#', $options) ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
