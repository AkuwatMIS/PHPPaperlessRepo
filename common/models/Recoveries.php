<?php

namespace common\models;

use common\components\Helpers\StructureHelper;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "recoveries".
 *
 * @property int $id
 * @property int $application_id
 * @property int $schedule_id
 * @property int $loan_id
 * @property int $region_id
 * @property int $area_id
 * @property int $branch_id
 * @property int $team_id
 * @property int $field_id
 * @property string $due_date
 * @property string $credit_tax
 * @property string $receive_date
 * @property string $overdue
 * @property string $amount
 * @property string $receipt_no
 * @property int $project_id
 * @property int $type
 * @property string $source
 * @property int $is_locked
 * @property int $deleted
 * @property int $assigned_to
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Donations[] $donations
 * @property Applications $application
 * @property Areas $area
 * @property Branches $branch
 * @property Loans $loan
 * @property Projects $project
 * @property Regions $region
 * @property Schedules $schedule
 * @property RecoveriesLogs[] $recoveriesLogs
 * @property LoanWriteOff[] $writeOff
 */
class Recoveries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $sanction_no;
    public $mdp;
    public $mobile;
    public $total_amount;

    public static function tableName()
    {
        return 'recoveries';
    }

    public function behaviors()
    {
        return [TimestampBehavior::className(),
            'LogsBehavior' => [
                'class' => 'common\behavior\LogsBehavior',
                'allowed' => ['amount', 'receive_date', 'receipt_no', 'project_id'],
                'table' => "recoveries_logs",
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
            [['application_id', 'sanction_no', 'schedule_id', 'loan_id', 'region_id', 'area_id', 'branch_id', 'amount', 'receipt_no', 'type', 'source', 'is_locked', 'assigned_to', 'created_by', 'receive_date'], 'required'],
            [['platform', 'application_id', 'schedule_id', 'loan_id', 'region_id', 'area_id', 'branch_id', 'team_id', 'field_id', 'project_id', 'assigned_to', 'created_by', 'updated_by', 'due_date', /*'receivev_date'*/], 'integer'],
            [['amount', 'charges_amount'], 'number'],
            [['mdp', 'source'], 'string', 'max' => 20],
            [['receipt_no', 'credit_tax'], 'string', 'max' => 50],
            [['type', 'is_locked'], 'string', 'max' => 3],
            [['deleted'], 'string', 'max' => 1],
            [['mobile'], 'safe'],
            ['receive_date', 'validateRecvDate'],
            ['receipt_no', 'validateReceiptNo'],
            //['receive_date','validateRecvDateConvertToIneger'],
            ['amount', 'validateAmount'],
            [['application_id'], 'exist', 'skipOnError' => true, 'targetClass' => Applications::className(), 'targetAttribute' => ['application_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Areas::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branches::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loans::className(), 'targetAttribute' => ['loan_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schedules::className(), 'targetAttribute' => ['schedule_id' => 'id']],
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
            'schedule_id' => 'Schedule ID',
            'loan_id' => 'Loan ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'branch_id' => 'Branch ID',
            'team_id' => 'Team ID',
            'field_id' => 'Field ID',
            'due_date' => 'Due Date',
            'receive_date' => 'Receive Date',
            'amount' => 'Amount',
            'receipt_no' => 'Receipt No',
            'project_id' => 'Project ID',
            'type' => 'Type',
            'source' => 'Source',
            'is_locked' => 'Is Locked',
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
    public function getDonations()
    {
        return $this->hasMany(Donations::className(), ['recovery_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'created_by']);
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
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
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
    public function getRecoveriesLogs()
    {
        return $this->hasMany(RecoveriesLogs::className(), ['id' => 'id']);
    }

    public function getWriteOff()
    {
        return $this->hasOne(LoanWriteOff::className(), ['recovery_id' => 'id']);
    }

    public function validateRecvDateConvertToIneger($attribute)
    {
        $this->receive_date = strtotime($this->receive_date);
    }

    public function validateReceiptNo($attribute)
    {
        $receipt_no = self::find()->where(['receipt_no' => $this->receipt_no, 'branch_id' => $this->branch_id, 'deleted' => 0])->one();
        if (!empty($receipt_no)) {
            $this->addError('receipt_no', 'Receipt No already exists.');
        }
    }

    public function getMemberForRecovery($sanc_no)
    {
        $returnData = [];
        $loan = Loans::find()->where(['sanction_no' => $sanc_no])->all();


        if (!empty($loan)) {
            if (count($loan) > 1) {

                $returnData['error'] = "More than one record found against this sanction number.";
            } else if ($loan[0]['status'] == 'not collected') {
                $returnData['error'] = "You cannot post recovery against cancelled loan.";
            } else {
                $returnData['name'] = $loan[0]->application->member->full_name;
                $returnData['cnic'] = $loan[0]->application->member->cnic;
            }
        } else {
            $returnData['error'] = "No record found against this sanction number.";
        }
        return $returnData;
    }

    public function validateAmount($attribute)
    {
        if (!in_array($this->project_id, StructureHelper::trancheProjects())) {
            if (!isset($this->amount) || $this->amount == 0) {
                $this->addError('amount', 'Recovery Amount cannot be zero.');
            }
        }
    }

    public function validateRecvDate($attribute)
    {
        if ($this->isNewRecord && ($this->source == '1' || $this->source == 'branch')) {
            $currect_date = strtotime("now");
            //$third_of_every_month = strtotime('+4 days',strtotime("now first day of this month"));
            // $third_of_every_month = strtotime("now first day of this month");
            $third_of_every_month = "1601550907";
            if ($currect_date > $third_of_every_month) {
                $last_months = strtotime('now last day of last month');
            } else {
                $last_months = strtotime('now first day of last month');
                //$last_months = strtotime('+19 days',strtotime("now first day of last month"));
            }

            if ($this->receive_date > $currect_date && $this->amount>0) {
                $this->addError('receive_date', 'You cannot post recovery in future date.');
            }
            if ($this->receive_date <= $last_months && $this->amount>0) {
                $this->addError('receive_date', 'You cannot post recovery in previous month.');
            }
        }
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate() && !empty($this->sanction_no)) {

            //Get Loan Info
            $loan = Loans::find()->where(['sanction_no' => $this->sanction_no])->one();
            if (empty($loan)) {
                return $this->addError('sanction_no', 'Sanction number not found.');
            }

            //Check If Schedules Exists
            $schedules_exist = $loan->schedules;
            if (count($schedules_exist) <= 0) {
                return $this->addError('schedule_id', 'Schedules Does Not exists');
            }

            //Check If Recovery Exists
            $recoveries_exist = $loan->recoveries;
            $this->loan_id = $loan->id;

            if ($loan->status == 'loan completed' && $this->amount>0) {
                $this->addError('loan_id', 'Loan of this Borrower is completed');
            }
            if ($loan->status == 'not collected' && $this->amount>0) {
                $this->addError('loan_id', 'You cannot post recovery against cancelled loan.');
            }

            //Check Recovery Amount less than balance
            $cum_recv = $this->find()->select('sum(amount) as cum_amount, sum(charges_amount) as cum_charges_amount')->where([
                'loan_id' => $loan->id,
                'recoveries.deleted' => '0'
            ])->asArray()->one();
            $schedule_info = $this->GetScheduleInfo($loan);

            $this->schedule_id = $schedule_info['id'];
            $this->charges_amount = 0;

            if (in_array($loan->project_id, StructureHelper::trancheProjects()) && $this->credit_tax==0) {

                $current_tax_received = 0;
                $current_charges_received = 0;

                $current_rec_received = $this->find()
                    ->where(['loan_id' => $loan->id])
                    ->andWhere(['recoveries.deleted' => 0])
                    ->all();


                $schedule_till_this_date = Schedules::find()
                    ->where([
                        'loan_id' => $loan->id
                    ])->andWhere(["<=",'due_date' , $schedule_info['due_date']])
                    ->groupBy('id')
                    ->sum('charges_schdl_amount');

                $recovery_till_this_date = $this->find()
                    ->where(['loan_id' => $loan->id])
                    ->andWhere(["<=",'receive_date' , $schedule_info['due_date']])
                    ->andWhere(['deleted' => 0])
                    ->groupBy('id')
                    ->sum('charges_amount');

                if(empty($recovery_till_this_date)){
                    $recovery_till_this_date = 0;
                }



                if (!empty($current_rec_received) && $current_rec_received != null) {
                    foreach ($current_rec_received as $res){
                        $current_tax_received = $current_tax_received+(int)$res->credit_tax;
                        $current_charges_received = $current_charges_received+(int)$res->charges_amount;
                    }
                }

//                -------------tax nd charges till current date-----------

                //Get Schedule Info
                $prev_schdl = Schedules::find()
                    ->select('sum(schdl_amnt) as schdl_amount,sum(charges_schdl_amount) as charges_schdl_amount,sum(credit) as amount,sum(charges_credit) as charges_amount')
                    ->where(['loan_id' => $this->loan_id])
                    ->andWhere(['<=', 'due_date', $schedule_info['due_date']])
                    ->asArray()
                    ->one();
                $tax = Schedules::find()->where(['<=','due_date' , strtotime(date('Y-m-10',$this->receive_date))])
                    ->andWhere(['loan_id' => $loan->id])
                    ->sum('charges_schdl_amnt_tax');

                $taxReceivable = $tax - $current_tax_received;
                $dueReceivable = $prev_schdl['schdl_amount'] - $cum_recv['cum_amount'];
                $chargesReceivable = $prev_schdl['charges_schdl_amount'] - $cum_recv['cum_charges_amount'];

                if ($chargesReceivable > 0 || $dueReceivable >0 || $taxReceivable >0) {

                    $required_charges_received = $schedule_till_this_date-$recovery_till_this_date;

                    if ($this->amount > $taxReceivable) {
                        $this->credit_tax = (string)$taxReceivable;

                        if ($required_charges_received > 0) {
                            if ($required_charges_received == ($this->amount - $taxReceivable) || $required_charges_received > ($this->amount - $taxReceivable)) {
                                $this->charges_amount = $this->amount - $taxReceivable;
                            } elseif ($required_charges_received < ($this->amount - $taxReceivable)) {
                                $this->charges_amount = $required_charges_received;
                            }
                        } else {
                            $this->charges_amount = 0;
                        }


                        if (($this->amount - $this->charges_amount - $taxReceivable) > 0) {
                            $this->amount = $this->amount - $this->charges_amount - $taxReceivable;
                        } else {
                            $this->amount = 0;
                        }
                    } else {

                        $thisMonthTaxReceived = Recoveries::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['recoveries.deleted' => 0])
                            ->andWhere(['between', 'recoveries.receive_date', strtotime(date('Y-m-01 00:00:00')), strtotime(date('Y-m-t 23:59:50'))])
                            ->sum('credit_tax');

                        $thisMonthChargesReceived = Recoveries::find()->where(['loan_id' => $loan->id])
                            ->andWhere(['recoveries.deleted' => 0])
                            ->andWhere(['between', 'recoveries.receive_date', strtotime(date('Y-m-01 00:00:00')), strtotime(date('Y-m-t 23:59:50'))])
                            ->sum('charges_amount');

                        if($thisMonthTaxReceived<$tax){
                            $this->credit_tax = (string)$this->amount;
                            $this->charges_amount = 0;
                            $this->amount = 0;
                        }elseif($thisMonthChargesReceived<$prev_schdl['charges_schdl_amount']){
                            $this->credit_tax = 0;
                            $this->charges_amount = (int)$this->amount;
                            $this->amount = 0;
                        }else{
                            $this->credit_tax = 0;
                            $this->charges_amount = 0;
                            $this->amount = (int)$this->amount;
                        }
                    }
                }
            }

           
            if ($this->amount > ($loan->balance) && count($recoveries_exist) > 0) {
                $this->addError('amount', 'Amount is Greater than Remaining Balance.');
            }

            if (($this->amount + $cum_recv['cum_amount']) > $loan->loan_amount) {
                $this->addError('loan_id', 'Total Recovered Amount exceeds than Loan Amount');
            }
            /*$prev_amount = Schedules::find()->select('sum(credit) as amount')->where(['loan_id' => $this->loan_id])->andWhere(['=', 'due_date', $schedule_info['due_date']])->asArray()->one();
            if($schedule_info['charges_schdl_amount'] != 0 && $schedule_info['charges_credit'] != $schedule_info['charges_schdl_amount'])
            {
                if ($schedule_info['schdl_amnt'] <= $schedule_info['credit']) {
                    $this->amount = $this->amount;
                }
                if ($this->amount + $prev_amount['amount'] <= $schedule_info['schdl_amnt']) {
                    $this->amount = $this->amount;
                }
            }*/
            ///// service charges amount
            /*$prev_amount = Schedules::find()->select('sum(credit) as amount')->where(['loan_id' => $this->loan_id])->andWhere(['<=', 'due_date', $this->due_date])->asArray()->one();

            if ($schedule_info['charges_schdl_amount'] != 0 && $schedule_info['charges_credit'] != $schedule_info['charges_schdl_amount'] && $this->amount >= $schedule_info['schdl_amnt']) {
                if ($this->amount <= $schedule_info['schdl_amnt']) {
                    $this->amount = $this->amount;
                } else if ($this->amount <= ($schedule_info['schdl_amnt'] - $prev_amount['amount'])) {

                    $this->amount = $schedule_info['schdl_amnt'];
                    $this->charges_amount = $this->amount - $schedule_info['schdl_amnt'];
                }


                $diff = ($this->amount + $prev_amount['amount']) - $schedule_info['schdl_amnt'];
                if ($diff >= $schedule_info['charges_schdl_amount']) {
                    $this->charges_amount = $schedule_info['charges_schdl_amount'];
                } else {
                    $this->charges_amount = $diff;
                }
                $this->amount = $this->amount - $this->charges_amount;
            }*/
            /*$diff = $schedule_info['schdl_amnt'] - $schedule_info['charges_schdl_amount'];
            $rec_diff = $this->amount - $diff ;
            if($rec_diff >  $schedule_info['charges_schdl_amount'])
            {
                $this->charges_amount = $schedule_info['charges_schdl_amount'];
            } else if ($rec_diff >= 0){
                $this->charges_amount = $rec_diff;
            }*/
            ///////////////////////////////////////////

            $this->application_id = $loan->application_id;
            $this->branch_id = $loan->branch_id;
            $this->area_id = $loan->area_id;
            $this->region_id = $loan->region_id;
            $this->project_id = $loan->project_id;
            $this->created_by = isset($this->created_by) ? $this->created_by : Yii::$app->user->getId();
            //$this->created_by = isset($this->user_id) ? $this->user_id : Yii::$app->user->getId();
            $this->due_date = $schedule_info->due_date;
            $this->team_id = isset($loan->team_id) ? $loan->team_id : 0;
            $this->field_id = $loan->field_id;
            $this->is_locked = '0';
            $this->source = isset($this->source) ? $this->source : 'branch';
            $this->type = '1';
            $this->assigned_to = 0;

            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {

        $connection = Yii::$app->db;
        $schedule_info = Schedules::find()->where(['id' => $this->schedule_id])->asArray()->one();
        $loan = $this->getLoan()->asArray()->one();

        $overdue_query = Schedules::find()->select('sum(schdl_amnt) as schdl_amnt,sum(charges_schdl_amount) as charges_schdl_amnt, sum(credit) as amount,sum(charges_credit) as charges_amount')->where(['loan_id' => $this->loan_id])->andWhere(['<=', 'due_date', $this->due_date])->asArray()->one();
        $charges_overdue_log = $charges_advance_log = $charges_due = $charges_balance = 0;
        if (in_array($loan['project_id'], StructureHelper::trancheProjects())) {
            ////service charges working

            $charges_cum_recovery = $overdue_query['charges_amount'] + $this->charges_amount;
            $charges_overdue_advance = $overdue_query['charges_schdl_amnt'] - $charges_cum_recovery;

            if ($loan['loan_amount'] != $loan['disbursed_amount']) {
                $charges_balance = $loan['service_charges'] / 2 - $charges_cum_recovery;
            } else {
                $charges_balance = $loan['service_charges'] - $charges_cum_recovery;
            }

            if ($charges_overdue_advance > 0) {
                $charges_overdue_log = abs($charges_overdue_advance);
            } else {
                $charges_advance_log = abs($charges_overdue_advance);
            }
            if ($schedule_info['charges_schdl_amount'] > $charges_balance) {
                $charges_due = $charges_balance;
            } else {
                $charges_due = $schedule_info['charges_schdl_amount'] + $charges_overdue_log;
            }
            /////////////////////////////////////////////////////////////////////////////
        }

        $overdue_log = $advance_log = $due = $balance = 0;

        $cum_recovery = $overdue_query['amount'] + $this->amount;
        $overdue_advance = $overdue_query['schdl_amnt'] - $cum_recovery;
        $balance = $loan['disbursed_amount'] - $cum_recovery;
        if ($overdue_advance > 0) {
            $overdue_log = abs($overdue_advance);
        } else {
            $advance_log = abs($overdue_advance);
        }

        $next_date = date("Y-m-10", strtotime("+1 month", strtotime($schedule_info['due_date'])));
        
        if ($schedule_info['schdl_amnt'] > $balance) {
            $due = $balance;
        } else {
            $due = $schedule_info['schdl_amnt'] + $overdue_log;
        }

        $credit_tax = 0;
        //if($this->branch_id == 814){
        $recovery = Recoveries::find()->where(['id' => $schedule_info['id'], 'deleted' => 1])->one();
        if (!$recovery) {
            $credit_tax = $schedule_info['charges_schdl_amnt_tax'];
            //$credit_tax = 0;
        }
        //    }


        $date = gmdate("Y-m-d H:i:s");
        $connection->createCommand("UPDATE schedules SET 
                                                    overdue_log = '" . $overdue_log . "',
                                                    advance_log = '" . $advance_log . "',
                                                    credit = credit + $this->amount,
                                                    overdue = '" . $charges_overdue_log . "',
                                                    advance = '" . $charges_advance_log . "',
                                                    credit_tax = '" . $credit_tax . "',
                                                    charges_credit = charges_credit + $this->charges_amount where id = '" . $this->schedule_id . "'")->execute();
        $connection->createCommand("UPDATE schedules SET due_amnt = '" . $due . "',charges_due_amount = '" . $charges_due . "' where loan_id = '" . $this->loan_id . "' and due_date = '" . strtotime($next_date) . "'")->execute();
        if ($balance <= 0) {
            $connection->createCommand("UPDATE loans SET 
                                            due = '" . $due . "', 
                                            overdue = '" . $overdue_log . "', 
                                            balance = '" . $balance . "' ,
                                            charges_due = '" . $charges_due . "', 
                                            charges_overdue = '" . $charges_overdue_log . "', 
                                            charges_balance = '" . $charges_balance . "' ,
                                            loan_completed_date = '" . $this->receive_date . "',
                                            status = 'loan completed' where id = '" . $this->loan_id . "'")->execute();
        } else {
            $connection->createCommand("UPDATE loans SET 
                                            due = '" . $due . "', 
                                            overdue = '" . $overdue_log . "', 
                                            balance = '" . $balance . "',
                                            charges_due = '" . $charges_due . "', 
                                            charges_overdue = '" . $charges_overdue_log . "', 
                                            charges_balance = '" . $charges_balance . "' where id = '" . $this->loan_id . "'")->execute();
        }

        $thisSchedule = Schedules::find()->where(['id'=>$this->schedule_id])->one();
        if(!empty($thisSchedule)){
            $thisSchedule->credit_tax = $thisSchedule->credit_tax+(int)$this->credit_tax;
            if(!$thisSchedule->save()){
                var_dump($thisSchedule->getErrors());
                die();
            }
        }

        //save donation
        if ($this->mdp > 0) {
            $donation = new Donations();
            $donation->application_id = $this->application_id;
            $donation->loan_id = $this->loan_id;
            $donation->schedule_id = $this->schedule_id;
            $donation->branch_id = $this->branch_id;
            $donation->area_id = $this->area_id;
            $donation->region_id = $this->region_id;
            $donation->team_id = $this->team_id;
            $donation->field_id = $this->field_id;
            $donation->amount = $this->mdp;
            $donation->receive_date = $this->receive_date;
            $donation->receipt_no = $this->receipt_no;
            $donation->project_id = $this->project_id;
            $donation->created_by = isset($this->user_id) ? $this->user_id : Yii::$app->user->getId();
            $donation->assigned_to = 0;
            $donation->source = $this->source;
            $donation->platform = 1;
            if ($donation->save()) {

            } else {
                print_r($donation->getErrors());
                die('here');
                $this->addError('recovery_id', 'Donation does not saved.');
            }
        }
    }

    public function GetScheduleInfo($loan, $recv_date = 0)
    {

        if ($recv_date != 0) {
            $this->receive_date = $recv_date;
        }
        $schedules = $loan->schedules;

        foreach ($schedules as $key => $part) {
            $sort[$key] = ($part['due_date']);
        }
        array_multisort($sort, SORT_ASC, $schedules);
        $first_schedule = '';
        foreach ($schedules as $s) {
            $first_schedule = $s;
            break;
        }
        $last_schedule = end($schedules);

        $min_due_date = $first_schedule->due_date;
        $max_due_date = $last_schedule->due_date;
        if ($this->receive_date >= $min_due_date && $this->receive_date <= $max_due_date) {
            foreach ($schedules as $s) {
                if (date('Y-m', $s->due_date) == date('Y-m', $this->receive_date)) {
                    $schedule_info = $s;
                }
            }
            if (!isset($schedule_info)) {
                foreach ($schedules as $s) {
                    if (date('Y-m', $s->due_date) < date('Y-m', $this->receive_date)) {
                        $schedule_info = $s;
                    }
                }
            }
        } else if ($this->receive_date < $min_due_date) {
            $schedule_info = $first_schedule;
        } else if ($this->receive_date > $max_due_date) {
            $schedule_info = $last_schedule;
        }
        return $schedule_info;
    }
}
