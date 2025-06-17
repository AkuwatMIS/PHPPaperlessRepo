<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "appraisals_emergency".
 *
 * @property int $id
 * @property int $application_id
 * @property string $house_ownership
 * @property int $total_family_members
 * @property int $no_of_earning_hands
 * @property int $ladies
 * @property int $gents
 * @property string $economic_dealings
 * @property string $social_behaviour
 * @property int $fatal_disease
 * @property int $income_before_corona
 * @property int $income_after_corona
 * @property int $expenses_in_corona
 * @property string $emergency_appraisal_address
 * @property string $description
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
 */
class AppraisalsEmergency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [TimestampBehavior::className()];

    }
    public static function tableName()
    {
        return 'appraisals_emergency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'house_ownership', 'total_family_members', 'no_of_earning_hands', 'ladies', 'gents', 'economic_dealings', 'social_behaviour', 'latitude', 'longitude', 'status'], 'required'],
            [['application_id', 'total_family_members', 'no_of_earning_hands', 'ladies', 'gents', 'fatal_disease', 'income_before_corona', 'income_after_corona', 'expenses_in_corona', 'is_lock', 'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted', 'platform'], 'integer'],
            [['latitude', 'longitude', 'bm_verify_latitude', 'bm_verify_longitude'], 'number'],
            [['house_ownership', 'economic_dealings', 'social_behaviour'], 'string', 'max' => 10],
            [['emergency_appraisal_address'], 'string', 'max' => 200],
            [['description'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 15],
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
            'house_ownership' => 'House Ownership',
            'total_family_members' => 'Total Family Members',
            'no_of_earning_hands' => 'No Of Earning Hands',
            'ladies' => 'Ladies',
            'gents' => 'Gents',
            'economic_dealings' => 'Economic Dealings',
            'social_behaviour' => 'Social Behaviour',
            'fatal_disease' => 'Fatal Disease',
            'income_before_corona' => 'Income Before Corona',
            'income_after_corona' => 'Income After Corona',
            'expenses_in_corona' => 'Expenses In Corona',
            'emergency_appraisal_address' => 'Emergency Appraisal Address',
            'description' => 'Description',
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
    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
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
    public function set_values($request)
    {
        $this->load($request);
        $this->application_id=$request['Applications']['id'];
        $this->longitude=0;
        $this->latitude=0;
        $this->status='pending';
        $this->income_before_corona = !empty($this->income_before_corona) ? $this->income_before_corona : 0;
        $this->income_after_corona = !empty($this->income_after_corona) ? $this->income_after_corona : 0;
        $this->expenses_in_corona = !empty($this->expenses_in_corona) ? $this->expenses_in_corona : 0;
    }
}
