<?php

namespace console\controllers;

use Yii;
use common\classes\RedisCache;
use yii\helpers\Json;

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
        // $data = $redis->BRPOP('queue',2);
        $redis->LPUSH('queue', 2);
        // echo Json::encode($data);
        // if ($data) {
            
        // }
    }
}
