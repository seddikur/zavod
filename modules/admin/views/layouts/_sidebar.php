<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$controller = Yii::$app->controller;
$currentController = $controller?->id ?? '';
$currentAction = $controller?->action?->id ?? '';

/**
 * Формирует класс ссылки меню: активный пункт подсвечивается.
 */
$navLinkClass = static function (string $ctrl) use ($currentController): string {
    $isActive = $currentController === $ctrl;

    return 'nav-link' . ($isActive ? ' active' : '');
};

$appName = (string) (Yii::$app->name ?: 'Admin');
$brandLetter = mb_strtoupper(mb_substr($appName, 0, 1));

$items = [
    [
        'label' => 'Материалы',
        'icon' => 'category',
        'url' => ['/admin/material/index'],
        'controller' => 'material',
    ],
    [
        'label' => 'Категории',
        'icon' => 'folder',
        'url' => ['/admin/material-category/index'],
        'controller' => 'material-category',
    ],
    [
        'label' => 'Склады',
        'icon' => 'warehouse',
        'url' => ['/admin/warehouse/index'],
        'controller' => 'warehouse',
    ],
    [
        'label' => 'Единицы',
        'icon' => 'straighten',
        'url' => ['/admin/unit/index'],
        'controller' => 'unit',
    ],
    [
        'label' => 'Покупатели',
        'icon' => 'store',
        'url' => ['/admin/customer/index'],
        'controller' => 'customer',
    ],
    [
        'label' => 'Поставщики',
        'icon' => 'local_shipping',
        'url' => ['/admin/supplier/index'],
        'controller' => 'supplier',
    ],
    [
        'label' => 'Остатки',
        'icon' => 'inventory_2',
        'url' => ['/admin/material-stock/index'],
        'controller' => 'material-stock',
    ],
    [
        'label' => 'Рецепты',
        'icon' => 'science',
        'url' => ['/admin/recipe/index'],
        'controller' => 'recipe',
    ],
    [
        'label' => 'Приходы',
        'icon' => 'input',
        'url' => ['/admin/receipt/index'],
        'controller' => 'receipt',
    ],
    [
        'label' => 'Корректировки',
        'icon' => 'tune',
        'url' => ['/admin/adjustment/index'],
        'controller' => 'adjustment',
    ],
    [
        'label' => 'Отгрузки',
        'icon' => 'local_shipping',
        'url' => ['/admin/production-order/index'],
        'controller' => 'production-order',
    ],
    [
        'label' => 'Настройки',
        'icon' => 'settings',
        'url' => ['/admin/settings/index'],
        'controller' => 'settings',
    ],
    [
        'label' => 'Пользователи',
        'icon' => 'group',
        'url' => ['/admin/user/index'],
        'controller' => 'user',
    ],
    [
        'label' => 'Админы',
        'icon' => 'admin_panel_settings',
        'url' => ['/admin/admin-user/index'],
        'controller' => 'admin-user',
    ],
    [
        'label' => 'Журнал входов',
        'icon' => 'history',
        'url' => ['/admin/login-log/index'],
        'controller' => 'login-log',
    ],
    [
        'label' => 'Просмотры',
        'icon' => 'visibility',
        'url' => ['/admin/page-view/index'],
        'controller' => 'page-view',
    ],
];
?>
<aside class="sidenav" id="sidenav-main" aria-label="Боковое меню">
    <div class="sidenav-header">
        <button type="button" class="cc-close" id="iconSidenav" aria-label="Закрыть меню">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>

        <a class="cc-brand" href="<?= Yii::getAlias('@web/admin') ?>">
            <span class="cc-brand-mark"><?= Html::encode($brandLetter) ?></span>
            <span class="cc-brand-text">
                <span class="cc-brand-title"><?= Html::encode($appName) ?></span>
                <span class="cc-brand-sub">Admin Panel</span>
            </span>
        </a>
    </div>

    <div class="cc-nav-label">Меню</div>

    <div id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link <?= $currentController === 'default' ? 'active' : '' ?>"
                   href="<?= Yii::getAlias('@web/admin') ?>">
                    <span class="nav-icon">
                        <i class="material-symbols-rounded">dashboard</i>
                    </span>
                    <span class="nav-link-text">Дашборд</span>
                </a>
            </li>

            <?php foreach ($items as $item): ?>
                <li class="nav-item">
                    <a class="<?= $navLinkClass($item['controller']) ?>" href="<?= Url::to($item['url']) ?>">
                        <span class="nav-icon">
                            <i class="material-symbols-rounded"><?= Html::encode($item['icon']) ?></i>
                        </span>
                        <span class="nav-link-text"><?= Html::encode($item['label']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="sidenav-footer">
        <?= Html::beginForm(['/admin/logout'], 'post', ['class' => 'w-100']) ?>
        <?= Html::submitButton(
            '<i class="material-symbols-rounded" aria-hidden="true">logout</i><span>Выход</span>',
            ['class' => 'cc-logout-btn'],
        ) ?>
        <?= Html::endForm() ?>
    </div>
</aside>
