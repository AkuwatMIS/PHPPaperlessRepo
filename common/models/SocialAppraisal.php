<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "social_appraisal".
 *
 * @property int $id
 * @property int $application_id
 * @property string $poverty_index
 * @property string $house_ownership
 * @property string $house_rent_amount
 * @property double $land_size
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
 * @property string $bank_name
 * @property string $other_expenses
 * @property string $total_expenses
 * @property string $loan_amount
 * @property string $economic_dealings
 * @property string $social_behaviour
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
 * @property Applications $application
 */
class SocialAppraisal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $coordinates;
    public static function tableName()
    {
        return 'appraisals_social';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['is_lock','approved_by','approved_on','house_ownership','bm_verify_latitude','bm_verify_longitude','fatal_disease'],
                'table' => "social_appraisal_logs",
                //'ignored' => ['updated_at'],
            ]
        ];

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id', 'poverty_index', 'house_ownership', 'land_size', 'total_family_members', 'no_of_earning_hands', 'ladies', 'gents', 'source_of_income', 'total_household_income', 'utility_bills', 'educational_expenses', 'medical_expenses', 'kitchen_expenses', 'monthly_savings', 'other_expenses', 'total_expenses', 'other_loan', 'economic_dealings', 'social_behaviour', 'latitude', 'longitude'], 'required'],
            [['application_id', 'total_family_members', 'no_of_earning_hands', 'ladies', 'gents','approved_by', 'approved_on', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at','other_loan','deleted','loan_amount'], 'integer'],
            [['business_income', 'job_income', 'house_rent_income', 'other_income', 'expected_increase_in_income','house_rent_amount', 'land_size', 'total_household_income', 'utility_bills', 'educational_expenses', 'medical_expenses', 'kitchen_expenses', 'amount', 'other_expenses', 'total_expenses', 'loan_amount', 'latitude', 'longitude'], 'number'],
            [['poverty_index'], 'string', 'max' => 20],
            [['house_ownership', 'economic_dealings', 'social_behaviour'], 'string', 'max' => 10],
            [['source_of_income', 'monthly_savings', 'status'], 'string', 'max' => 15],
            //[['other_loan'], 'string', 'max' => 4],
            //[['deleted'], 'string', 'max' => 1],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['bm_verify_latitude','social_appraisal_address'], 'safe'],
            [['bm_verify_longitude','description','date_of_maturity'], 'safe'],
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
            'poverty_index' => 'Poverty Index',
            'house_ownership' => 'House Ownership',
            'house_rent_amount' => 'House Rent Amount',
            'land_size' => 'Land Size',
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
            'loan_amount' => 'Loan Amount',
            'other_loan' => 'Other Loan',
            'economic_dealings' => 'Economic Dealings',
            'social_behaviour' => 'Social Behaviour',
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
}
