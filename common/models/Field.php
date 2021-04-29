<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "field".
 *
 * @property int $id
 * @property string $title
 *
 * @property FieldAssign[] $fieldAssigns
 * @property Option[] $options
 * @property Value[] $values
 */
class Field extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

    /**
     * Gets query for [[FieldAssigns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFieldAssigns()
    {
        return $this->hasMany(FieldAssign::className(), ['field_id' => 'id']);
    }

    /**
     * Gets query for [[Options]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOptions()
    {
        return $this->hasMany(Option::className(), ['field_id' => 'id']);
    }

    /**
     * Gets query for [[Values]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getValues()
    {
        return $this->hasMany(Value::className(), ['field_id' => 'id']);
    }

    public function getTitleById($id) {
        return $this->find()->select(['title'])->where(['id' => $id])->all();
    }

    public function get_fields($cat_id, $country_id) {
        return $this->find()->where(['=', 'cat_id', $cat_id])->andWhere(['=', 'country_id', $country_id])->all();
    }
}
