<?php

namespace frontend\controllers;
use common\models\Post;
use common\models\FieldAssign;
use common\models\Option;
use common\models\Value;
use common\models\PostsLifeCycle;
use common\models\Field;
use common\models\Category;
use common\models\SubCategory;
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
        $sub_cat_model = new SubCategory();

        if ($model->load(Yii::$app->request->post())) {
            $post_data = Yii::$app->request->post();
            $cat_id = $post_data['cat_id'];
            $sub_cat_id = $post_data['sub_cat_id'];
            $params_array = [];
            $country_id = Yii::$app->user->identity->country_id;
            foreach ($post_data as $key => $value) {

                // This block of code will check if the option for the right filed, and both of them for the right category.
                if (strpos($key, 'field') !== false) {
                    $field_value = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
                    $first_underscore = strpos($key, '_');
                    $second_underscore = strpos($key, '_', $first_underscore + 1);
                    $type = substr($key, $first_underscore + 1, $second_underscore - ($first_underscore + 1));
                    $field_id = (int)(substr($key, 5));
                    $param = (object)['field_id' => $field_value, 'type' => $type, 'value' => $value];
                    $params_array[] = $param;
                    if ($type == 'option') {
                        $check = $field_assign_model->check_field_option_category($sub_cat_id, $field_value, $value, $country_id);
                        if (!$check) {
                            return $this->render('index', [
                                'model' => $model,
                                'url' => Url::base(),
                                'wrong' => true
                            ]);
                        }
                    } else {
                        if (empty($value)) {
                            return $this->render('index', [
                                'model' => $model,
                                'url' => Url::base(),
                                'wrong' => true
                            ]);
                        }
                    }
                }
            }
            // Save post in MySQL.
            $post_id = $model->create_new_post($cat_id, $sub_cat_id);

            if ((bool) $post_id) {
                for ($i = 0; $i < count($params_array); $i++) {
                    $field_id = $params_array[$i]->field_id;
                    $type = $params_array[$i]->type;
                    $value = $params_array[$i]->value;
                    $value_model->new_value($post_id, $field_id, $value, $type);
                }

                // To store the transaction details in mongoDB.
                $post_transaction = new PostsLifeCycle();
                $post_transaction->create_new_transaction(Yii::$app->user->identity->user_role, $post_id, 'New Post', null, 2, date("Y/m/d"));
                $redis = new RedisCache();
                $redis->LPUSH('queue', $post_id);
                return $this->redirect(['site/index']); 
            }
        }

        return $this->render('index', [
            'model' => $model,
            'url' => Url::base(),
            'wrong' => false
        ]);
    }

    public function actionDelete($id)
    {
        $post = new Post();
        $redis = new RedisCache();
        $role_id = Yii::$app->user->identity->user_role;
        $post->change_post_status($role_id, $id, 'Delete', 4, 'Deleted');
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
            $post_details = $posts_model->get_post_by_id($post_id);
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

    public function actionSubcat($cat_id) {
        $country_id = Yii::$app->user->identity->country_id;
        $sub_cat_model = new SubCategory();
        return Json::encode($sub_cat_model->getSubCat($cat_id));
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

    public function actionTest($post_id) {
        $post = new Value();
        $post_data = $post->postCustomParams($post_id);
        return Json::encode($post_data);
    }

    public function actionCheck($cat_id, $field_id, $option_id) {
        $fieldAddignModel = new FieldAssign();
        return $fieldAddignModel->check_field_option_category($cat_id, $field_id, $option_id);
    }
}
