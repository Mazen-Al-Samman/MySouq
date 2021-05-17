<?php

namespace common\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "field_assign".
 *
 * @property int $id
 * @property int $field_id
 * @property int $cat_id
 * @property int $country_id
 * @property string $label
 * @property string|null $type
 *
 * @property SubCategory $cat
 * @property Country $country
 * @property Field $field
 */
class FieldAssign extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field_assign';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['field_id', 'cat_id', 'country_id', 'label'], 'required'],
            [['field_id', 'cat_id', 'country_id'], 'integer'],
            [['type'], 'string'],
            [['label'], 'string', 'max' => 255],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubCategory::className(), 'targetAttribute' => ['cat_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['field_id'], 'exist', 'skipOnError' => true, 'targetClass' => Field::className(), 'targetAttribute' => ['field_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'field_id' => Yii::t('app', 'Field ID'),
            'cat_id' => Yii::t('app', 'Cat ID'),
            'country_id' => Yii::t('app', 'Country ID'),
            'label' => Yii::t('app', 'Label'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    /**
     * Gets query for [[Cat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(SubCategory::className(), ['id' => 'cat_id']);
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
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

    public function get_fields_for_country($country_id, $cat_id) {
        $fields = $this->find()->where(['country_id' => $country_id, 'cat_id' => $cat_id])->all();
        return $fields;
    }

    public function check_field_option_category($cat_id, $field_id, $option_id, $country_id) {
        if (empty($cat_id) || empty($field_id) || empty($option_id)) {
            return false;
        }
        $query = new Query();
        $data = $query
        ->select(['COUNT(field_assign.id) AS TOTAL_COUNT'])
        ->from('field_assign')
        ->join('INNER JOIN', 'field', 'field_assign.field_id = field.id')
        ->join('INNER JOIN', '`option`', 'field_assign.field_id = `option`.field_id')
        ->where(['field_assign.field_id' => $field_id, '`option`.id' => $option_id, 'field_assign.cat_id' => $cat_id])
        ->all();
        $check = (int)($data[0]['TOTAL_COUNT']);
        if ($check > 0) {
            return true;
        }
        return false;
    }
}
