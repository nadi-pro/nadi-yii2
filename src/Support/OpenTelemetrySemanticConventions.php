<?php

namespace Nadi\Yii2\Support;

use Nadi\Support\OpenTelemetrySemanticConventions as CoreConventions;

class OpenTelemetrySemanticConventions extends CoreConventions
{
    public const YII_CONTROLLER = 'yii.controller';

    public const YII_ACTION = 'yii.action';

    public const YII_MODULE = 'yii.module';

    public const DB_CONNECTION_NAME = 'db.connection.name';

    public static function httpAttributesFromYiiRequest(\yii\web\Request $request, ?\yii\web\Response $response = null): array
    {
        $attributes = [
            self::HTTP_METHOD => $request->getMethod(),
            self::HTTP_URL => $request->getAbsoluteUrl(),
            self::HTTP_SCHEME => $request->getIsSecureConnection() ? 'https' : 'http',
            self::HTTP_HOST => $request->getHostName(),
            self::HTTP_TARGET => $request->getUrl(),
        ];

        $userAgent = $request->getUserAgent();
        if ($userAgent) {
            $attributes[self::HTTP_USER_AGENT] = $userAgent;
        }

        $clientIp = $request->getUserIP();
        if ($clientIp) {
            $attributes[self::HTTP_CLIENT_IP] = $clientIp;
        }

        if ($response) {
            $attributes[self::HTTP_STATUS_CODE] = $response->getStatusCode();
        }

        // Add Yii-specific attributes
        if (class_exists(\Yii::class) && \Yii::$app) {
            if (\Yii::$app->controller) {
                $attributes[self::YII_CONTROLLER] = get_class(\Yii::$app->controller);
                if (\Yii::$app->controller->action) {
                    $attributes[self::YII_ACTION] = \Yii::$app->controller->action->id;
                }
                if (\Yii::$app->controller->module) {
                    $attributes[self::YII_MODULE] = \Yii::$app->controller->module->id;
                }
            }
        }

        return $attributes;
    }

    public static function httpAttributesFromGlobals(): array
    {
        $attributes = [];

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $attributes[self::HTTP_METHOD] = $_SERVER['REQUEST_METHOD'];
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            $scheme = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
            $attributes[self::HTTP_URL] = $scheme.'://'.$host.$_SERVER['REQUEST_URI'];
            $attributes[self::HTTP_SCHEME] = $scheme;
            $attributes[self::HTTP_HOST] = $host;
            $attributes[self::HTTP_TARGET] = $_SERVER['REQUEST_URI'];
        }

        return $attributes;
    }

    public static function databaseAttributes(string $connectionName, string $query, float $duration): array
    {
        $attributes = [
            self::DB_SYSTEM => 'unknown',
            self::DB_STATEMENT => $query,
            self::DB_QUERY_DURATION => $duration,
            self::DB_CONNECTION_NAME => $connectionName,
        ];

        if (preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER|TRUNCATE)\s+/i', $query, $matches)) {
            $attributes[self::DB_OPERATION] = strtoupper($matches[1]);
        }

        if (preg_match('/(?:FROM|INTO|UPDATE|TABLE)\s+`?(\w+)`?/i', $query, $matches)) {
            $attributes[self::DB_SQL_TABLE] = $matches[1];
        }

        return $attributes;
    }

    public static function userAttributes(): array
    {
        if (class_exists(\Yii::class) && \Yii::$app && isset(\Yii::$app->user) && ! \Yii::$app->user->isGuest) {
            $identity = \Yii::$app->user->identity;
            if ($identity) {
                return [
                    self::USER_ID => $identity->getId(),
                ];
            }
        }

        return [];
    }

    public static function sessionAttributes(): array
    {
        if (class_exists(\Yii::class) && \Yii::$app && \Yii::$app->has('session')) {
            $session = \Yii::$app->session;
            if ($session->getIsActive()) {
                return [self::SESSION_ID => $session->getId()];
            }
        }

        return [];
    }

    public static function exceptionAttributes(\Throwable $exception): array
    {
        return parent::exceptionAttributes($exception);
    }

    public static function performanceAttributes(float $startTime, ?int $memoryPeak = null): array
    {
        return parent::performanceAttributes($startTime, $memoryPeak);
    }
}
