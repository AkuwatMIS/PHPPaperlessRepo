<?php

namespace frontend\modules\test\api\models;

use common\models\Applications;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "appraisal_details_social".
 *
 * @property int $id
 * @property int $application_id
 * @property string $poverty_index
 * @property string $house_ownership
 * @property string $house_rent_amount
 * @property double $land_size
 * @property string $land_area_type
 * @property int $total_family_members
 * @property int $no_of_earning_hands
 * @property int $ladies
 * @property int $gents
 * @property string $source_of_income
 * @property string $total_household_income
 * @property string $utility_bills
 * @property string $educational_expenses
 * @property string $medical_expenses
 * @property string $kitchen_expenses
 * @property string $monthly_savings
 * @property string $amount
 * @property int $date_of_maturity
 * @property string $other_expenses
 * @property string $total_expenses
 * @property int $other_loan
 * @property string $loan_amount
 * @property string $economic_dealings
 * @property string $social_behaviour
 * @property int $fatal_disease
 * @property string $description
 * @property string $description_image
 * @property double $latitude
 * @property double $longitude
 * @property string $social_appraisal_address
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
 *
 * @property Applications $application
 * @property SocialAppraisalDiseasesMapping[] $socialAppraisalDiseasesMappings
 */
class AppraisalsSocial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'appraisals_social';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['house_ownership','approved_by','approved_on','bm_verify_latitude','bm_verify_longitude'],
                'table' => "appraisals_social_logs",
                //'ignored' => ['updated_at'],
            ]
        ];

    }
    /**
     * {@inheritdoc}
     */
    public $coordinates;

    public function rules()
    {
        return [
            [['application_id', 'house_ownership', 'land_size', 'total_family_members', 'ladies', 'gents', 'source_of_income', 'total_household_income', 'utility_bills', 'educational_expenses', 'medical_expenses', 'kitchen_expenses', 'monthly_savings', 'other_expenses','other_loan', 'economic_dealings', 'social_behaviour', 'latitude', 'longitude', 'status', /*, 'created_at', 'updated_at'*/], 'required'],
            [['platform','application_id', 'total_family_members', 'no_of_earning_hands', 'ladies', 'gents', /*'date_of_maturity',*/ 'other_loan', 'fatal_disease', 'is_lock', 'approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted'], 'integer'],
            [['house_rent_amount','house_rent_income','job_income','business_income','other_income','expected_increase_in_income',
                'land_size', 'total_household_income', 'utility_bills', 'educational_expenses', 'medical_expenses', 'kitchen_expenses', 'amount', 'other_expenses', 'total_expenses', 'loan_amount', 'latitude', 'longitude', 'bm_verify_latitude', 'bm_verify_longitude'], 'number'],
            //[['poverty_index', 'land_area_type'], 'string', 'max' => 20],
            [['parent'], 'string', 'max' => 20],
            [['family_member_info','earning_hands_data'], 'string'],
            [['house_ownership', 'economic_dealings', 'social_behaviour'], 'string', 'max' => 10],
            [['source_of_income', 'monthly_savings', 'status'], 'string', 'max' => 15],
            [['description_image'], 'string', 'max' => 200],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            ['date_of_maturity', 'validateDateofMaturity'],
            ['loan_amount', 'validateLoanAmount'],
            ['house_rent_amount', 'validateHouseRentAmount'],
            ['amount', 'validateAmount'],
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
            //'poverty_index' => 'Poverty Index',
            'house_ownership' => 'House Ownership',
            'house_rent_amount' => 'House Rent Amount',
            'land_size' => 'Land Size',
            'land_area_type' => 'Land Area Type',
            'total_family_members' => 'Total Family Members',
            'no_of_earning_hands' => 'No Of Earning Hands',
            'ladies' => 'Ladies',
            'gents' => 'Gents',
            'source_of_income' => 'Source Of Income',
            'total_household_income' => 'Total Household Income',
            'utility_bills' => 'Utility Bills',
            'educational_expenses' => 'Educational Expenses',
            'medical_expenses' => 'Medical Expenses',
            'kitchen_expenses' => 'Kitchen Expenses',
            'monthly_savings' => 'Monthly Savings',
            'amount' => 'Amount',
            'date_of_maturity' => 'Date Of Maturity',
            'other_expenses' => 'Other Expenses',
            'total_expenses' => 'Total Expenses',
            'other_loan' => 'Other Loan',
            'loan_amount' => 'Loan Amount',
            'economic_dealings' => 'Economic Dealings',
            'social_behaviour' => 'Social Behaviour',
            'fatal_disease' => 'Fatal Disease',
            'job_income' => 'Job Income',
            'business_income' => 'Business Income',
            'other_income' => 'Other Income',
            'expected_increase_in_income' => 'Expected Increase in Income',
            'description' => 'Description',
            'description_image' => 'Description Image',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'social_appraisal_address' => 'Social Appraisal Address',
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
            'parent' => 'Parent',
            'family_member_info' => 'Family Member Info',
            'earning_hands_data' => 'Earning Hands Data',
        ];
    }

    public function validateDateofMaturity($attribute){

        if(!is_numeric($this->date_of_maturity)) {
            $this->date_of_maturity = strtotime($this->date_of_maturity);
        }
    }

    public function validateLoanAmount($attribute){

        $this->loan_amount = !empty($this->loan_amount) ? $this->loan_amount : null;
    }

    public function validateAmount($attribute){

        $this->amount = !empty($this->amount) ? $this->amount : null;
    }

    public function validateHouseRentAmount($attribute){

        $this->house_rent_amount = !empty($this->house_rent_amount) ? $this->house_rent_amount : null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSocialAppraisalDiseasesMappings()
    {
        return $this->hasMany(SocialAppraisalDiseasesMapping::className(), ['social_appraisal_id' => 'id']);
    }
    public function beforeSave($insert)
    {
       // die('a');
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
        $this->business_income=(!empty($this->business_income)?$this->business_income:'0');
        $this->job_income=(!empty($this->job_income)?$this->job_income:'0');
        $this->total_expenses=$this->educational_expenses+$this->medical_expenses+$this->kitchen_expenses+$this->utility_bills+$this->other_expenses;
        $this->total_household_income=$this->business_income+$this->job_income+$this->house_rent_income+$this->other_income;
        $this->total_family_members=$this->ladies+$this->gents;
        $this->date_of_maturity=strtotime($this->date_of_maturity);
        $this->loan_amount = !empty($this->loan_amount) ? $this->loan_amount : 0;
        $this->house_rent_amount = !empty($this->house_rent_amount) ? $this->house_rent_amount : 0;
    }
}
