<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use app\modules\admin\assets\AdminAsset;
use app\widgets\Noty;
use yii\helpers\Html;

AdminAsset::register($this);
$this->registerCsrfMetaTags();

$isGuest = Yii::$app->adminUser->isGuest;
$identity = Yii::$app->adminUser->identity;

// Страницы на всю ширину (например, логин) рендерятся без сайдбара и обёртки контента
$isFullWidth = !empty($this->params['fullWidth']);

$bodyClass = 'admin-layout'
    . (($isFullWidth || $isGuest) ? ' admin-layout--guest d-flex flex-column h-100' : '');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100" data-bs-theme="light">
<head>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title) ?> &middot; Админ-панель</title>
</head>
<body class="<?= $bodyClass ?>">
<?php $this->beginBody() ?>

<?php if (!$isGuest && !$isFullWidth): ?>
    <?= $this->render('_sidebar') ?>
<?php endif; ?>

<main class="main-content">
    <?php if (!$isGuest && !$isFullWidth): ?>
        <nav class="cc-topbar" id="navbarBlur" aria-label="Верхняя панель">

            <div class="cc-topbar-actions">
                <button type="button" class="cc-menu-toggle" id="iconNavbarSidenav" aria-label="Открыть меню">
                    <span class="sidenav-toggler-inner" aria-hidden="true">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </span>
                </button>

                <span class="cc-user">
                    <i class="material-symbols-rounded" aria-hidden="true">account_circle</i>
                    <span class="d-none d-sm-inline"><?= Html::encode($identity?->username ?? '') ?></span>
                </span>
            </div>
        </nav>
    <?php endif; ?>

    <?php if ($isFullWidth): ?>
        <?= $content ?>
    <?php else: ?>
        <div class="admin-content">
            <?= $content ?>
        </div>
    <?php endif; ?>
</main>

<?= Noty::widget() ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
