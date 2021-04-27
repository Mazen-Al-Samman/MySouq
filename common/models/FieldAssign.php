<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "field_assign".
 *
 * @property int $id
 * @property int $field_id
 * @property int $cat_id
 * @property int $country_id
 * @property string $label
 *
 * @property Category $cat
 * @property Country $country
 * @property Field $field
 */
class FieldAssign extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'field_assign';
    }

    public function rules()
    {
        return [
            [['field_id', 'cat_id', 'country_id', 'label'], 'required'],
            [['field_id', 'cat_id', 'country_id'], 'integer'],
            [['label'], 'string', 'max' => 255],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['cat_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'field_id' => Yii::t('app', 'Field ID'),
            'cat_id' => Yii::t('app', 'Cat ID'),
            'country_id' => Yii::t('app', 'Country ID'),
            'label' => Yii::t('app', 'Label'),
        ];
    }

    public function getCat()
    {
        return $this->hasOne(Category::className(), ['id' => 'cat_id']);
    }

    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    public function getField()
    {
        return $this->hasOne(Field::className(), ['id' => 'field_id']);
    }

    public function get_fields_for_country($country_id) {
        $fields = $this->find()->where(['country_id' => $country_id])->all();
        return $fields;
    }
}
