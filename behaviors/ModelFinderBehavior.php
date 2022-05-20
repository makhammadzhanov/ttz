<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;
use app\controllers\ClientsController;
use app\models\{ClientXlsx, ClientJson};

/**
 * @property-read ClientsController $owner
 */
class ModelFinderBehavior extends Behavior
{
    public $modelClass;

    public function findModel($id)
    {
        if ($this->owner->dataSource === DS_JSON) {
            return ClientJson::findOne($id);
        }

        if ($this->owner->dataSource === DS_XLSX) {
            return ClientXlsx::findOne($id);
        }

        return $this->modelClass::findOne($id);
    }
}