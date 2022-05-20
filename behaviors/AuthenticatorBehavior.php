<?php

namespace app\behaviors;

use yii\base\Behavior;
use yii\filters\auth\HttpHeaderAuth;

class AuthenticatorBehavior extends Behavior
{
    public static function getAuthenticator($params = []): array
    {
        return array_merge([
            'class' => HttpHeaderAuth::class,
            'header' => 'x-access-token',
            'pattern' => '/^(.*?)$/',
        ], $params);
    }
}