<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "provinces".
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $app_tax_percent
 * @property int $cib_tax_percent
 * @property int $nadra_tax_percent
 * @property int $cib_fee
 * @property string $created_at
 * @property string $updated_at
 * @property BranchRequests[] $branchRequests
 * @property Branches[] $branches
 * @property Cities[] $cities
 * @property Divisions[] $divisions
 * @property ProgressReportDetails[] $progressReportDetails
 * @property Countries $country
 */
class Provinces extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provinces';
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
            [['country_id', 'assigned_to', 'created_by', 'updated_by', 'app_tax_percent', 'cib_tax_percent', 'nadra_tax_percent', 'cib_fee','gst'], 'integer'],
            [['name', 'assigned_to', 'created_by', 'created_at'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_id' => 'Country ID',
            'name' => 'Name',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranchRequests()
    {
        return $this->hasMany(BranchRequests::className(), ['province_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranches()
    {
        return $this->hasMany(Branches::className(), ['province_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(Cities::className(), ['province_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivisions()
    {
        return $this->hasMany(Divisions::className(), ['province_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgressReportDetails()
    {
        return $this->hasMany(ProgressReportDetails::className(), ['province_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }
}
