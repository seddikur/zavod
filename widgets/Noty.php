<?php

namespace app\widgets;

use app\assets\NotyAsset;
use Yii;
use yii\base\Widget;
use yii\web\View;

/**
 * Виджет Noty для отображения уведомлений
 * 
 * Этот виджет интегрирует библиотеку Noty.js для отображения уведомлений в приложении.
 * 
 * Установка:
 * 1. Создайте директорию widgets в корне приложения (если её нет)
 * 2. Скопируйте этот файл в директорию widgets
 * 3. CSS и JS библиотеки Noty подключаются автоматически через NotyAsset
 *    (см. app\assets\NotyAsset). Убедитесь, что файлы лежат локально:
 *      - web/css/noty.min.css
 *      - web/css/animate.min.css
 *      - web/js/noty.min.js
 *    В layouts/main.php или другом шаблоне достаточно вызвать виджет
 *    после начала тела страницы:
 *      <?= \app\widgets\Noty::widget() ?>
 * 
 * 4. Для звуковых уведомлений создайте директорию web/sounds/noty и добавьте файл error.mp3
 * 
 * Использование:
 * В любом месте приложения можно вызывать уведомления через Flash-сообщения:
 * ```php
 * Yii::$app->session->setFlash('success', 'Операция выполнена успешно');
 * Yii::$app->session->setFlash('error', 'Произошла ошибка');
 * Yii::$app->session->setFlash('info', 'Информационное сообщение');
 * Yii::$app->session->setFlash('warning', 'Предупреждение');
 * ```
 * 
 * Настройка:
 * Можно изменить время отображения уведомлений через свойство $delay
 * ```php
 * <?= \app\widgets\Noty::widget(['delay' => 5000]) // 5 секунд ?>
 * ```
 * 
 * @property array $alertTypes Типы уведомлений и их соответствие типам Noty
 * @property int $delay Время в миллисекундах до автоматического закрытия уведомления
 */
final class Noty extends Widget
{
    /**
     * Конфигурация типов уведомлений
     * Ключ - тип Flash-сообщения в Yii
     * Значение - соответствующий тип уведомления в Noty
     * 
     * @var array
     */
    public $alertTypes = [
        'error'   => 'error',    // Критические ошибки
        'danger'  => 'error',    // Опасные ситуации
        'success' => 'success',  // Успешные операции
        'info'    => 'info',     // Информационные сообщения
        'warning' => 'warning',  // Предупреждения
        'default' => 'default',  // Обычные сообщения
        'simple'  => 'default'   // Простые сообщения
    ];

    /**
     * Время до автоматического закрытия уведомления
     * Для ошибок игнорируется (они закрываются только вручную)
     * 
     * @var int время в миллисекундах
     */
    public $delay = 7000;

    /**
     * Накопитель JavaScript кода для уведомлений
     * 
     * @var string
     */
    private $js;

    /**
     * Инициализация виджета
     * Регистрирует настройки по умолчанию и обрабатывает все Flash-сообщения
     */
    public function init(): void
    {
        parent::init();

        NotyAsset::register($this->getView());

        // Глобальные настройки Noty (без DOMContentLoaded: POS_READY уже после готовности DOM)
        $defaultOptions = <<<JS
if (typeof Noty !== 'undefined') {
    Noty.overrideDefaults({
        theme: 'relax',
        layout: 'topRight',
        timeout: {$this->delay},
        progressBar: true,
        closeWith: ['click', 'button'],
        animation: {
            open: 'animate__animated animate__fadeIn',
            close: 'animate__animated animate__fadeOut'
        }
    });
}
JS;
        $this->getView()->registerJs($defaultOptions, View::POS_READY);

        // Обработка Flash-сообщений
        $allFlashes = Yii::$app->getSession()->getAllFlashes();
        if (\count($allFlashes) > 0) {
            $this->js = '';
        }

        $count = 0;
        foreach ($allFlashes as $type => $data) {
            if (isset($this->alertTypes[$type])) {
                $data = (array) $data;

                foreach ($data as $message) {
                    // Обработка сообщения
                    $message = addslashes((string) $message);
                    $message = str_replace(["\r", "\n"], '<br />', $message);
                    if (strpos($message, '[') !== false && strpos($message, ']') !== false) {
                        $message = '<pre>' . $message . '</pre>';
                    }

                    $notyType = $this->alertTypes[$type];

                    // Добавление иконок к сообщениям
                    switch ($notyType) {
                        case 'error':
                            $message = '<i class="fas fa-exclamation-triangle"></i> ' . $message;
                            break;
                        case 'warning':
                        case 'info':
                            $message = '<i class="fas fa-exclamation-circle"></i> ' . $message;
                            break;
                        case 'success':
                            $message = '<i class="fas fa-check"></i> ' . $message;
                            break;
                    }

                    // Особая обработка для ошибок
                    if ($notyType === 'error') {
                        $this->registerError($message);
                        continue;
                    }

                    // Расчет времени отображения в зависимости от длины сообщения
                    $count++;
                    $delay = \strlen($message) * $this->delay / 100 * $count;
                    if ($delay <= $this->delay) {
                        $delay = $this->delay + $delay;
                    }

                    $this->registerOther($message, $notyType, (int) $delay);
                }
                
                Yii::$app->getSession()->removeFlash($type);
            }
        }

        // Регистрация накопленного JavaScript кода
        if (!empty($this->js)) {
            $this->getView()->registerJs($this->js, View::POS_READY);
        }
    }

    /**
     * Регистрирует уведомление об ошибке
     * Ошибки отображаются постоянно и имеют звуковое оповещение
     * 
     * @param string $message текст сообщения
     */
    private function registerError(string $message) : void
    {
        $this->js .= <<<JS
new Noty({
    type: 'error',
    text: '{$message}',
    timeout: false,              // Отключаем автозакрытие
    progressBar: false,          // Отключаем индикатор прогресса
    closeWith: ['click', 'button'], // Закрытие по клику или кнопкой
    sounds: {
        sources: ['/sounds/noty/error.mp3'], // Звук ошибки
        volume: 0.2,                         // Громкость
        conditions: ['docHidden', 'docVisible'] // Когда воспроизводить
    },
    docTitle: {
        conditions: ['docHidden', 'docVisible'] // Изменение заголовка вкладки
    }
}).show();
JS;
    }

    /**
     * Регистрирует обычное уведомление
     * 
     * @param string $message текст сообщения
     * @param string $type тип уведомления (success, info, warning)
     * @param int $delay время до автозакрытия
     */
    private function registerOther(string $message, string $type, int $delay) : void
    {
        $this->js .= <<<JS
new Noty({
    type: '{$type}',
    text: '{$message}',
    timeout: {$delay}
}).show();
JS;
    }
} 