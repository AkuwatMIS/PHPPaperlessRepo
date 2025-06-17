<?php

namespace common\models;

use App\Models\Region;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "awp".
 *
 * @property int $id
 * @property string $month
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $project_id
 * @property int $awp_id
 * @property int $no_of_loans
 * @property int $avg_loan_size
 * @property int $disbursement_amount
 * @property int $monthly_olp
 * @property int $active_loans
 * @property int $monthly_closed_loans
 * @property int $monthly_recovery
 * @property int $avg_recovery
 * @property int $funds_required
 * @property int $actual_recovery
 * @property int $actual_disbursement
 * @property int $actual_no_of_loans
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted
 */
class Awp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'awp';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),
        ];
    }
    public $active_loans_last;
    public $olp_last;
    public $active_loans_current;
    public $monthly_closed_loans_last;
    public $no_of_loans_last;
    public $olp_current;
    public $monthly_recovery_last;
    public $amount_disbursed_last;
    public $month_from;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['month', 'region_id', 'area_id', 'branch_id', 'project_id',/* 'created_at', 'updated_at'*/], 'required'],
            [['region_id', 'area_id', 'branch_id', 'project_id', 'awp_id', 'no_of_loans', 'avg_loan_size', 'disbursement_amount', 'monthly_olp', 'active_loans', 'monthly_closed_loans', 'monthly_recovery', 'avg_recovery', 'funds_required', 'actual_recovery', 'actual_disbursement', 'actual_no_of_loans', 'is_lock','status','created_at', 'updated_at', 'deleted'], 'integer'],
            [['month'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'month' => 'Month',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'project_id' => 'Project ID',
            'awp_id' => 'Awp ID',
            'no_of_loans' => 'No Of Loans',
            'avg_loan_size' => 'Avg Loan Size',
            'disbursement_amount' => 'Disbursement Amount',
            'monthly_olp' => 'Monthly Olp',
            'active_loans' => 'Active Loans',
            'monthly_closed_loans' => 'Monthly Closed Loans',
            'monthly_recovery' => 'Monthly Recovery',
            'avg_recovery' => 'Avg Recovery',
            'funds_required' => 'Funds Required',
            'actual_recovery' => 'Actual Recovery',
            'actual_disbursement' => 'Actual Disbursement',
            'actual_no_of_loans' => 'Actual No Of Loans',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted' => 'Deleted',
            'status' => 'Status',
        ];
    }

    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }

    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }

    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }
}
