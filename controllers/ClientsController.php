<?php

namespace app\controllers;

use Yii;
use app\behaviors\{
    AuthenticatorBehavior,
    DataSourceBehavior,
    ModelFinderBehavior
};
use app\dataproviders\{JsonDataProvider, XlsxDataProvider};
use app\models\{Client, ClientJson, ClientXlsx};
use yii\data\{ActiveDataProvider, BaseDataProvider};
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * @property string $dataSource
 */
class ClientsController extends ActiveController
{
    public $modelClass = \app\models\Client::class;

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
            'except' => ['index', 'view']
        ]);
        $behaviors['dataSource'] = [
            'class' => DataSourceBehavior::class,
        ];
        $behaviors['modelFinder'] = [
            'class' => ModelFinderBehavior::class,
            'modelClass' => $this->modelClass,
        ];

        return $behaviors;
    }

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        if ($this->dataSource !== DS_DATABASE) {
            unset($actions['create']);
            unset($actions['update']);
            unset($actions['view']);
        }

        return $actions;
    }

    public function actionCreate()
    {
        if ($this->dataSource === DS_JSON) {
            return ClientJson::create(Yii::$app->request->bodyParams);
        }

        if ($this->dataSource === DS_XLSX) {
            return ClientXlsx::create(Yii::$app->request->bodyParams);
        }

        return [];
    }

    public function actionUpdate($id)
    {
        if ($this->dataSource === DS_JSON) {
            return ClientJson::update($id, Yii::$app->request->bodyParams);
        }

        if ($this->dataSource === DS_XLSX) {
            return ClientXlsx::update($id, Yii::$app->request->bodyParams);
        }

        return [];
    }

    public function actionView($id)
    {
        return $this->findModel($id);
    }

    public function prepareDataProvider(): BaseDataProvider
    {
        if ($this->dataSource === DS_JSON) {
            return new JsonDataProvider;
        }

        if ($this->dataSource === DS_XLSX) {
            return new XlsxDataProvider;
        }

        /* @var Client $modelClass */
        $modelClass = $this->modelClass;

        return new ActiveDataProvider([
            'query' => $modelClass::find(),
        ]);
    }
}