<?php

declare(strict_types=1);

/**
 * Сборка текстов публичного сайта из файлов по страницам.
 *
 * @see config/site-texts/
 */
$dir = __DIR__ . '/site-texts';

return array_merge(
    require $dir . '/common.php',
    require $dir . '/index.php',
    require $dir . '/cases.php',
    require $dir . '/process.php',
    require $dir . '/prices.php',
    require $dir . '/faq.php',
);
