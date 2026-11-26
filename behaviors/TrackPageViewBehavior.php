<?php

declare(strict_types=1);

namespace app\behaviors;

use app\models\PageView;
use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\helpers\StringHelper;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Request;
use yii\web\Response;

/**
 * Логирует GET-просмотры публичных страниц (path + UTM + visitor cookie).
 *
 * @property-read Controller $owner
 */
class TrackPageViewBehavior extends Behavior
{
    public const COOKIE_NAME = '_vid';

    /** @var string[] actions, которые не пишем */
    public array $except = ['login', 'logout', 'captcha', 'error', 'contact'];

    /** Срок жизни visitor cookie, секунды (1 год) */
    public int $cookieDuration = 365 * 24 * 60 * 60;

    public function events(): array
    {
        return [
            Controller::EVENT_AFTER_ACTION => 'afterAction',
        ];
    }

    public function afterAction(ActionEvent $event): void
    {
        $request = Yii::$app->request;
        if (!$request instanceof Request || !$request->getIsGet()) {
            return;
        }

        $actionId = $event->action?->id;
        if ($actionId === null || in_array($actionId, $this->except, true)) {
            return;
        }

        $response = Yii::$app->response;
        if ($response->format !== Response::FORMAT_HTML) {
            return;
        }

        try {
            $this->saveView($request);
        } catch (\Throwable $e) {
            Yii::warning('PageView track failed: ' . $e->getMessage(), __METHOD__);
        }
    }

    private function saveView(Request $request): void
    {
        $pathInfo = trim($request->pathInfo, '/');
        $path = $pathInfo === '' ? '/' : '/' . $pathInfo;

        $queryString = $request->queryString;
        if ($queryString === '') {
            $queryString = null;
        } else {
            $queryString = StringHelper::truncate($queryString, 1024, '');
        }

        $utmSource = $this->normalizeUtm($request->get('utm_source'));
        $utmMedium = $this->normalizeUtm($request->get('utm_medium'));

        $userAgent = $request->userAgent;
        if ($userAgent !== null) {
            $userAgent = StringHelper::truncate($userAgent, 512, '');
        }

        $referrer = $request->referrer;
        if ($referrer !== null) {
            $referrer = StringHelper::truncate($referrer, 1024, '');
        }

        $model = new PageView([
            'created_at' => time(),
            'path' => StringHelper::truncate($path, 255, ''),
            'route' => Yii::$app->controller?->route,
            'query_string' => $queryString,
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'referrer' => $referrer,
            'visitor_id' => $this->resolveVisitorId(),
            'ip' => $request->userIP,
            'user_agent' => $userAgent,
            'is_bot' => $this->isBot($userAgent),
        ]);

        $model->save(false);
    }

    private function resolveVisitorId(): string
    {
        $cookies = Yii::$app->request->cookies;
        $existing = $cookies->getValue(self::COOKIE_NAME);
        if (is_string($existing) && preg_match('/^[0-9a-f-]{36}$/i', $existing)) {
            return $existing;
        }

        $visitorId = $this->generateUuid();

        Yii::$app->response->cookies->add(new Cookie([
            'name' => self::COOKIE_NAME,
            'value' => $visitorId,
            'expire' => time() + $this->cookieDuration,
            'httpOnly' => true,
            'sameSite' => Cookie::SAME_SITE_LAX,
            'secure' => YII_ENV_PROD,
        ]));

        return $visitorId;
    }

    private function normalizeUtm(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return StringHelper::truncate($value, 128, '');
    }

    private function isBot(?string $userAgent): bool
    {
        if ($userAgent === null || $userAgent === '') {
            return false;
        }

        return (bool) preg_match(
            '/bot|crawl|spider|slurp|facebookexternalhit|preview|wget|curl|python-requests/i',
            $userAgent,
        );
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
