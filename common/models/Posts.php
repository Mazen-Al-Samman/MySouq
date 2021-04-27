<?php

namespace common\models;

use Yii;

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

    public function create_new_post($post_id, $title, $description, $user_id, $status, $category, $price, $custom_params) {
        $post = new Posts();
        $post->post_id = $post_id;
        $post->title = $title;
        $post->description = $description;
        $post->user_id = $user_id;
        $post->status = $status;
        $post->category = $category;
        $post->price = $price;
        $post->custom_params = $custom_params;
        $post->created_at = date("Y/m/d");
        if ($post->save()) {
            return true;
        }
        return false;
    }

    public function get_all_posts_for_user($user_id, $first_row_id = -1, $posts_per_page = -1) {
        if ($first_row_id == -1) {
            $posts = $this->find()->where(['status' => ['Pending', 'Live'], 'user_id' => $user_id])->limit(($posts_per_page == -1)?3 : $posts_per_page)->orderBy(['post_id' => SORT_DESC])->all();
        } else {
            $posts = $this->find()->where(['status' => ['Pending', 'Live'], 'user_id' => $user_id])->limit(($posts_per_page == -1)?3 : $posts_per_page)->orderBy(['post_id' => SORT_DESC])->andWhere(['<', 'post_id', (int)($first_row_id)])->all();
        }
        return $posts;
    }

    public function setStatus($post_id, $new_status) {
        $post = $this->find()->where(['=', 'post_id', (int)($post_id)])->all();
        $post[0]->status = $new_status;
        $post[0]->save();
        return $post[0];
    }

    public function getPostById($post_id) {
        $post = $this->find()->where(['=', 'post_id', $post_id])->one();
        return $post;
    }
}
