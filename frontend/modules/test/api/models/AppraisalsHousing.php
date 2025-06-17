<?php

namespace frontend\modules\test\api\models;

use common\models\Applications;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "appraisals_housing".
 *
 * @property int $id
 * @property int $application_id
 * @property string $property_type
 * @property string $ownership
 * @property double $land_area
 * @property double $residential_area
 * @property int $living_duration
 * @property string $duration_type
 * @property int $no_of_rooms
 * @property int $no_of_kitchens
 * @property int $no_of_toilets
 * @property string $purchase_price
 * @property string $current_price
 * @property string $address
 * @property string $estimated_figures
 * @property int $estimated_start_date
 * @property int $estimated_completion_time
 * @property string $housing_appraisal_address
 * @property string $description
 * @property string $description_image
 * @property double $latitude
 * @property double $longitude
 * @property string $status
 * @property double $bm_verify_latitude
 * @property double $bm_verify_longitude
 * @property int $is_lock
 * @property int $approved_by
 * @property int $approved_on
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 * @property int $platform
 *
 * @property Applications $application
 */
class AppraisalsHousing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'appraisals_housing';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['approved_by','approved_on','bm_verify_latitude','bm_verify_longitude'],
                'table' => "appraisals_housing_logs",
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
            [['application_id', 'property_type', 'ownership', 'land_area', 'residential_area', 'living_duration', 'duration_type', 'no_of_rooms', 'no_of_kitchens', 'no_of_toilets', 'purchase_price', 'current_price', 'address', /*'estimated_figures', 'estimated_start_date', 'estimated_completion_time'*/], 'required'],
            [['application_id', 'living_duration', 'no_of_rooms', 'no_of_kitchens', 'no_of_toilets', 'estimated_completion_time', 'is_lock', 'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted', 'platform'], 'integer'],
            [['land_area', 'residential_area', 'purchase_price', 'current_price', 'latitude', 'longitude', 'bm_verify_latitude', 'bm_verify_longitude'], 'number'],
            [['address', 'estimated_figures', 'housing_appraisal_address', 'description'], 'string'],
            [['property_type', 'ownership', 'duration_type'], 'string', 'max' => 10],
            [['description_image'], 'string', 'max' => 200],
            [['status'], 'string', 'max' => 15],
            ['estimated_start_date', 'validateDate'],

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
            'property_type' => 'Property Type',
            'ownership' => 'Ownership',
            'land_area' => 'Land Area',
            'residential_area' => 'Residential Area',
            'living_duration' => 'Living Duration',
            'duration_type' => 'Duration Type',
            'no_of_rooms' => 'No Of Rooms',
            'no_of_kitchens' => 'No Of Kitchens',
            'no_of_toilets' => 'No Of Toilets',
            'purchase_price' => 'Purchase Price',
            'current_price' => 'Current Price',
            'address' => 'Address',
            'estimated_figures' => 'Estimated Figures',
            'estimated_start_date' => 'Estimated Start Date',
            'estimated_completion_time' => 'Estimated Completion Time',
            'housing_appraisal_address' => 'Housing Appraisal Address',
            'description' => 'Description',
            'description_image' => 'Description Image',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'status' => 'Status',
            'bm_verify_latitude' => 'Bm Verify Latitude',
            'bm_verify_longitude' => 'Bm Verify Longitude',
            'is_lock' => 'Is Lock',
            'approved_by' => 'Approved By',
            'approved_on' => 'Approved On',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
            'platform' => 'Platform',
        ];
    }

    public function validateDate($attribute){
        if(!is_numeric($this->estimated_start_date)) {
            $this->estimated_start_date = strtotime($this->estimated_start_date);
        }
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
}
