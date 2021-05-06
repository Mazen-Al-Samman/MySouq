<?php

namespace console\controllers;

use Yii;
use common\classes\RedisCache;
use yii\helpers\Json;
use common\models\Value;
use common\models\Posts;
use common\models\Post;


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

        $postsModel = new Posts();
        $postModel = new Post();
        $valueModel = new Value();
        $redis = new RedisCache();

        while(true) {
            echo "Reading from the cache !\n";
            $data = $redis->BRPOP('queue', 50);
            if ($data) {
                $post_data = $postModel->get_post(($data));
                $params = $valueModel->postCustomParams(($data));
                $postsModel->create_or_update_post($post_data, $params);
                echo "Posts updated \n";
            }
        }
    }
}
