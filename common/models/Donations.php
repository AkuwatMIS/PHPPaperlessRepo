<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "donations".
 *
 * @property int $id
 * @property int $application_id
 * @property int $loan_id
 * @property int $schedule_id
 * @property int $branch_id
 * @property int $project_id
 * @property string $amount
 * @property string $debit
 * @property string $receive_date
 * @property string $receipt_no
 * @property int $deleted
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Applications $application
 * @property Branches $branch
 * @property Loans $loan
 * @property Projects $project
 * @property Recoveries $recovery
 * @property DonationsLogs[] $donationsLogs
 */
class Donations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public  $sanction_no;
    const WITHOUTRECOVERY = 'withoutrecovery';
    public static function tableName()
    {
        return 'donations';
    }
    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['amount','receipt_no','receive_date','project_id'],
                'table' => "donations_logs",
                //'ignored' => ['updated_at'],
            ]];

    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id', 'loan_id', 'branch_id', 'project_id','region_id','area_id','team_id','field_id', 'assigned_to', 'created_by', 'updated_by'], 'integer'],
            [['platform','loan_id', 'branch_id', 'region_id','area_id','team_id','field_id','receipt_no', 'assigned_to', 'receive_date'], 'required'],
            [['amount'], 'number'],
            [['receipt_no'], 'string', 'max' => 30],
           // [['deleted'], 'string', 'max' => 1],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loans::className(), 'targetAttribute' => ['loan_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schedules::className(), 'targetAttribute' => ['schedule_id' => 'id']],

            ['receive_date', 'validateRecvDate','on' => self::WITHOUTRECOVERY],
            ['receive_date','validateRecvDateConvertToIneger','on' => self::WITHOUTRECOVERY],
            [['sanction_no'],'required','on'=>'withoutrecovery'],
            [['sanction_no'],'required','on' => self::WITHOUTRECOVERY],
            ['amount', 'validateAmount','on' => self::WITHOUTRECOVERY],
            ['amount', 'required','on' => self::WITHOUTRECOVERY],
            [['receipt_no'], 'unique', 'targetAttribute' => ['receipt_no', 'branch_id'],'message' => 'This Receipt No already Exists.','filter' => ['deleted' => 0],'on' => self::WITHOUTRECOVERY],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::WITHOUTRECOVERY] = ['sanction_no','receipt_no','receive_date','amount'];
        return $scenarios;
    }
    public function validateRecvDateConvertToIneger($attribute){
        $this->receive_date = strtotime($this->receive_date);
    }
    public function validateAmount($attribute) {
        if(!isset($this->amount) || $this->amount == 0){
            $this->addError('amount','Donation Amount cannot be zero.');
        }
    }

    public function validateRecvDate($attribute) {
        if($this->isNewRecord){
            $currect_date = date('Y-m-d');
            //$third_of_every_month = date('Y-m-03');
            //$third_of_every_month = date('Y-m-01');
            $third_of_every_month = "1601550907";
            if($currect_date > $third_of_every_month){
                $last_months = date('Y-m-d',strtotime('last day of last month'));
            }else{
                //$last_months = date('Y-m-10',strtotime('first day of last month'));
                $last_months = date('Y-m-01',strtotime('first day of last month'));
            }

            if($this->receive_date > $currect_date){
                $this->addError('receive_date','You cannot post recovery in future date.');
            }
            if($this->receive_date <= $last_months){
                $this->addError('receive_date','You cannot post recovery in previous month.');
            }
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'loan_id' => 'Loan ID',
            'schedule_id' => 'Schedule ID',
            'branch_id' => 'Branch ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'field_id' => 'Field ID',
            'team_id' => 'Team ID',
            'project_id' => 'Project ID',
            'amount' => 'Amount',
            'receive_date' => 'Receive Date',
            'receipt_no' => 'Receipt No',
            'deleted' => 'Deleted',
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

    public function getApplication()
    {
        return $this->hasOne(Applications::className(), ['id' => 'application_id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
    public function getArea()
    {
        return $this->hasOne(Areas::className(), ['id' => 'area_id']);
    }
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
    public function getLoan()
    {
        return $this->hasOne(Loans::className(), ['id' => 'loan_id']);
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
    public function getSchedule()
    {
        return $this->hasOne(Schedules::className(), ['id' => 'schedule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDonationsLogs()
    {
        return $this->hasMany(DonationsLogs::className(), ['id' => 'id']);
    }
    public function getMemberForRecovery($sanc_no){

        {
            $returnData = [];
            $loan=Loans::find()->where(['sanction_no'=>$sanc_no])->all();


            if(!empty($loan)){
                if(count($loan) > 1){

                    $returnData['error'] = "More than one record found against this sanction number.";
                }else{
                    $returnData['name'] = $loan[0]->application->member->full_name;
                    $returnData['cnic'] = $loan[0]->application->member->cnic;
                }
            }else{
                $returnData['error'] = "No record found against this sanction number.";
            }
            return $returnData;
        }
    }

    public function beforeValidate()
    {
        $this->created_by = 1;
        if(parent::beforeValidate() && !empty($this->sanction_no)){

            $loan = Loans::find()->where(['sanction_no' => $this->sanction_no])->one();
            if(empty($loan)){
                return $this->addError('sanction_no', 'Sanction number not found.');
            }
            $schedules_exist = $loan->schedules;
            if(count($schedules_exist) <= 0){
                return $this->addError('application_id', 'Schedules Does Not exists');
            }
            $this->application_id=$loan->application_id;
            $this->loan_id=$loan->id;
            $this->region_id=$loan->region_id;
            $this->area_id=$loan->area_id;
            $this->branch_id=$loan->branch_id;
            $this->team_id=isset($loan->team_id) ? $loan->team_id : 0;
            $this->field_id=$loan->field_id;
            $this->schedule_id=$this->GetScheduleInfo($loan);
            $this->project_id=$loan->project_id;
            $this->created_by=isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            $this->assigned_to=0;

            return true;
        } else{
            return true;
        }
    }

    public function GetScheduleInfo($loan){

        $schedules = $loan->schedules;

        foreach ($schedules as $key => $part) {
            $sort[$key] = ($part['due_date']);
        }
        array_multisort($sort, SORT_ASC, $schedules);
        $first_schedule = '';
        foreach ($schedules as $s){
            $first_schedule = $s;
            break;
        }
        $last_schedule = end($schedules);

        $min_due_date = $first_schedule->due_date;
        $max_due_date = $last_schedule->due_date;

        if($loan->status=='1'){
            if(date('Y-m',( $loan->loan_completed_date))==date('Y-m',strtotime($this->receive_date))){
                return $last_schedule->id;
            }
            else{
                $this->addError('sanction_no', 'Loan is Completed');
            }
        }

        if($this->receive_date >= $min_due_date && $this->receive_date <= $max_due_date){
            foreach($schedules as $s){
                if(date('Y-m', strtotime($s->due_date)) == date('Y-m',strtotime($this->receive_date))){
                    $schedule_info = $s;
                }
            }
        }else if($this->receive_date < $min_due_date){
            $schedule_info = $first_schedule;
        }else if($this->receive_date > $max_due_date){
            $schedule_info = $last_schedule;
        }
        return $schedule_info->id;
    }
}
