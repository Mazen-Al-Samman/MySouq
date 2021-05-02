<?php 
namespace console\controllers;

use common\models\Post;
use yii\helpers\Json;
use yii2tech\crontab\CronTab;

class CheckController extends \yii\web\Controller
{
    public function beforeAction($action)
    {            
        if ($action->id == 'checkposts' || $action->id == 'print') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionCheckposts() {
        $post = new Post();
        $posts = $post->block_all_posts_that_contains(2, 'كلية');
        echo Json::encode($posts);
        return;
    }

    public function actionPrint() {
        echo 'Hello world';
    }
}
?>