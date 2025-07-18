<?php

namespace common\models;

use common\components\Helpers\LoanHelper;
use common\components\Helpers\StructureHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "loans".
 *
 * @property int $id
 * @property int $application_id
 * @property int $project_id
 * @property string $project_table
 * @property string $date_approved
 * @property string $loan_amount
 * @property string $cheque_no
 * @property string $inst_amnt
 * @property string $inst_months
 * @property string $inst_type
 * @property string $date_disbursed
 * @property string $cheque_dt
 * @property int $disbursement_id
 * @property int $activity_id
 * @property int $product_id
 * @property int $group_id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property string $loan_expiry
 * @property string $loan_completed_date
 * @property string $old_sanc_no
 * @property string $remarks
 * @property int $br_serial
 * @property string $sanction_no
 * @property string $due
 * @property string $overdue
 * @property string $balance
 * @property int $status
 * @property string $reject_reason
 * @property int $is_lock
 * @property int $deleted
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Donations[] $donations
 * @property Activities $activity
 * @property Applications $application
 * @property Areas $area
 * @property Branches $branch
 * @property Groups $group
 * @property Products $product
 * @property Projects $project
 * @property Regions $region
 * @property LoansLogs[] $loansLogs
 * @property Operations[] $operations
 * @property ProjectsDisabled[] $ProjectsDisabled
 * @property ProjectsTevta[] $ProjectsTevta
 * @property Recoveries[] $recoveries
 * @property Schedules[] $schedules
 * @property LoanActions[] $accountVerification
 */
class Loans extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loans';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['application_id', 'fund_request_id', 'date_disbursed', 'project_id', 'loan_amount', 'disbursed_amount', 'inst_amnt', 'sanction_no', 'date_approved', 'status', 'group_id'],
                'table' => "loans_logs",
                //'ignored' => ['updated_at'],
            ]/*,
            'ConfigsBehavior' => [
                'class' => 'common\behavior\ConfigsBehavior',
            ]*/
        ];

    }

    public $start_date;
    public $total_expenses;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id', 'project_id', 'inst_amnt', 'inst_months', 'inst_type', 'product_id', 'group_id', 'region_id', 'area_id', 'branch_id'], 'required'],
            [['platform', 'application_id', 'project_id', 'disbursement_id', 'activity_id', 'product_id', 'group_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'br_serial', 'assigned_to', 'created_by', 'updated_by', 'cheque_dt', 'loan_expiry', 'loan_completed_date', 'is_lock', 'deleted', 'fund_request_id'], 'integer'],
            [['activity_id'], 'default', 'value' => '0'],
            [['loan_amount', 'disbursed_amount', 'service_charges', 'inst_amnt', 'inst_months', 'due', 'overdue', 'balance'], 'number'],
            [['loan_amount'], 'number', 'min' => 5000],
            [['remarks', 'reject_reason', 'status'], 'string'],
            [['sanction_no'], 'unique', 'filter' => ['deleted' => 0]],
            [['project_table', 'old_sanc_no', 'sanction_no'], 'string', 'max' => 50],
            [['cheque_no'], 'string', 'max' => 15],
            [['inst_type'], 'string', 'max' => 30],
            [['attendance_status'], 'string', 'max' => 20],
            //[['status', 'is_lock'], 'string', 'max' => 3],
            //[['deleted'], 'string', 'max' => 1],
            //[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Activities::className(), 'targetAttribute' => ['activity_id' => 'id']],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Groups::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            ['date_approved', 'validateDateApprovedConvertToIneger'],
            [['date_disbursed'], 'safe']
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
            'project_id' => 'Project ID',
            'project_table' => 'Project Table',
            'date_approved' => 'Date Approved',
            'loan_amount' => 'Loan Amount',
            'cheque_no' => 'Cheque No',
            'inst_amnt' => 'Inst Amnt',
            'inst_months' => 'Inst Months',
            'inst_type' => 'Inst Type',
            'date_disbursed' => 'Date Disbursed',
            'cheque_dt' => 'Cheque Dt',
            'disbursement_id' => 'Disbursement ID',
            'activity_id' => 'Activity ID',
            'product_id' => 'Product ID',
            'group_id' => 'Group ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'loan_expiry' => 'Loan Expiry',
            'loan_completed_date' => 'Loan Completed Date',
            'old_sanc_no' => 'Old Sanc No',
            'remarks' => 'Remarks',
            'br_serial' => 'Br Serial',
            'sanction_no' => 'Sanction No',
            'due' => 'Due',
            'overdue' => 'Overdue',
            'balance' => 'Balance',
            'status' => 'Status',
            'reject_reason' => 'Reject Reason',
            'attendance_status' => 'Attendance Status',
            'is_lock' => 'Is Lock',
            'deleted' => 'Deleted',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                if (in_array($this->project_id, StructureHelper::trancheProjects())) {
                    $visit = Visits::find()->where(['parent_id' => $this->application_id])->one();
                    if (!isset($visit) || empty($visit)) {
                        $this->addError('application_id', 'Please add visit before lac');
                    }
                }
                $this->status = isset($this->status) ? $this->status : "pending";
                $this->is_lock = isset($this->is_lock) ? $this->is_lock : 0;
                $this->assigned_to = isset($this->assigned_to) ? $this->assigned_to : Yii::$app->user->getId();
                $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            } else {
                $this->updated_by = isset($this->updated_by) ? $this->updated_by : Yii::$app->user->getId();
            }
            return true;
        } else {
            return false;
        }
    }

    public function setLoaninfo()
    {
        if (!in_array($this->project_id, StructureHelper::trancheProjects()) && !in_array($this->project_id, [132,75,35,6, 10, 59, 78, 128, 87, 74, 94, 83, 100, 106, 112, 118, 119, 121, 123, 124, 125, 126, 111, 122, 96, 113, 24, 129, 130, 109, 131,134,135,136,137,138,139,140,141,142,143,145])) {
            $this->inst_months = 0;
        }

        $this->inst_amnt = 0;
        $this->inst_type = 'Monthly';
        if ($this->project->code == 'gb' && (isset($this->activity->name) && $this->activity->name == 'Agriculture inputs')) {
            if ($this->loan_amount > 0 && $this->loan_amount <= 20000) {
                $this->inst_months = 12;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 20000 && $this->loan_amount <= 30000) {
                $this->inst_amnt = 2000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 30000 && $this->loan_amount <= 50000) {
                $this->inst_months = 15;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 50000 && $this->loan_amount <= 100000) {
                $this->inst_months = 20;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 100000) {
                $this->inst_months = 24;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            }
            // } else if ($this->project->code == 'PSIC' && (isset($this->activity->name) && $this->activity->name == 'Agriculture inputs')) {
            //     if ($this->loan_amount > 0 && $this->loan_amount <= 20000) {
            //         $this->inst_months = 12;
            //         $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            //     } else if ($this->loan_amount > 20000 && $this->loan_amount <= 30000) {
            //         $this->inst_amnt = 2000;
            //         $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            //     } else if ($this->loan_amount > 30000 && $this->loan_amount <= 50000) {
            //         $this->inst_months = 15;
            //         $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            //     } else if ($this->loan_amount > 50000) {
            //         $this->inst_months = 20;
            //         $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            //     }
        } else if (in_array($this->project_id, StructureHelper::trancheProjectsLac())) {
            $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
            $years = ($this->inst_months / 12);
            $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
            $total_amount = $this->loan_amount + $charges;
            $this->inst_amnt = round($total_amount / $this->inst_months);

        } else if ($this->project_id == 59) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 96) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 129) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 130) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 139) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 137) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 140) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 135) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 113) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 141) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 142) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 145) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project_id == 74) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project->code == 'Al-Asr') {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project->code == 'ASJ') {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project->code == 'M-Aslam') {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project->code == 'PM-ALS' && $this->product_id == 15) {
            $this->inst_months = 1;
            $this->inst_type = 'Semi-Annually';
            $this->inst_amnt = $this->loan_amount;
        } else if ($this->project->code == 'HardoMky' && $this->product_id == 15) {
            $this->inst_months = 1;
            $this->inst_type = 'Semi-Annually';
            $this->inst_amnt = $this->loan_amount;
        } else if ($this->project->code == 'lwc' && $this->product_id == 12) {
            $this->inst_months = 3;
            $this->inst_type = 'Semi-Annually';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project->code == 'Asia-Poultry' && $this->product_id == 15) {
            $this->inst_months = 1;
            $this->inst_type = 'Semi-Annually';
            $this->inst_amnt = $this->loan_amount;    
        } else if ($this->project->code == 'Ali-Bhalli' && $this->product_id == 15) {
            $this->inst_months = 1;
            $this->inst_type = 'Semi-Annually';
            $this->inst_amnt = $this->loan_amount;
        } else if ($this->project->code == 'IFL-Ehsaas' && $this->product_id == 15) {
            $this->inst_months = 1;
            $this->inst_type = 'Semi-Annually';
            $this->inst_amnt = $this->loan_amount;
        } else if ($this->project->code == 'ARFSP' && $this->product_id == 15) {
            $this->inst_months = 1;
            $this->inst_type = 'Semi-Annually';
            $this->inst_amnt = $this->loan_amount;
        } else if ($this->project->code == 'Asia-Pultry' && $this->product_id == 15) {
            $this->inst_months = 1;
            $this->inst_type = 'Semi-Annually';
            $this->inst_amnt = $this->loan_amount;
        } else if (($this->project->code == 'Ali-Bhalli') && (in_array($this->product_id, [1, 13, 14, 16]))) {
            $this->inst_type = 'Monthly';
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
        } else if ($this->project->code == 'pmifl') {
            $this->inst_months = 12;
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            $this->inst_type = 'Monthly';
        } else if ($this->project->code == 'IFL-Ehsaas') {
            $this->inst_months = 12;
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            $this->inst_type = 'Monthly';
        } else if ($this->project->code == 'lwc' && (isset($this->activity->name) && $this->activity->name == 'Solar TubeWell')) {
            $this->inst_months = 3;
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            $this->inst_type = 'Semi-Annually';
        } else if ($this->project_id == 26) {
            $this->inst_months = 18;
            $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            $this->inst_type = 'Monthly';
        } else if ($this->project_id == 11) {
            $this->inst_amnt = 5000;
            $this->inst_months = ceil((($this->loan_amount / $this->inst_amnt) / 100) * 100);
            $this->inst_type = 'Monthly';

        } else if ($this->project_id == 1 && $this->project->code == 'PSIC') {
            if ($this->loan_amount > 0 && $this->loan_amount <= 35000) {
                $this->inst_months = 20;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 35000 && $this->loan_amount <= 50000) {
                $this->inst_months = 24;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            }
        } else if ($this->project_id == 134 && $this->project->code == 'Ehsaas Naujawan Program') {
            if ($this->loan_amount > 0 && $this->loan_amount <= 100000) {
                $this->inst_months = 18;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 100000 && $this->loan_amount <= 200000) {
                $this->inst_months = 24;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 200000) {
                $this->inst_months = 36;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            }

        } else if ($this->project_id == 71 && $this->project->code == 'SIDB') {
            if ($this->loan_amount > 0 && $this->loan_amount <= 22000) {
                $this->inst_months = 12;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 22000 && $this->loan_amount <= 50000) {
                $this->inst_months = 15;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 50000) {
                $this->inst_months = 20;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            }
        } else if ($this->project_id == 76) {
            $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
            $years = ($this->inst_months / 12);
            $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
            $total_amount = $this->loan_amount + $charges;
            $this->inst_amnt = round($total_amount / $this->inst_months);
        }else if ($this->project_id == 136) {
                $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
                $years = ($this->inst_months / 12);
                $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
                $total_amount = $this->loan_amount + $charges;
                $this->inst_amnt = round($total_amount / $this->inst_months);
        } else if ($this->project_id == 132) {
            $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
            $years = ($this->inst_months / 12);
            $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
            $total_amount = $this->loan_amount + $charges;
            $this->inst_amnt = round($total_amount / $this->inst_months);
        } else if ($this->project_id == 109) {
            $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
            $years = ($this->inst_months / 12);
            $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
            $total_amount = $this->loan_amount + $charges;
            $this->inst_amnt = round($total_amount / $this->inst_months);
        } else if ($this->project_id == 138) {
            $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
            $years = ($this->inst_months / 12);
            $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
            $total_amount = $this->loan_amount + $charges;
            $this->inst_amnt = round($total_amount / $this->inst_months);
        } else if ($this->project_id == 143) {
            $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
            $years = ($this->inst_months / 12);
            $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
            $total_amount = $this->loan_amount + $charges;
            $this->inst_amnt = round($total_amount / $this->inst_months);
        } else if ($this->project_id == 118) {
            $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
            $years = ($this->inst_months / 12);
            $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
            $total_amount = $this->loan_amount + $charges;
            $this->inst_amnt = round($total_amount / $this->inst_months);
//        }else if ($this->project_id == 119) {
//            $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
//            $years = ($this->inst_months / 12);
//            $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
//            $total_amount = $this->loan_amount + $charges;
//            $this->inst_amnt = round($total_amount / $this->inst_months);
        } else if ($this->project_id == 119) {
            if ($this->loan_amount > 0 && $this->loan_amount <= 30000) {
                $this->inst_type = 'Monthly';
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 30001 && $this->loan_amount <= 150000) {
                $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
                $years = ($this->inst_months / 12);
                $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
                $total_amount = $this->loan_amount + $charges;
                $this->inst_amnt = round($total_amount / $this->inst_months);
            }
        } else if ($this->project_id == 35) {
            if ($this->loan_amount > 0 && $this->loan_amount <= 50000) {
                $this->inst_type = 'Monthly';
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 50001 && $this->loan_amount <= 150000) {
                $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
                $years = ($this->inst_months / 12);
                $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
                $total_amount = $this->loan_amount + $charges;
                $this->inst_amnt = round($total_amount / $this->inst_months);
            }
        } else if ($this->project_id == 131) {
            if ($this->product_id == 15){
                if ($this->loan_amount <= 30000) {
                    $this->inst_months = 1;
                    $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
                    $this->inst_type = 'Semi-Annually';
                }else{
                    $this->inst_type = 'Semi-Annually';
                    $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
                    $charges = ($this->loan_amount * $charges_percentage * 0.5) / 100;
                    $total_amount = $this->loan_amount + $charges;
                    $this->inst_amnt = round($total_amount / $this->inst_months);
                }
            }else{
                if ($this->loan_amount <= 30000) {
                    $this->inst_type = 'Monthly';
                    $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
                }else{
                    $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
                    $years = ($this->inst_months / 12);
                    $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
                    $total_amount = $this->loan_amount + $charges;
                    $this->inst_amnt = round($total_amount / $this->inst_months);
                }
            }   
        } else if ($this->project_id == 126) {
            if ($this->loan_amount > 0 && $this->loan_amount <= 50000) {
                $this->inst_type = 'Monthly';
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 50001 && $this->loan_amount <= 300000) {
                $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
                $years = ($this->inst_months / 12);
                $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
                $total_amount = $this->loan_amount + $charges;
                $this->inst_amnt = round($total_amount / $this->inst_months);
            }
        } else if ($this->project_id == 24) {
            if ($this->loan_amount > 0 && $this->loan_amount <= 40000) {
                $this->inst_type = 'Monthly';
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 40001 && $this->loan_amount <= 300000) {
                $charges_percentage = LoanHelper::getProjectChargesFromId($this->project_id);
                $years = ($this->inst_months / 12);
                $charges = ($this->loan_amount * $charges_percentage * $years) / 100;
                $total_amount = $this->loan_amount + $charges;
                $this->inst_amnt = round($total_amount / $this->inst_months);
            }
        } else if ($this->project_id == 105) {
            if ($this->loan_amount > 0 && $this->loan_amount <= 50000) {
                $this->inst_amnt = 3000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 50001 && $this->loan_amount <= 75000) {
                $this->inst_amnt = 3500;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 75001 && $this->loan_amount <= 100000) {
                $this->inst_amnt = 4000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 100001 && $this->loan_amount <= 125000) {
                $this->inst_amnt = 4500;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 125001 && $this->loan_amount <= 150000) {
                $this->inst_amnt = 5000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 150001 && $this->loan_amount <= 175000) {
                $this->inst_amnt = 5500;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 175001 && $this->loan_amount <= 200000) {
                $this->inst_amnt = 6000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 200001 && $this->loan_amount <= 500000) {
                $this->inst_months = 36;
                $this->inst_amnt = round(($this->loan_amount / $this->inst_months) / 100) * 100;
            }
        } else if ($this->project_id == 106 && $this->product_id == 14) {
            if ($this->loan_amount > 0 && $this->loan_amount <= 50000) {
                $this->inst_amnt = 3000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 50001 && $this->loan_amount <= 75000) {
                $this->inst_amnt = 3500;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 75001 && $this->loan_amount <= 100000) {
                $this->inst_amnt = 4000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 100001 && $this->loan_amount <= 125000) {
                $this->inst_amnt = 4500;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 125001 && $this->loan_amount <= 150000) {
                $this->inst_amnt = 5000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 150001 && $this->loan_amount <= 175000) {
                $this->inst_amnt = 5500;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 175001 && $this->loan_amount <= 200000) {
                $this->inst_amnt = 6000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 200001 && $this->loan_amount <= 500000) {
                $this->inst_months = 36;
                $this->inst_amnt = round(($this->loan_amount / $this->inst_months) / 100) * 100;
            }
            
        } else {
            $this->inst_type = 'Monthly';
            if ($this->loan_amount > 0 && $this->loan_amount <= 20000) {
                $this->inst_months = 12;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 20000 && $this->loan_amount <= 30000) {
                $this->inst_amnt = 2000;
                $this->inst_months = ceil($this->loan_amount / $this->inst_amnt);
            } else if ($this->loan_amount > 30000 && $this->loan_amount <= 50000) {
                $this->inst_months = 15;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            } else if ($this->loan_amount > 50000) {
                if (!in_array($this->project_id, [128, 78, 87, 94, 83, 112])) {
                    $this->inst_months = 20;
                }
                //$this->inst_months = $this->inst_months;
                $this->inst_amnt = ceil(($this->loan_amount / $this->inst_months) / 100) * 100;
            }

        }
    }

    public function validateLoanAmount($application)
    {

        if (isset($application->appraisalsBusiness->new_required_assets_amount)) {
            if ($application->req_amount < $this->loan_amount || $application->appraisalsBusiness->new_required_assets_amount < $this->loan_amount) {
                $this->addError('loan_amount', 'Loan Amount is greater then the requested_amount or new required assets');
            }
        } else {
            if ($application->req_amount < $this->loan_amount) {
                $this->addError('loan_amount', 'Loan Amount is greater then the requested amount');
            }/*else if(in_array($application->project_id,StructureHelper::trancheProjects()) && ($application->recommended_amount < $this->loan_amount)){
                $this->addError('loan_amount', 'Loan Amount is greater then the recommended amount');
            }*/
        }
        if (($application->memberAccount->account_type == 'coc_accounts') && ($this->loan_amount > 100000) && (!in_array($application->project_id, [108, 98, 6, 10, 91, 104, 99, 113]))) {
            $this->addError('loan_amount', 'COC Limit cannot be greater then 100,000');
        }

        return $this;
    }

    public function validateDateApprovedConvertToIneger($attribute)
    {
        if (!is_numeric($this->date_approved)) {
            $this->date_approved = strtotime($this->date_approved);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDonations()
    {
        return $this->hasMany(Donations::className(), ['loan_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(Activities::className(), ['id' => 'activity_id']);
    }

    public function getVigaLoan()
    {
        return $this->hasOne(VigaLoans::className(), ['loan_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }

    public function getFundRequest()
    {
        return $this->hasOne(FundRequests::className(), ['id' => 'fund_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branches::className(), ['id' => 'branch_id']);
    }

    public function getTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }

    public function getField()
    {
        return $this->hasOne(Fields::className(), ['id' => 'field_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Groups::className(), ['id' => 'group_id']);
    }

    public function getDisbursement()
    {
        return $this->hasOne(Disbursements::className(), ['id' => 'disbursement_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Products::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoansLogs()
    {
        return $this->hasMany(LoansLogs::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operations::className(), ['loan_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectsDisabled()
    {
        return $this->hasMany(ProjectsDisabled::className(), ['loan_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectsTevta()
    {
        return $this->hasMany(ProjectsTevta::className(), ['loan_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecoveries()
    {
        return $this->hasMany(Recoveries::className(), ['loan_id' => 'id'])->andOnCondition(['recoveries.deleted' => 0]);
    }

    public function getTranchesSorted()
    {
        return $this->hasMany(LoanTranches::className(), ['loan_id' => 'id'])->orderBy('tranch_no asc');
    }

    public function getTranches()
    {
        return $this->hasMany(LoanTranches::className(), ['loan_id' => 'id'])->orderBy('tranch_no desc');
    }

    public function getReadyTranche()
    {
        return $this->hasOne(LoanTranches::className(), ['loan_id' => 'id'])->andOnCondition(['in', 'loan_tranches.status', [4, 5, 6, 7, 8]])->orderBy('tranch_no asc');
    }

    public function getDisbTranches()
    {
        return $this->hasMany(LoanTranches::className(), ['loan_id' => 'id'])->andOnCondition(['loan_tranches.status' => 6])->orderBy('tranch_no desc');
    }

    public function getActiveTranche()
    {
        return $this->hasOne(LoanTranches::className(), ['loan_id' => 'id'])->andOnCondition(['loan_tranches.status' => 1]);
    }

    public function getProcessedTranches()
    {
        return $this->hasMany(LoanTranches::className(), ['loan_id' => 'id'])->andOnCondition(['in', 'loan_tranches.status', [4, 5, 6]])->orderBy('tranch_no desc');
    }

    public function getTranch()
    {
        return $this->hasOne(LoanTranches::className(), ['loan_id' => 'id'])->andOnCondition(['loan_tranches.status' => 5]);
    }

    public function getActiveTranch()
    {
        return $this->hasOne(LoanTranches::className(), ['loan_id' => 'id'])->andOnCondition(['in', 'loan_tranches.status', [3, 5, 7]]);
    }

    public function getActive()
    {
        return $this->hasOne(LoanTranches::className(), ['loan_id' => 'id'])->andOnCondition(['in', 'loan_tranches.status', [/*1,2,6,*/
            1]]);
    }

    public function getFristTranch()
    {
        return $this->hasOne(LoanTranches::className(), ['loan_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedules::className(), ['loan_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }

    static public function getRecoveryInfo($schedule_id)
    {
        if (isset($schedule_id) && $schedule_id != 0) {
            return Recoveries::find()->where(['schedule_id' => $schedule_id, 'deleted' => 0])->all();
        }
        return false;
    }

    public function getActions()
    {
        return $this->hasMany(ApplicationActions::className(), ['parent_id' => 'id']);
    }

    public function getLoanactions()
    {
        return $this->hasMany(LoanActions::className(), ['parent_id' => 'id']);
    }

    public function getAccountVerification()
    {
        return $this->hasone(LoanActions::className(), ['parent_id' => 'id'])->andOnCondition(['loan_actions.action' => 'account_verification']);
    }

    public function getEmergency()
    {
        return $this->hasOne(EmergencyLoans::className(), ['loan_id' => 'id']);
    }

    static public function getRecovery($id)
    {
        $recoveryData =  Recoveries::find()
            ->where(['loan_id' => $id, 'deleted' => 0])
            ->select(['sum_amount' => 'SUM(amount)', 'sum_charges_amount' => 'SUM(charges_amount)'])
            ->asArray()
            ->one();
        if ($recoveryData) {
            $totalAmount = $recoveryData['sum_amount'];
            $totalChargesAmount = $recoveryData['sum_charges_amount'];
            return $totalAmount+$totalChargesAmount;
        } else {
            return 0;
        }

    }
}
