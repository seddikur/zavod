<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\AdminUser|null $admin */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Дашборд';
$admin = $admin ?? Yii::$app->adminUser->identity;

$cards = [
    [
        'title' => 'Настройки',
        'text' => 'Ключ-значение параметров сайта.',
        'icon' => 'fa fa-sliders',
        'url' => ['/admin/settings/index'],
    ],
    [
        'title' => 'Пользователи',
        'text' => 'Управление фронтовыми пользователями.',
        'icon' => 'fa fa-users',
        'url' => ['/admin/user/index'],
    ],
    [
        'title' => 'Администраторы',
        'text' => 'Учётные записи админ-панели.',
        'icon' => 'fa fa-user-shield',
        'url' => ['/admin/admin-user/index'],
    ],
    [
        'title' => 'Журнал входов',
        'text' => 'История авторизаций администраторов.',
        'icon' => 'fa fa-clock-rotate-left',
        'url' => ['/admin/login-log/index'],
    ],
    [
        'title' => 'Просмотры',
        'text' => 'Журнал посещений публичных страниц.',
        'icon' => 'fa fa-eye',
        'url' => ['/admin/page-view/index'],
    ],
];
?>
<div class="admin-dashboard">
    <div class="card cc-card mb-3">
        <div class="card-body p-4">
            <h1 class="h4 fw-bold mb-2">Добро пожаловать, <?= Html::encode($admin?->username ?? '') ?></h1>
            <p class="text-body-secondary mb-0">
                Роль:
                <span class="cc-badge">
                    <?= Html::encode($admin instanceof \app\models\AdminUser ? $admin->getRoleLabel() : '—') ?>
                </span>
            </p>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($cards as $card): ?>
            <div class="col-md-6 col-xl-3">
                <a href="<?= Url::to($card['url']) ?>" class="text-decoration-none text-reset d-block h-100">
                    <div class="card cc-card h-100">
                        <div class="card-body p-4">
                            <div class="cc-stat-icon"><i class="<?= Html::encode($card['icon']) ?>"></i></div>
                            <h5 class="fw-bold mb-1"><?= Html::encode($card['title']) ?></h5>
                            <p class="text-body-secondary small mb-0"><?= Html::encode($card['text']) ?></p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
