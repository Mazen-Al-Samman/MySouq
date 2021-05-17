<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sub_category".
 *
 * @property int $id
 * @property string $title
 * @property int $cat_id
 *
 * @property FieldAssign[] $fieldAssigns
 * @property Category $cat
 */
class SubCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sub_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'cat_id'], 'required'],
            [['cat_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'unique'],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['cat_id' => 'id']],
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
            'cat_id' => Yii::t('app', 'Cat ID'),
        ];
    }

    /**
     * Gets query for [[FieldAssigns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFieldAssigns()
    {
        return $this->hasMany(FieldAssign::className(), ['cat_id' => 'id']);
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

    public function getSubCat($cat_id) {
        return self::find()->select(['id', 'title'])->where(['cat_id' => $cat_id])->all();
    }
}
