<?php

namespace common\models;

use Yii;
use common\classes\RedisCache;
use yii\helpers\Json;

/**
 * This is the model class for collection "posts".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $post_id
 * @property mixed $title
 * @property mixed $description
 * @property mixed $user_id
 * @property mixed $status
 * @property mixed $category
 * @property mixed $price
 * @property mixed $custom_params
 * @property mixed $created_at
 */
class Posts extends \yii\mongodb\ActiveRecord
{

    public static function collectionName()
    {
        return ['opensooq_posts', 'posts'];
    }

    public function attributes()
    {
        return [
            '_id',
            'post_id',
            'title',
            'description',
            'user_id',
            'status',
            'category',
            'price',
            'custom_params',
            'created_at',
        ];
    }

    public function rules()
    {
        return [
            [['post_id', 'title', 'description', 'user_id', 'status', 'category', 'price', 'custom_params', 'created_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
            'post_id' => Yii::t('app', 'Post ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'user_id' => Yii::t('app', 'User ID'),
            'status' => Yii::t('app', 'Status'),
            'category' => Yii::t('app', 'Category'),
            'price' => Yii::t('app', 'Price'),
            'custom_params' => Yii::t('app', 'Custom Params'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function create_new_post($post, $params) {
        $postObj = new Posts();
        $postObj->post_id = (int)($post['id']);
        $postObj->title = $post['title'];
        $postObj->description = $post['description'];
        $postObj->user_id = (int)($post['user_id']);
        $postObj->status = $post['status'];
        $postObj->category = $post['category'];
        $postObj->price = $post['price'];
        $postObj->custom_params = $params;
        $postObj->created_at = date("Y/m/d");
        if ($postObj->save()) {
            return true;
        }
        return false;
    }

    public function get_all_posts() {
        $posts = $this->find()->where(['status_id' => [2]])->orderBy(['id' => SORT_DESC])->all();
        return $posts;
    }

    public function get_all_posts_for_user($user_id, $first_row_id = -1, $posts_per_page = -1) {
        if ($first_row_id == -1) {
            $posts = self::find()->where(['status' => ['Pending', 'Live'], 'user_id' => $user_id])->limit(($posts_per_page == -1)?3 : $posts_per_page)->orderBy(['post_id' => SORT_DESC])->all();
        } else {
            $posts = self::find()->where(['status' => ['Pending', 'Live'], 'user_id' => $user_id])->limit(($posts_per_page == -1)?3 : $posts_per_page)->orderBy(['post_id' => SORT_DESC])->andWhere(['<', 'post_id', (int)($first_row_id)])->all();
        }
        return $posts;
    }

    public function block_all_posts_that_contains($word) {
        $posts = $this->find()->where(['status_id' => [1,2]])->andWhere([
        'OR',
        ['like', 'LOWER(title)', "%$word%", false],
        ['like', 'LOWER(description)', "%$word%", false]
        ])->all();
        
        foreach ($posts as $post) {
            $this->block_post(2, $post->id);
        }
        return $posts;
    }

    public function create_or_update_post($post, $params) {  
        $post_id = (int)($post['id']);
        $postObj = self::find()->where(['=', 'post_id', $post_id])->one();
        if (!empty($postObj)) {
            $postObj->status = $post['status'];
            if ($postObj->save()) {
                $status_id = $post['status_id'];
                self::cache_post($post_id, $status_id, $postObj);
            }
        } else {
            self::create_new_post($post, $params);
        }
        return;
    }

    public function change_post_status($post_id, $new_status) {
        $post = self::find()->where(['=', 'post_id', (int)($post_id)])->one();
        $post->status = $new_status;
        $post->save();
        return $post;
    }

    public function get_post_by_id($post_id) {
        $post = self::find()->where(['=', 'post_id', $post_id])->one();
        return $post;
    }

    public function cache_post($post_id, $status_id, $post_details) {
        $redis = new RedisCache();
        if ($status_id == 1) {
            $exist = $redis->exists(strval($post_id));
            if ($exist) {
                $redis->cachePost($post_details, $post_id, 'Live');
            }
        } else {
            $exist = $redis->exists($post_id);
            if ($exist) {
                $redis->RemovePost($post_id);
            }
        }
    }
}
