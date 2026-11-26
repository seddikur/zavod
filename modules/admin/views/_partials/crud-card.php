<?php

declare(strict_types=1);

/**
 * Обёртка контента CRUD в карточку.
 *
 * Использование:
 *   <?php $this->beginContent('@app/modules/admin/views/_partials/crud-card.php'); ?>
 *   ...контент...
 *   <?php $this->endContent(); ?>
 *
 * @var yii\web\View $this
 * @var string $content
 * @var string|null $title
 * @var bool $padded
 * @var string|null $bodyClass
 */

$title = $title ?? null;
$padded = $padded ?? true;
$bodyClass = $bodyClass ?? ($padded ? 'card-body p-4' : 'card-body p-0');
?>
<div class="card cc-card cc-crud-card">
    <?php if ($title !== null && $title !== ''): ?>
        <div class="cc-card-head">
            <h2 class="cc-card-head__title"><?= \yii\helpers\Html::encode($title) ?></h2>
        </div>
    <?php endif; ?>
    <div class="<?= \yii\helpers\Html::encode($bodyClass) ?>">
        <?= $content ?>
    </div>
</div>
