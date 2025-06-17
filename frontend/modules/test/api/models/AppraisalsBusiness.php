<?php

namespace frontend\modules\test\api\models;

use common\models\Applications;
use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "business_appraisal".
 *
 * @property int $id
 * @property int $application_id
 * @property int $business_type
 * @property string $business
 * @property string $place_of_business
 * @property string $business_place_ownership
 * @property string $business_details
 * @property string $business_income
 * @property string $job_income
 * @property string $house_rent_income
 * @property string $other_income
 * @property string $total_income
 * @property string $estimated_business_capital
 * @property string $business_expenses
 * @property string $income_before_business
 * @property string $total_business_income
 * @property double $latitude
 * @property double $longitude
 * @property string $status
 * @property int $approved_by
 * @property int $approved_on
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 *
 * @property BaBusinessExpenses[] $baBusinessExpenses
 * @property BaFixedBusinessAssets[] $baFixedBusinessAssets
 * @property BaNewRequiredAssets[] $baNewRequiredAssets
 * @property BaRunningCapital[] $baRunningCapitals
 * @property Applications $application
 */
class AppraisalsBusiness extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $coordinates;

    public static function tableName()
    {
        return 'appraisals_business';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['is_lock','approved_by','approved_on','bm_verify_latitude','bm_verify_longitude'],
                'table' => "appraisals_business_logs",
                //'ignored' => ['updated_at'],
            ]
        ];

    }

    public $ba_fixed_buiness_assets;
    public $ba_running_capital;
    public $ba_business_expenses;
    public $ba_new_required_assets;
    public $ba_fixed_buiness_assets_total;
    public $ba_running_capital_total;
    public $ba_business_expenses_total;
    public $ba_new_required_assets_total;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id', 'place_of_business', 'latitude', 'longitude',], 'required'],
            [['platform','application_id', 'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted','period_of_business','emp_before_loan','emp_after_loan'], 'integer'],
            [[ 'status','fixed_business_assets','running_capital','business_expenses','new_required_assets'], 'safe'],
            [['business_appraisal_address','description'], 'safe'],
            [['latitude', 'longitude','fixed_business_assets_amount','new_required_assets_amount','running_capital_amount','business_expenses_amount','new_required_assets_amount'], 'number'],
            //[['business_type', 'deleted'], 'string', 'max' => 1],
            [['place_of_business','place_of_buying','who_are_customers'], 'string', 'max' => 50],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'place_of_business' => 'Place Of Business',
            'business_appraisal_address' => 'Business Appraisal Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'status' => 'Status',
            'approved_by' => 'Approved By',
            'approved_on' => 'Approved On',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
            'place_of_buying' => 'Place of Buying',
            'period_of_business' => 'Period of Business',
            'who_are_customers' => 'Who are Customers?',
            'emp_before_loan' => 'No. of Employees before loan',
            'emp_after_loan' => 'No. of Employees after loan',
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
        $this->load($request);

        $application=Applications::find()->select('bzns_cond')->where(['id'=>$request['Applications']['id']])->one();

        if($application->bzns_cond=='new'){
            if(empty($request['new_required_assets']) || (empty($this->new_required_assets_amount))){
                $this->addError('new_required_assets','If Business is new then New Required Assets can not be empty');
                $this->addError('new_required_assets','If Business is new then New Required Assets Total can not be empty');
            }
        }
        else if($application->bzns_cond=='old'){
            if(!isset($request['fixed_business_assets']) || !isset($this->fixed_business_assets_amount)){
                $this->addError('fixed_business_assets','If Business is old then Fixed Business Assets can not be empty');
                $this->addError('fixed_business_assets_amount','If Business is old then Fixed Business Assets Total can not be empty');
            }
            if (!isset($request['running_capital']) || (!isset($this->running_capital_amount))){
                $this->addError('running_capital','If Business is old then Running Capital can not be empty');
                $this->addError('running_capital_amount','If Business is old then Running Capital Total can not be empty');
            }
            if (!isset($request['business_expenses' ]) || (!isset($this->business_expenses_amount))){
                $this->addError('business_expenses','If Business is old then Business Expenses can not be empty');
                $this->addError('business_expenses_amount','If Business is old then Business Expenses Total can not be empty');
            }
        }





        $fixed_business_assets="";
        $business_expenses="";
        $new_required_assets="";
        $running_capital="";
        if(isset($request['fixed_business_assets'])) {
            foreach ($request['fixed_business_assets'] as $asset => $status) {
                if ($fixed_business_assets != "") {
                    $fixed_business_assets .= "," . $asset;
                } else {
                    $fixed_business_assets .= $asset;
                }
            }
        }
        if(isset($request['business_expenses'])) {
            foreach ($request['business_expenses'] as $asset => $status) {
                if ($business_expenses != "") {
                    $business_expenses .= "," . $asset;
                } else {
                    $business_expenses .= $asset;
                }
            }
        }
        if(isset($request['new_required_assets'])) {
            foreach ($request['new_required_assets'] as $asset => $status) {
                if ($new_required_assets != "") {
                    $new_required_assets .= "," . $asset;
                } else {
                    $new_required_assets .= $asset;
                }
            }
        }
        if(isset($request['running_capital'])) {
            foreach ($request['running_capital'] as $asset => $status) {
                if ($running_capital != "") {
                    $running_capital .= "," . $asset;
                } else {
                    $running_capital .=   $asset;
                }
            }
        }

        $this->application_id=$request['Applications']['id'];
        $this->longitude=0;
        $this->latitude=0;
        $this->fixed_business_assets=$fixed_business_assets;
        $this->running_capital=$running_capital;
        $this->business_expenses=$business_expenses;
        $this->new_required_assets=$new_required_assets;
        /*echo'<pre>';
        print_r($this->new_required_assets);
        die();*/
    }
}
