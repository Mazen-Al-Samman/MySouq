<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "value".
 *
 * @property int $id
 * @property int $post_id
 * @property int $field_id
 * @property int|null $option_id
 * @property string|null $varchar_val
 * @property int|null $int_val
 * @property float|null $float_val
 *
 * @property Field $field
 * @property Option $option
 * @property Post $post
 */
class Value extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['post_id', 'field_id'], 'required'],
            [['post_id', 'field_id', 'option_id', 'int_val'], 'integer'],
            [['float_val'], 'number'],
            [['varchar_val'], 'string', 'max' => 255],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
            [['option_id'], 'exist', 'skipOnError' => true, 'targetClass' => Option::className(), 'targetAttribute' => ['option_id' => 'id']],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'post_id' => Yii::t('app', 'Post ID'),
            'field_id' => Yii::t('app', 'Field ID'),
            'option_id' => Yii::t('app', 'Option ID'),
            'varchar_val' => Yii::t('app', 'Varchar Val'),
            'int_val' => Yii::t('app', 'Int Val'),
            'float_val' => Yii::t('app', 'Float Val'),
        ];
    }

    /**
     * Gets query for [[Field]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Field::className(), ['id' => 'field_id']);
    }

    /**
     * Gets query for [[Option]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(Option::className(), ['id' => 'option_id']);
    }

    /**
     * Gets query for [[Post]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    public function new_value($post_id, $field_id, $option_id) {
        $value = new Value();
        $value->post_id = $post_id;
        $value->field_id = $field_id;
        $value->option_id = $option_id;
        if ($value->save()) {
            return true;
        }
        return false;
    }
}
