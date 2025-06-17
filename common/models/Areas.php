<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "areas".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $tags
 * @property string $short_description
 * @property string $mobile
 * @property string $opening_date
 * @property string $full_address
 * @property double $latitude
 * @property double $longitude
 * @property int $region_id
 * @property int $status
 * @property int $assigned_to
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Loans[] $loans
 */
class Areas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'areas';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),];

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code', 'assigned_to', 'opening_date','created_by'], 'required'],
            [['name', 'code', 'tags', 'short_description', 'mobile', 'full_address', 'status'], 'string'],
            [['latitude', 'longitude'], 'number'],
            [['region_id', 'assigned_to', 'created_by'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'tags' => 'Tags',
            'short_description' => 'Short Description',
            'mobile' => 'Mobile',
            'opening_date' => 'Opening Date',
            'full_address' => 'Full Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'region_id' => 'Region ID',
            'status' => 'Status',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoans()
    {
        return $this->hasMany(Loans::className(), ['area_id' => 'id']);
    }
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
}
