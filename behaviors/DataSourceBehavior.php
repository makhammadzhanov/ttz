<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;

class DataSourceBehavior extends Behavior
{

    public function getDataSource()
    {
        $source = 'database';
        $source_param = Yii::$app->request->queryParams['source'];

        if (!empty($source_param) && in_array($source_param, DATA_SOURCES)) {
            $source = DATA_SOURCES[$source_param];
        }

        return $source;
    }

}