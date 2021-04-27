<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $title
 *
 * @property FieldAssign[] $fieldAssigns
 * @property Post[] $posts
 */
class Category extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'category';
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
        ];
    }

    public function getFieldAssigns()
    {
        return $this->hasMany(FieldAssign::className(), ['cat_id' => 'id']);
    }

    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['cat_id' => 'id']);
    }

    public function getTitleById($cat_id) {
        $result = $this->find()->select(['title'])->where(['id' => $cat_id])->all();
        return $result;
    }
}
