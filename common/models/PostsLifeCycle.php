<?php

namespace common\models;

use Yii;

/**
 * This is the model class for collection "posts_life_cycle".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $role_id
 * @property mixed $post_id
 * @property mixed $transaction
 * @property mixed $pre_status
 * @property mixed $post_status
 * @property mixed $created_at
 */
class PostsLifeCycle extends \yii\mongodb\ActiveRecord
{

    public static function collectionName()
    {
        return ['opensooq_posts', 'posts_life_cycle'];
    }

    public function attributes()
    {
        return [
            '_id',
            'role_id',
            'post_id',
            'transaction',
            'pre_status',
            'post_status',
            'created_at',
        ];
    }

    public function rules()
    {
        return [
            [['role_id', 'post_id', 'transaction', 'pre_status', 'post_status', 'created_at'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
            'role_id' => Yii::t('app', 'Role ID'),
            'post_id' => Yii::t('app', 'Post ID'),
            'transaction' => Yii::t('app', 'Transaction'),
            'pre_status' => Yii::t('app', 'Pre Status'),
            'post_status' => Yii::t('app', 'Post Status'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    // Function will store a new transaction in the mongoDB.
    public function create_new_transaction($role_id, $post_id, $transaction, $pre_status, $post_status, $created_at) {
        $post_life = new PostsLifeCycle();
        $post_life->role_id = $role_id;
        $post_life->post_id = $post_id;
        $post_life->transaction = $transaction;
        $post_life->pre_status = $pre_status;
        $post_life->post_status = $post_status;
        $post_life->created_at = $created_at;
        $post_life->save();
        return true;
    }
}
