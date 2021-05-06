<?php

namespace console\controllers;

use Yii;
use common\models\Post;
use common\models\Posts;
use common\models\Value;
use yii\helpers\Json;

class StoreController extends \yii\web\Controller
{
    public function beforeAction($action)
    {            
        if ($action->id == 'mongo') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionMongo()
    {
        $postModel = new Post();
        $postsModel = new Posts();
        $valueModel = new Value();

        if (!file_exists('last_updated_id.txt')) {
            touch('last_updated_id.txt');
            file_put_contents('last_updated_id.txt', '-1');
        }

        $last_updated = file_get_contents('last_updated_id.txt');
        $posts = $postModel->getPostsUpdatedAfter($last_updated);
        
        if (!empty($posts)) 
        {
            $length = count($posts);
            for ($i = 0; $i < $length; $i++) 
            {
                $params = $valueModel->postCustomParams($posts[$i]['id']);
                $postsModel->create_or_update_post($posts[$i], $params);
            }
            $last_id = $posts[$i - 1]['updated_at'];
            file_put_contents('last_updated_id.txt', $last_id);
            echo "Posts updated";
            return;
        }
    }

}
