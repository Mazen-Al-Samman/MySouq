<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "option".
 *
 * @property int $id
 * @property int $field_id
 * @property string $title
 *
 * @property Field $field
 */
class Option extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'option';
    }

    public function rules()
    {
        return [
            [['field_id', 'title'], 'required'],
            [['field_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'field_id' => Yii::t('app', 'Field ID'),
            'title' => Yii::t('app', 'Title'),
        ];
    }

    public function getField()
    {
        return $this->hasOne(Field::className(), ['id' => 'field_id']);
    }

    public function get_all_options() {
        return $this->find()->all();
    }

    public function getTitleById($id) {
        return $this->find()->select(['title'])->where(['id' => $id])->all();
    }
}
