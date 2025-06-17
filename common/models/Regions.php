<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "regions".
 *
 * @property int $id
 * @property int $cr_division_id
 * @property string $name
 * @property string $code
 * @property string $tags
 * @property string $short_description
 * @property string $mobile
 * @property string $opening_date
 * @property string $full_address
 * @property double $latitude
 * @property double $longitude
 * @property int $status
 * @property int $assigned_to
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 */
class Regions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'regions';
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
            [['cr_division_id', 'name', 'code', 'assigned_to', 'created_by'], 'required'],
            [['cr_division_id', 'assigned_to', 'created_by','opening_date'], 'integer'],
            [['name', 'code', 'tags', 'short_description', 'mobile', 'full_address', 'status'], 'string'],
            [['latitude', 'longitude'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cr_division_id' => 'Cr Division ID',
            'name' => 'Name',
            'code' => 'Code',
            'tags' => 'Tags',
            'short_description' => 'Short Description',
            'mobile' => 'Mobile',
            'opening_date' => 'Opening Date',
            'full_address' => 'Full Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'status' => 'Status',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public function getCreditdivision()
    {
        return $this->hasOne(CreditDivisions::className(), ['id' => 'cr_division_id']);
    }

    /**
     *
     */
    public function getAreas()
    {
        return $this->hasMany(Areas::className(), ['region_id' => 'id'])->inverseOf('region');
    }

    /**
     *
     */
    public function getBranches()
    {
        return $this->hasMany(Branches::className(), ['region_id' => 'id'])->inverseOf('region');
    }

    /**
     *
     */
    public function getLoans()
    {
        return $this->hasMany(Loans::className(), ['area_id' => 'id']);
    }
}
