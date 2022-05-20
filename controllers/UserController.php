<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Response;
use yii\rest\Controller;
use yii\filters\ContentNegotiator;
use app\behaviors\AuthenticatorBehavior;

class UserController extends Controller
{
    protected function verbs(): array
    {
        return [
            'signup' => ['POST'],
            'signin' => ['POST'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['authenticator'] = AuthenticatorBehavior::getAuthenticator([
            'only' => ['logout']
        ]);

        return $behaviors;
    }

    public function actionSignup()
    {
        $result = [
            'success' => false,
            'error' => [
                'message' => 'Не заполнены обьязательные поля!'
            ]
        ];

        $post = Yii::$app->request->post();

        if (!empty($post)) {
            $user = User::signup($post['email'], $post['password']);

            if ($user !== null) {
                $result = [
                    'success' => true,
                    'data' => [
                        'username' => $user->username,
                        'access_token' => $user->access_token
                    ]
                ];
            }
        }

        return $result;
    }

    public function actionSignin()
    {
        $result = [
            'success' => false,
            'error' => [
                'message' => 'Не заполнены обьязательные поля!'
            ]
        ];

        $post = Yii::$app->request->post();

        if (!empty($post)) {
            $user = User::findByUsername($post['username']);

            if ($user !== null && $user->validatePassword($post['password'])) {
                $user->updateAccessToken();

                $result = [
                    'success' => true,
                    'data' => [
                        'access_token' => $user->access_token
                    ]
                ];
            }
        }

        return $result;
    }

    public function actionLogout()
    {
        $user = Yii::$app->user;

        if (!$user->isGuest) {
            $user->identity->updateAccessToken();

            return [
                'success' => true,
                'data' => [
                    'message' => 'Вы успешно вышли из системы. Токены сброшены.'
                ]
            ];
        }

        return [
            'success' => false,
            'error' => [
                'message' => ''
            ]
        ];
    }
}