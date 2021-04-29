<?php

namespace frontend\controllers;
use common\models\Post;
use common\models\FieldAssign;
use common\models\Option;
use common\models\Value;
use common\models\PostsLifeCycle;
use common\models\Field;
use common\models\Category;
use common\models\Posts;
use common\classes\RedisCache;
use yii\helpers\Json;
use yii\helpers\Url;
use Yii;

class PostController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        // Models call
        $field_assign_model = new FieldAssign();
        $category_model = new Category();
        $field_model = new Field();
        $option_model = new Option();
        $model = new Post();
        $value_model = new Value();
        $posts_model = new Posts();

        if ($model->load(Yii::$app->request->post())) {

            // Data for mongoDB posts.
            $post_data = Yii::$app->request->post();
            $title = $post_data['Post']['title'];
            $description = $post_data['Post']['description'];
            $price = $post_data['Post']['price'];
            $cat_id = $post_data['cat_id'];
            $category = $category_model->getTitleById($cat_id)[0]['title'];

            // Save post in MySQL.
            $post_id = $model->create_new_post($_POST['cat_id']);

            if ((bool) $post_id) {
                $params = []; 
                foreach (Yii::$app->request->post() as $key => $value) {
                    if (strpos($key, 'field') !== false) {
                        $field_id = (int)(substr($key, 5));
                        $option_id = $value;

                        $field_title = $field_model->getTitleById($field_id)[0]['title'];
                        $option_title = $option_model->getTitleById($option_id)[0]['title'];
                        $params[$field_title] = $option_title;

                        $value_model->new_value($post_id, $field_id, $option_id);
                    }
                }

                // To store the transaction details in mongoDB.
                $post_transaction = new PostsLifeCycle();
                $post_transaction->create_new_transaction(Yii::$app->user->identity->user_role, $post_id, 'New Post', null, 2, date("Y/m/d"));

                // To store the post data in the mongoDB
                $status = 'Pending';
                $user_id = Yii::$app->user->id;
                $posts_model->create_new_post($post_id, $title, $description, $user_id, $status, $category, $price, $params);
                return $this->redirect(['site/index']); 
            }
        }

        return $this->render('index', [
            'model' => $model,
            'url' => Url::base()
        ]);
    }

    public function actionDelete($id)
    {
        $post = new Post();
        $redis = new RedisCache();
        $post->delete_post(Yii::$app->user->identity->user_role, $id);
        $redis->RemovePost($id);
        return $this->redirect(['site/index']);
    }

    public function actionMore($id) {
        $redis = new RedisCache();
        $post_key = $id;
        $exist = $redis->exists($post_key);

        if ($exist) {
            $post_details = $redis->GetPost($post_key);
        } else {
            $posts_model = new Posts();
            $post_id = (int)($id);
            $post_details = $posts_model->getPostById($post_id);
            $redis->cachePost($post_details, $post_id, $post_details['status']);
        }

        if ($post_details['user_id'] == Yii::$app->user->id){
            return $this->render('details', [
                'post' => $post_details
            ]);
        } else {
            return $this->redirect(['site/index']);
        }
    }

    public function actionParams($cat_id) {
        $country_id = Yii::$app->user->identity->country_id;
        $field_assign_model = new FieldAssign();
        $options_model = new Option();
        $fields = ($field_assign_model->get_fields_for_country($country_id, $cat_id));
        $fields_length = count($fields);
        $result_array = [];
        for ($i = 0; $i < $fields_length; $i++) { 
            $obj = [];
            $field = $fields[$i];
            foreach ($field as $key => $value) {
                $obj[$key] = $value;
            }
            $options = $options_model->get_options_for_field($obj['field_id']);
            $result_array[$i]['field'] = $obj;
            $result_array[$i]['options'] = $options;
        }
        return Json::encode($result_array);
    }
}