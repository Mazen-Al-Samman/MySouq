<?php

namespace console\controllers;

use Yii;
use common\classes\RedisCache;

class QueueController extends \yii\web\Controller
{
    public function beforeAction($action)
    {            
        if ($action->id == 'start') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionStart() {
        $redis = new RedisCache();
        echo "Hello";
    }
}
