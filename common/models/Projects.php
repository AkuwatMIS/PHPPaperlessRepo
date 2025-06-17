<?php

namespace common\models;

use common\components\Helpers\TranchesHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property string $project_table
 * @property string $bank_prefix
 * @property string $name
 * @property string $code
 * @property string $donor
 * @property string $funding_line
 * @property string $started_date
 * @property string $logo
 * @property string $description
 * @property int $status
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property int $application_fee
 * @property string $created_at
 * @property string $updated_at
 * @property string $fund_source
 * @property Applications[] $applications
 * @property ArchiveReports[] $archiveReports
 * @property BranchProjectsMapping[] $branchProjectsMappings
 * @property Donations[] $donations
 * @property Loans[] $loans
 * @property Operations[] $operations
 * @property ProgressReports[] $progressReports
 * @property Recoveries[] $recoveries
 */
class Projects extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public $current_fund_receive;
    public $file;
    public static function tableName()
    {
        return 'projects';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['total_fund','loan_amount_limit','fund_received','received_amount','charges_percent'],
                'table' => "projects_logs",
                //'ignored' => ['updated_at'],
            ]];

    }
    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [[ 'name', 'code'/*, 'assigned_to', 'created_by'*/], 'required'],
            [['description','bank_prefix'], 'string'],
            [['application_fee','assigned_to','loan_amount_limit','fund_received', 'total_fund','created_by', 'updated_by','sc_due','received_amount','adjustment_made','current_fund_receive'], 'integer'],
            [['project_table'], 'string', 'max' => 50],
            [['charges_percent'], 'number'],
            [['name', 'code', 'donor', 'logo'], 'string', 'max' => 255],
            [['funding_line'], 'string', 'max' => 5],
            [['sc_type','sector'], 'string', 'max' => 15],
            [['short_name','fund_source'], 'string', 'max' => 30],
            ['received_date', 'validateReceivedDate'],
            ['started_date', 'validateStartDate'],
            ['ending_date', 'validateEndDate'],
//            [['file'], 'file', 'extensions' => ['csv', 'xls', 'xlsx', 'pdf'], 'checkExtensionByMimeType' => false]
            [['file'], 'file']

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_table' => 'Project Table',
            'name' => 'Name',
            'code' => 'Code',
            'donor' => 'Donor',
            'funding_line' => 'Funding Line',
            'fund_source' => 'Fund Source',
            'started_date' => 'Started Date',
            'logo' => 'Logo',
            'description' => 'Description',
            'received_date' => 'Received Date',
            'received_amount' => 'Received Amount',
            'sc_type' => 'Type',
            'sc_due' => 'Due Amount',
            'status' => 'Status',
            'application_fee' => 'Application Fee',
            'bank_prefix' => 'Bank prefix Code',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

//    public function afterSave($insert, $changedAttributes){
//
//        $data = TranchesHelper::getTranchesData();
//        TranchesHelper::saveTranchesFile($data);
//    }

    public function validateReceivedDate($attribute){
        if(!is_numeric($this->received_date)) {
            $this->received_date = strtotime($this->received_date);
        }
    }

    public function validateStartDate($attribute){
        if(!is_numeric($this->started_date)) {
            $this->started_date = strtotime($this->started_date);
        }
    }
    public function validateEndDate($attribute){
        if(!is_numeric($this->ending_date)) {
            $this->ending_date = strtotime($this->ending_date);
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApplications()
    {
        return $this->hasMany(Applications::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArchiveReports()
    {
        return $this->hasMany(ArchiveReports::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranchProjectsMappings()
    {
        return $this->hasMany(BranchProjectsMapping::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDonations()
    {
        return $this->hasMany(Donations::className(), ['project_id' => 'id']);
    }

    public function getServiceCharges()
    {
        return $this->hasOne(ProjectCharges::className(), ['project_id' => 'id'])->orderBy('id desc');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoans()
    {
        return $this->hasMany(Loans::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperations()
    {
        return $this->hasMany(Operations::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgressReports()
    {
        return $this->hasMany(ProgressReports::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecoveries()
    {
        return $this->hasMany(Recoveries::className(), ['project_id' => 'id']);
    }

    public function getAppraisals()
    {
        return $this->hasMany(ProjectAppraisalsMapping::className(), ['project_id' => 'id']);
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
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
}
