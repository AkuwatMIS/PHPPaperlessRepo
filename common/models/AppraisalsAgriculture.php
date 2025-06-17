<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "appraisals_agriculture".
 *
 * @property int $id
 * @property int $application_id
 * @property int $water_analysis
 * @property int $soil_analysis
 * @property int $laser_level
 * @property string $irrigation_source
 * @property string $other_source
 * @property string $crop_year
 * @property string $crop_production
 * @property string $resources
 * @property string $expenses
 * @property string $available_resources
 * @property string $required_resources
 * @property double $latitude
 * @property double $longitude
 * @property string $status
 * @property string $agriculture_appraisal_address
 * @property double $bm_verify_latitude
 * @property double $bm_verify_longitude
 * @property int $approved_by
 * @property int $approved_on
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 * @property int $platform
 * @property string $required_machinery_detail
 * @property int $expected_sale_price
 * @property int $expected_expenses
 * @property int $expected_savings
 * @property string $crop_type
 * @property string $crops
 * @property string $owner
 * @property string $land_area_type
 * @property int $land_area_size
 *
 * @property Applications $application
 */
class AppraisalsAgriculture extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'appraisals_agriculture';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['approved_by','approved_on','bm_verify_latitude','bm_verify_longitude'],
                'table' => "appraisals_agriculture_logs",
                //'ignored' => ['updated_at'],
            ]
        ];

    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'water_analysis', 'soil_analysis', 'laser_level', 'irrigation_source', 'crop_year', 'crop_production', 'resources', 'expenses', 'available_resources', 'required_resources', 'latitude', 'longitude', 'status'], 'required'],
            [['expected_sale_price','expected_expenses','expected_savings','application_id', 'water_analysis', 'soil_analysis', 'laser_level', 'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted', 'platform'], 'integer'],
            [['resources', 'agriculture_appraisal_address','required_machinery_detail','crop_type','crops','owner','land_area_type'], 'string'],
            [['crop_production','expenses', 'available_resources', 'required_resources', 'latitude', 'longitude', 'bm_verify_latitude', 'bm_verify_longitude'], 'number'],
            [['irrigation_source', 'other_source', 'crop_year'], 'string', 'max' => 30],
            [['status'], 'string', 'max' => 15],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'water_analysis' => 'Water Analysis',
            'soil_analysis' => 'Soil Analysis',
            'laser_level' => 'Laser Level',
            'irrigation_source' => 'Irrigation Source',
            'other_source' => 'Other Source',
            'crop_year' => 'Crop Year',
            'crop_production' => 'Crop Production',
            'resources' => 'Resources',
            'expenses' => 'Expenses',
            'available_resources' => 'Available Resources',
            'required_resources' => 'Required Resources',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'status' => 'Status',
            'agriculture_appraisal_address' => 'Agriculture Appraisal Address',
            'bm_verify_latitude' => 'Bm Verify Latitude',
            'bm_verify_longitude' => 'Bm Verify Longitude',
            'required_machinery_detail' => 'Required Machinery',
            'expected_sale_price' => 'Expected Sale Price',
            'expected_expenses' => 'Expected Expenses',
            'expected_savings' => 'Expected Savings',
            'crop_type' => 'Crop Type',
            'crops' => 'Crops',
            'approved_by' => 'Approved By',
            'approved_on' => 'Approved On',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
            'platform' => 'Platform',
            'owner' => 'owner',
            'land_area_size' => 'Land Area Size',
            'land_area_type' => 'Land Area Type',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->status = isset($this->status) ? $this->status : "incomplete";
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }
    public function set_values($request)
    {


        $resources='';
        $this->load($request);
        if(isset($request['resources'])) {
            foreach ($request['resources'] as $asset => $status) {
                if ($resources != "") {
                    $resources .= "," . $asset;
                } else {
                    $resources .= $asset;
                }
            }
        }
        $this->resources=$resources;
        $this->application_id=$request['Applications']['id'];
        $this->longitude=0;
        $this->latitude=0;
        $this->status='pending';
    }
}
