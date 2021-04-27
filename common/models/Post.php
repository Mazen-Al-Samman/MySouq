<?php

namespace common\models;

use Yii;
use common\models\PostsLifeCycle;
use common\models\Value;
use common\models\Posts;
use common\classes\RedisCache;
use yii\helpers\Json;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $status_id
 * @property int $cat_id
 * @property float $price
 * @property int $user_id
 *
 * @property Category $cat
 * @property Status $status
 * @property User $user
 * @property Value[] $values
 */
class Post extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'post';
    }

    public function rules()
    {
        return [
            [['title', 'description', 'status_id', 'cat_id', 'price', 'user_id'], 'required'],
            [['status_id', 'cat_id', 'user_id'], 'integer'],
            [['price'], 'number'],
            [['title', 'description'], 'string', 'max' => 255],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['cat_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'status_id' => Yii::t('app', 'Status ID'),
            'cat_id' => Yii::t('app', 'Cat ID'),
            'price' => Yii::t('app', 'Price'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    public function getCat()
    {
        return $this->hasOne(Category::className(), ['id' => 'cat_id']);
    }

    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getValues()
    {
        return $this->hasMany(Value::className(), ['post_id' => 'id']);
    }

    // Function to create a new post.
    public function create_new_post($cat_id){
        $post = new Post();
        $post->title = $this->title;
        $post->description = $this->description;
        $post->cat_id = $cat_id;
        $post->price = $this->price;
        $post->status_id = 2;
        $post->user_id = Yii::$app->user->id;
        if($post->save()) {
            return $post->id;
        } else {
            return false;
        }
    }

    // Function to get all pending and live posts.
    public function get_all_posts() {
        $posts = $this->find()->where(['status_id' => [2]])->orderBy(['id' => SORT_DESC])->all();
        return $posts;
    }

    // Function to get all pending and live posts for a speicific user.
    public function get_all_posts_for_user($user_id, $first_row_id = -1, $posts_per_page = -1) {
        if ($first_row_id == -1) {
            $posts = $this->find()->where(['status_id' => [1,2], 'user_id' => $user_id])->limit(($posts_per_page == -1)?3 : $posts_per_page)->orderBy(['id' => SORT_DESC])->all();
        } else {
            $posts = $this->find()->where(['status_id' => [1,2], 'user_id' => $user_id])->limit(($posts_per_page == -1)?3 : $posts_per_page)->orderBy(['id' => SORT_DESC])->andWhere('id < ' . $first_row_id)->all();
        }
        return $posts;
    }

    // Function will block all posts that contains a specific word.
    public function block_all_posts_that_contains($word) {
        $posts = $this->find()->where(['like', 'LOWER(title)', "%$word%", false])->andWhere(['status_id' => [1,2]])->orWhere(['like', 'LOWER(description)', "%$word%", false])->andWhere(['status_id' => [1,2]])->all();
        foreach ($posts as $post) {
            $this->block_post(2, $post->id);
        }
        return $posts;
    }

    // Function will block the post [ change status to 3 ].
    public function block_post ($role_id, $post_id) {
        $post = $this->findOne($post_id);
        $pre_status = $post->status_id;
        $post->status_id = 3;
        if ($post->save()) {
            $post_transactions = new PostsLifeCycle();
            $posts = new Posts();
            $posts->setStatus($post_id, 'Blocked');
            $post_transactions->create_new_transaction($role_id, $post_id, 'Block', $pre_status, 3, date("Y/m/d"));
        }

        $redis = new RedisCache();
        $post_key = $post_id;
        $exist = $redis->exists($post_key);
        if ($exist) {
            $redis->RemovePost($post_key);
        }

        return true;
    }

    // Function will accept the post [ change status to 1 ].
    public function accept_post ($role_id, $post_id) {
        $post = $this->findOne($post_id);
        $pre_status = $post->status_id;
        $post->status_id = 1;
        $post_details = '';
        if ($post->save()) {
            $post_transactions = new PostsLifeCycle();
            $posts = new Posts();
            $post_details = $posts->setStatus($post_id, 'Live');
            $post_transactions->create_new_transaction($role_id, $post_id, 'Accept', $pre_status, 1, date("Y/m/d"));
        }

        $redis = new RedisCache();
        $post_key = $post_id;
        $exist = $redis->exists($post_key);
        if ($exist) {
            $redis->cachePost($post_details, $post_id, 'Live');
        }

        return true;
    }

    // Function will delte the post [ change status to 4 ].
    public function delete_post ($role_id, $post_id) {
        $post = $this->findOne($post_id);
        $pre_status = $post->status_id;
        $post->status_id = 4;
        if ($post->save()) {
            $post_transactions = new PostsLifeCycle();
            $posts = new Posts();
            $posts->setStatus($post_id, 'Deleted');
            $post_transactions->create_new_transaction($role_id, $post_id, 'Delete', $pre_status, 4, date("Y/m/d"));
        }

        $redis = new RedisCache();
        $post_key = $post_id;
        $exist = $redis->exists($post_key);
        if ($exist) {
            $redis->RemovePost($post_key);
        }

        return true;
    }
}
