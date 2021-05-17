<?php

namespace common\models;
use kartik\depdrop\DepDrop;

use Yii;
use common\models\PostsLifeCycle;
use common\models\Value;
use common\models\Posts;
use common\classes\RedisCache;
use yii\helpers\Json;
use yii\db\Query;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $status_id
 * @property int $cat_id
 * @property int $sub_cat_id
 * @property float $price
 * @property int $user_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Category $cat
 * @property Status $status
 * @property User $user
 * @property SubCategory $subCat
 * @property Value[] $values
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'status_id', 'cat_id', 'sub_cat_id', 'price', 'user_id'], 'required'],
            [['status_id', 'cat_id', 'sub_cat_id', 'user_id'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'description'], 'string', 'max' => 255],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['cat_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['sub_cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubCategory::className(), 'targetAttribute' => ['sub_cat_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'status_id' => Yii::t('app', 'Status ID'),
            'cat_id' => Yii::t('app', 'Cat ID'),
            'sub_cat_id' => Yii::t('app', 'Sub Cat ID'),
            'price' => Yii::t('app', 'Price'),
            'user_id' => Yii::t('app', 'User ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Cat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(Category::className(), ['id' => 'cat_id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[SubCat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubCat()
    {
        return $this->hasOne(SubCategory::className(), ['id' => 'sub_cat_id']);
    }

    /**
     * Gets query for [[Values]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getValues()
    {
        return $this->hasMany(Value::className(), ['post_id' => 'id']);
    }
    // Function to create a new post.
    public function create_new_post($cat_id, $sub_cat_id){
        $post = new Post();
        $post->title = $this->title;
        $post->description = $this->description;
        $post->cat_id = $cat_id;
        $post->sub_cat_id = $sub_cat_id;
        $post->price = $this->price;
        $post->status_id = 2;
        $post->user_id = Yii::$app->user->id;
        if($post->save(false)) {
            $redis = new RedisCache();
            $redis->LPUSH('queue', $post->id);
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
    public function block_all_posts_that_contains($role_id, $word) {
        $posts = $this->find()->where(['status_id' => [1,2]])->andWhere([
        'OR',
        ['like', 'LOWER(title)', "%$word%", false],
        ['like', 'LOWER(description)', "%$word%", false]
        ])->all();
        
        foreach ($posts as $post) {
            self::change_post_status($role_id, $post->id, 'Block', 3, 'Blocked');
        }
        return $posts;
    }

    // This function for changing the post status after an action.
    public function change_post_status($role_id, $post_id, $action, $status_id, $status) {
        $post = $this->findOne($post_id);
        $pre_status = $post->status_id;
        $post->status_id = $status_id;
        if ($post->save()) {
            $redis = new RedisCache();
            $redis->LPUSH('queue', $post->id);
            $post_transactions = new PostsLifeCycle();
            $post_transactions->create_new_transaction($role_id, $post_id, $action, $pre_status, $status_id, date("Y/m/d"));
        }
        return true;
    }

    // This function will recache the post after changing the status.
    public function cache_post($post_id, $status_id, $post_details) {
        $redis = new RedisCache();
        if ($status_id == 1) {
            $exist = $redis->exists($post_id);
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

    public function getPostsUpdatedAfter($updated_at) {
        $query = new Query();
        $data = $query
        ->select(['post.*', 'status.title as status', 'category.title as category'])
        ->from('post')
        ->join('INNER JOIN', 'status', 'post.status_id = status.id')
        ->join('INNER JOIN', 'category', 'post.cat_id = category.id')
        ->orderBy(['post.updated_at' => SORT_ASC])
        ->limit(5);

        if ($updated_at != '-1') {
            $data->where(['>', 'post.updated_at', $updated_at]);
        }

        $data = $data->all();
        return $data;
    }

    public function get_post($id) {
        $query = new Query();
        $data = $query
        ->select(['post.*', 'status.title as status', 'category.title as category'])
        ->from('post')
        ->join('INNER JOIN', 'status', 'post.status_id = status.id')
        ->join('INNER JOIN', 'category', 'post.cat_id = category.id')
        ->where(['post.id' => $id])
        ->one();
        return $data;
    }
}
