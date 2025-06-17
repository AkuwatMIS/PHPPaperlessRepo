<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */

namespace common\components\Helpers;

use common\models\DisbursementDetails;
use common\models\Donations;
use common\models\Funds;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\ProjectFundDetail;
use common\models\Recoveries;
use common\models\Schedules;
use common\models\SchedulesHousingPre;

class FixesHelper
{
    public static function fix_schedules_update($loan)
    {
        $connection = \Yii::$app->db;

        //clear all schedules
        $sql = "UPDATE schedules SET due_amnt=0,  overdue_log = 0, credit = 0, advance_log = 0, charges_due_amount = 0 ,overdue = 0, advance= 0 ,charges_credit = 0 where loan_id = '" . $loan->id . "'";
        $connection->createCommand($sql)->execute();
        $recoveries = Recoveries::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->orderBy('receive_date asc')->all();

        foreach ($recoveries as $r) {
            $schdl = Schedules::findOne(['id' => $r->schedule_id]);
            if (!empty($schdl)) {
                $schedule = $schdl;
            } else {
                die('Schedule not found with recovery id : ' . $r['R']['id']);
            }
            $connection->createCommand("UPDATE schedules SET credit = credit + '" . $r->amount . "', charges_credit = charges_credit + '" . $r->charges_amount . "' where id = '" . $r->schedule_id . "'")->execute();

        }

        $due_date = strtotime('+9 days', strtotime('first day of this month'));

        //select all schedules and update due, overdue and advance amount of all schedules
        $schedules = Schedules::find()->where(['loan_id' => $loan->id])->andWhere(['<=', 'due_date', $due_date])->orderBy('due_date asc')->all();

        if(empty($schedules)){
            $schd = Schedules::find()->where(['loan_id' => $loan->id])->orderBy('due_date asc')->one();
            $schedules[]=$schd;

        }
        $total_credit = $due = $overdue_log = $advance_log = 0;

        foreach ($schedules as $key => $s) {
            $charges_total_credit = $charges_due = $charges_overdue_log = $charges_advance_log = 0;
            //echo '<h2>'.$key.'</h2>';
            $last_day_of_month = strtotime(date('Y-m-t', $s->due_date));
            $overdue_query = "select (sum(schdl_amnt) - (select COALESCE(sum(amount),0) from recoveries where loan_id = '" . $s->loan_id . "' and receive_date <= '" . $last_day_of_month . "' and deleted = 0)) as overdue_advance, (sum(charges_schdl_amount) - (select COALESCE(sum(charges_amount),0) from recoveries where loan_id = '" . $s->loan_id . "' and receive_date <= '" . $last_day_of_month . "' and deleted = 0)) as charges_overdue_advance from schedules where loan_id = '" . $loan->id . "' and due_date <= '" . $s->due_date . "'";

            $overdue_advance = $connection->createCommand($overdue_query)->queryAll();

            $next_date = strtotime("+1 month", $s->due_date);
            //print_r($s->due_date);

            //if($next_date <= $due_date){,
            if ($overdue_advance[0]['overdue_advance'] > 0) {
                $overdue_log = abs($overdue_advance[0]['overdue_advance']);
            } else {
                $advance_log = abs($overdue_advance[0]['overdue_advance']);
            }

            if ($overdue_advance[0]['charges_overdue_advance'] > 0) {
                $charges_overdue_log = abs($overdue_advance[0]['charges_overdue_advance']);
            } else {
                $charges_advance_log = abs($overdue_advance[0]['charges_overdue_advance']);
            }

            // update schedule.overdue_log& schedule.advance_log
            $sql = "UPDATE schedules SET overdue_log  = overdue_log + '" . $overdue_log . "', advance_log = advance_log+'" . $advance_log . "',
                                         overdue = '" . $charges_overdue_log . "', advance = advance +'" . $charges_advance_log . "' where id = '" . $s->id . "' ";
            $connection->createCommand($sql)->execute();

            // update schedule.due_amnt
            if ($key == 0) {
                $due = $s->schdl_amnt;
                $charges_due = $s->charges_schdl_amount;
                $update_due = "UPDATE schedules SET due_amnt = '" . $due . "', charges_due_amount = '" . $charges_due . "' where loan_id = '" . $loan->id . "' and due_date = '" . $s->due_date . "' ";
                $connection->createCommand($update_due)->execute();
            } else {

                $due = ($s->schdl_amnt - $advance_log) + $overdue_log;
                $charges_due = ($s->charges_schdl_amount - $charges_advance_log) + $charges_overdue_log;
                $update_due = "UPDATE schedules SET due_amnt = '" . $due . "', charges_due_amount = '" . $charges_due . "' where loan_id = '" . $loan->id . "' and due_date = '" . $next_date . "' ";
                $connection->createCommand($update_due)->execute();
            }

            //}

            $total_credit += $s->credit;
            $charges_total_credit += $s->charges_credit;
        }

        //update balance, due and advance in loans table
        $total_credit = Recoveries::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->sum('amount');
        $charges_total_credit = Recoveries::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->sum('charges_amount');

        $last_recovery = array();
        if (!empty($recoveries)) {
            $last_recovery = end($recoveries);
        }
        $loan->due = $due;
        $loan->overdue = $overdue_log;
        $loan->balance = $loan->disbursed_amount - $total_credit;
        $loan->charges_due = $charges_due;
        $loan->charges_overdue = $charges_overdue_log;
        $loan->charges_balance = $loan->service_charges - $charges_total_credit;
        $loan->status = 'collected';
        $loan->loan_completed_date = 0;
        if ($loan->balance == 0) {
            $loan->status = 'loan completed';
            $loan->loan_completed_date = $last_recovery->receive_date;
        }
        $connection = \Yii::$app->db;
        $connection->createCommand("UPDATE loans SET 
                                            due = '".$due."'
                                           , overdue = '".$overdue_log."'
                                           , balance = '".$loan->balance."'
                                           , charges_due = '".$charges_due."'
                                           , charges_overdue = '".$charges_overdue_log."'
                                           , charges_balance = '".$loan->charges_balance."'
                                           , status = '".$loan->status."'
                                           , loan_completed_date = '".$loan->loan_completed_date."'
                                             where id = '".$loan->id."'")->execute();

        /*if ($loan->save()) {
            return 1;
        } else {
            echo 'Schedule updated, Loan due overdue not updated' . '<br>';
        }*/
        //die('here');
    }
    public static function adjust_recovery($loan)
    {
        $recoveries=Recoveries::find()->where(['loan_id'=>$loan->id,'deleted'=>'0'])->all();
        foreach($recoveries as $recovery){
            $connection = \Yii::$app->db;
            $r=new Recoveries();
            $schedule_info = $r->GetScheduleInfo($loan,$recovery->receive_date);
            $recovery->schedule_id=$schedule_info->id;
            $recovery->due_date = $schedule_info->due_date;
            //$recovery->save(false);
            ///update recovery
            $connection->createCommand("UPDATE recoveries SET 
                                                    schedule_id = '".$schedule_info->id."',
                                                    due_date = '".$schedule_info->due_date."'
                                                     where id = '".$recovery->id."'")->execute();
            
            ///update schedule
            $schedule_info = Schedules::find()->where(['id'=>$recovery->schedule_id])->asArray()->one();
            $_loan = Loans::find()->where(['id'=>$loan->id])->asArray()->one();
            $overdue_query = Schedules::find()->select('sum(schdl_amnt) as schdl_amnt ,sum(charges_schdl_amount) as charges_schdl_amnt, sum(credit) as amount,sum(charges_credit) as charges_amount')->where(['loan_id' => $recovery->loan_id])->andWhere(['<=','due_date', $recovery->due_date])->asArray()->one();

            ////service charges working
            $charges_overdue_log = $charges_advance_log = $charges_due = $charges_balance = 0;
            $charges_cum_recovery = $overdue_query['charges_amount'] + $recovery->charges_amount;
            $charges_overdue_advance = $overdue_query['charges_schdl_amnt'] - $charges_cum_recovery;
            if($_loan['loan_amount'] != $_loan['disbursed_amount']) {
                $charges_balance = $_loan['service_charges']/2 - $charges_cum_recovery;
            } else {
                $charges_balance = $_loan['service_charges'] - $charges_cum_recovery;
            }


            if($charges_overdue_advance > 0){
                $charges_overdue_log = abs($charges_overdue_advance);
            }else{
                $charges_advance_log = abs($charges_overdue_advance);
            }
            if($schedule_info['charges_schdl_amount'] > $charges_balance){
                $charges_due = $charges_balance;
            }else{
                $charges_due = $schedule_info['charges_schdl_amount'] + $charges_overdue_log;
            }
            /////////////////////////////////////////////////////////////////////////////


            $overdue_log = $advance_log = $due = $balance = 0;
            $cum_recovery = $overdue_query['amount'] + $recovery->amount;

            $overdue_advance = $overdue_query['schdl_amnt'] - $cum_recovery;
            $balance = $_loan['disbursed_amount'] - $cum_recovery;
            if($overdue_advance > 0){
                $overdue_log = abs($overdue_advance);
            }else{
                $advance_log = abs($overdue_advance);
            }

            $next_date = date("Y-m-10", strtotime("+1 month", strtotime($schedule_info['due_date'])));
            if($schedule_info['schdl_amnt'] > $balance){
                $due = $balance;
            }else{
                $due = $schedule_info['schdl_amnt'] + $overdue_log;
            }

            if(!empty($recovery->credit_tax)){
                $creditTax = $recovery->credit_tax;
            }else{
                $creditTax = 0;
            }


            $date = gmdate("Y-m-d H:i:s");
            $connection->createCommand("UPDATE schedules SET 
                                                    overdue_log = '".$overdue_log."',
                                                    advance_log = '".$advance_log."',
                                                    credit = credit + $recovery->amount,
                                                    credit_tax = credit_tax + $creditTax,
                                                    overdue = '".$charges_overdue_log."',
                                                    advance = '".$charges_advance_log."',
                                                    charges_credit = charges_credit + $recovery->charges_amount where id = '".$recovery->schedule_id."'")->execute();
            $connection->createCommand("UPDATE schedules SET due_amnt = '".$due."',charges_due_amount = '".$charges_due."' where loan_id = '".$recovery->loan_id."' and due_date = '".$next_date."'")->execute();
            if($balance <= 0){
                $connection->createCommand("UPDATE loans SET 
                                            due = '".$due."', 
                                            overdue = '".$overdue_log."', 
                                            balance = '".$balance."' ,
                                            charges_due = '".$charges_due."', 
                                            charges_overdue = '".$charges_overdue_log."', 
                                            charges_balance = '".$charges_balance."',
                                            loan_completed_date = '".$recovery->receive_date."',
                                            status = 'loan completed' where id = '".$recovery->loan_id."'")->execute();
            }
            else{
                $connection->createCommand("UPDATE loans SET 
                                            due = '".$due."', 
                                            overdue = '".$overdue_log."', 
                                            balance = '".$balance."',
                                            charges_due = '".$charges_due."', 
                                            charges_overdue = '".$charges_overdue_log."', 
                                            charges_balance = '".$charges_balance."' where id = '".$recovery->loan_id."'")->execute();
            }
        }
    }
    public static function  GetScheduleInfo($loan,$recv_date){

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

        if($recv_date >= $min_due_date && $recv_date <= $max_due_date){
            foreach($schedules as $s){
                if(date('Y-m', ($s->due_date)) == date('Y-m',($recv_date))){
                    $schedule_info = $s;
                }
            }
        }else if($recv_date < $min_due_date){
            $schedule_info = $first_schedule;
        }else if($recv_date > $max_due_date){
            $schedule_info = $last_schedule;
        }
        return $schedule_info->id;
    }
    public static function adjust_donation($loan)
    {
        $donations=Donations::find()->where(['loan_id'=>$loan->id,'deleted'=>'0'])->all();
        foreach($donations as $donation){
            $connection = \Yii::$app->db;
            //$d=new Donations();
            $schedule_info = FixesHelper::GetScheduleInfo($loan,$donation->receive_date);
            ///update donation
            $connection->createCommand("UPDATE donations SET 
                                                    schedule_id = '".$schedule_info."'
                                                     where id = '".$donation->id."'")->execute();
        }
    }
    public static function update_loan_expiry($loan)
    {
        $schedule=Schedules::find()->select('due_date')->where(['loan_id'=>$loan->id])->orderBy(['id'=>SORT_DESC])->one();
        $expiry_date=strtotime(date("Y-m-t", ($schedule->due_date)));
            $connection = \Yii::$app->db;
            $connection->createCommand("UPDATE loans SET 
                                                    loan_expiry = '".$expiry_date."'
                                                     where id = '".$loan->id."'")->execute();
    }
    public static function ledger_regenerate($loan)
    {
        //delete all existing schedules
        $connection = \Yii::$app->db;
        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
        $connection->createCommand($schdl_delete)->execute();
        //generate new schedules
        DisbursementHelper::GenerateSchedule($loan,true);
        ///update loan expiry
        FixesHelper::update_loan_expiry($loan);
        //adjust recovery
        FixesHelper::adjust_recovery($loan);
        //adjust donation
        FixesHelper::adjust_donation($loan);
    }

    public static function ledger_regenerate_closing_loan($loan)
    {
        //delete all existing schedules
        $connection = \Yii::$app->db;
        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
        $connection->createCommand($schdl_delete)->execute();
        //generate new schedules
        DisbursementHelper::GenerateSchedule($loan,false,true);
        ///update loan expiry
        FixesHelper::update_loan_expiry($loan);
        //adjust recovery
        FixesHelper::adjust_recovery($loan);
        //adjust donation
        FixesHelper::adjust_donation($loan);
    }

    public static function ledger_regenerate_extra($loan)
    {
        $date = date("Y-m-t 00:00:00");
        $ledger = Schedules::find()->where(['loan_id'=>$loan->id])
            ->andWhere(['!=', 'credit', 0])
            ->andWhere(['<', 'due_date', strtotime($date)])->all();
        foreach ($ledger as $data) {
            $preScheduleNew = new SchedulesHousingPre();
            $preScheduleNew->setAttributes($data->attributes);
            $preScheduleNew->save();
        }

        //delete all existing schedules
        $connection = \Yii::$app->db;
        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "' and credit = 0 and due_date >  '" . $date . "'";
        $sql = "UPDATE schedules SET deleted = 1  where loan_id = '" . $loan->id . "' and credit = 0 ";
        $connection->createCommand($sql)->execute();
        $connection->createCommand($schdl_delete)->execute();

        //generate new schedules
        $previous_schdl = Schedules::find()->select('sum(credit) as credit_sum,sum(charges_credit) as charges_sum, count(id) as schdl_count')->where(['loan_id'=>$loan->id])->andWhere(['!=', 'credit', 0])->andWhere(['<', 'due_date', strtotime($date)])->asArray()->all();

        $loan_tranche = LoanTranches::find()->where(['loan_id' => $loan->id,'status' => 6])->all();
        foreach ($loan_tranche as $data){
           // $disbursements = DisbursementDetails::find()->where(['tranche_id' => $data->id])->one();
            //foreach ($disbursements as $d) {
                DisbursementHelper::GenerateScheduleHousingExtraCase($loan, $previous_schdl,$data->tranch_amount,true);
           // }
        }
        ///update loan expiry
        FixesHelper::update_loan_expiry($loan);
        //adjust recovery
        FixesHelper::adjust_recovery($loan);
        //adjust donation
        FixesHelper::adjust_donation($loan);
        if($loan->project_id == 132){
            $status = 'Loan Disbursed';
            $statusReason = 'Loan Disbursed';
            AcagHelper::actionPush($loan->application,$status,$statusReason,$loan->disbursed_amount,date('Y-m-d'),0,$loan);
        }
    }

    public static function ledger_regenerate_housing($loan)
    {
        //delete all existing schedules
        $connection = \Yii::$app->db;
        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
        $connection->createCommand($schdl_delete)->execute();
        //generate new schedules
        DisbursementHelper::GenerateScheduleHousingDueDateFix($loan,true);
        ///update loan expiry
        FixesHelper::update_loan_expiry($loan);
        //adjust recovery
        FixesHelper::adjust_recovery($loan);
        //adjust donation
        FixesHelper::adjust_donation($loan);
        if($loan->project_id == 132){
            $status = 'Loan Disbursed';
            $statusReason = 'Loan Disbursed';
            AcagHelper::actionPush($loan->application,$status,$statusReason,$loan->disbursed_amount,date('Y-m-d'),0,$loan);
        }
    }
    public static function ledger_regenerate_housing_rupee_diff($loan)
    {
        //delete all existing schedules
        $connection = \Yii::$app->db;
        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
        $connection->createCommand($schdl_delete)->execute();
        //generate new schedules
        DisbursementHelper::GenerateScheduleHousingDueDateFixRupeeDiff($loan,true);
        ///update loan expiry
        FixesHelper::update_loan_expiry($loan);
        //adjust recovery
        FixesHelper::adjust_recovery($loan);
        //adjust donation
        FixesHelper::adjust_donation($loan);
        if($loan->project_id == 132){
            $status = 'Loan Disbursed';
            $statusReason = 'Loan Disbursed';
            AcagHelper::actionPush($loan->application,$status,$statusReason,$loan->disbursed_amount,date('Y-m-d'),0,$loan);
        }
    }
    public static function ledger_regenerate_old($loan)
    {
        //delete all existing schedules
        $connection = \Yii::$app->db;
        $schdl_delete = "SET FOREIGN_KEY_CHECKS=0;delete from schedules where loan_id = '" . $loan->id . "'";
        $connection->createCommand($schdl_delete)->execute();
        //generate new schedules
        DisbursementHelper::GenerateScheduleOld($loan,true);
        ///update loan expiry
        FixesHelper::update_loan_expiry($loan);
        //adjust recovery
        FixesHelper::adjust_recovery($loan);
        //adjust donation
        FixesHelper::adjust_donation($loan);
    }
    public static function fix_schedules_update_deffer($loan)
    {
        $connection = \Yii::$app->db;

        //clear all schedules
        $sql = "UPDATE schedules SET due_amnt=0,  overdue_log = 0, credit = 0, advance_log = 0, charges_due_amount = 0 ,overdue = 0, advance= 0 ,charges_credit = 0 where loan_id = '" . $loan->id . "'";
        $connection->createCommand($sql)->execute();
        $recoveries = Recoveries::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->orderBy('receive_date asc')->all();

        foreach ($recoveries as $r) {
            $schdl = Schedules::findOne(['id' => $r->schedule_id]);
            if (!empty($schdl)) {
                $schedule = $schdl;
            } else {
                die('Schedule not found with recovery id : ' . $r['R']['id']);
            }
            $connection->createCommand("UPDATE schedules SET credit = credit + '" . $r->amount . "', charges_credit = charges_credit + '" . $r->charges_amount . "' where id = '" . $r->schedule_id . "'")->execute();

        }

        $due_date = strtotime('+9 days', strtotime('first day of this month'));

        //select all schedules and update due, overdue and advance amount of all schedules
        $schedules = Schedules::find()->where(['loan_id' => $loan->id])->andWhere(['<=', 'due_date', $due_date])->orderBy('due_date asc')->all();

        if(empty($schedules)){
            $schd = Schedules::find()->where(['loan_id' => $loan->id])->orderBy('due_date asc')->one();
            $schedules[]=$schd;

        }
        $total_credit = $due = $overdue_log = $advance_log = 0;

        foreach ($schedules as $key => $s) {
            $charges_total_credit = $charges_due = $charges_overdue_log = $charges_advance_log = 0;
            //echo '<h2>'.$key.'</h2>';
            $last_day_of_month = strtotime(date('Y-m-t', $s->due_date));
            $overdue_query = "select (sum(schdl_amnt) - (select COALESCE(sum(amount),0) from recoveries where loan_id = '" . $s->loan_id . "' and receive_date <= '" . $last_day_of_month . "' and deleted = 0)) as overdue_advance, (sum(charges_schdl_amount) - (select COALESCE(sum(charges_amount),0) from recoveries where loan_id = '" . $s->loan_id . "' and receive_date <= '" . $last_day_of_month . "' and deleted = 0)) as charges_overdue_advance from schedules where loan_id = '" . $loan->id . "' and due_date <= '" . $s->due_date . "'";

            $overdue_advance = $connection->createCommand($overdue_query)->queryAll();

            $next_date = strtotime("+1 month", $s->due_date);
            //print_r($s->due_date);

            //if($next_date <= $due_date){,
            if ($overdue_advance[0]['overdue_advance'] > 0) {
                $overdue_log = abs($overdue_advance[0]['overdue_advance']);
            } else {
                $advance_log = abs($overdue_advance[0]['overdue_advance']);
            }

            if ($overdue_advance[0]['charges_overdue_advance'] > 0) {
                $charges_overdue_log = abs($overdue_advance[0]['charges_overdue_advance']);
            } else {
                $charges_advance_log = abs($overdue_advance[0]['charges_overdue_advance']);
            }

            // update schedule.overdue_log& schedule.advance_log
            $sql = "UPDATE schedules SET overdue_log  = overdue_log + '" . $overdue_log . "', advance_log = advance_log+'" . $advance_log . "',
                                         overdue = '" . $charges_overdue_log . "', advance = advance +'" . $charges_advance_log . "' where id = '" . $s->id . "' ";
            $connection->createCommand($sql)->execute();

            // update schedule.due_amnt
            if ($key == 0) {
                $due = $s->schdl_amnt;
                $charges_due = $s->charges_schdl_amount;
                $update_due = "UPDATE schedules SET due_amnt = '" . $due . "', charges_due_amount = '" . $charges_due . "' where loan_id = '" . $loan->id . "' and due_date = '" . $s->due_date . "' ";
                $connection->createCommand($update_due)->execute();
            } else {
                $next=Schedules::find()->where(['loan_id'=>$loan->id,'due_date'=>$next_date])->one();
                $due = ($next->schdl_amnt - $advance_log) + $overdue_log;
                $charges_due = ($s->charges_schdl_amount - $charges_advance_log) + $charges_overdue_log;
                $update_due = "UPDATE schedules SET due_amnt = '" . $due . "', charges_due_amount = '" . $charges_due . "' where loan_id = '" . $loan->id . "' and due_date = '" . $next_date . "' ";
                $connection->createCommand($update_due)->execute();
            }

            //}

            $total_credit += $s->credit;
            $charges_total_credit += $s->charges_credit;
        }

        //update balance, due and advance in loans table
        $total_credit = Recoveries::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->sum('amount');
        $charges_total_credit = Recoveries::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->sum('charges_amount');

        $last_recovery = array();
        if (!empty($recoveries)) {
            $last_recovery = end($recoveries);
        }
        $loan->due = $due;
        $loan->overdue = $overdue_log;
        $loan->balance = $loan->disbursed_amount - $total_credit;
        $loan->charges_due = $charges_due;
        $loan->charges_overdue = $charges_overdue_log;
        $loan->charges_balance = $loan->service_charges - $charges_total_credit;
        $loan->status = 'collected';
        $loan->loan_completed_date = 0;
        if ($loan->balance == 0) {
            $loan->status = 'loan completed';
            $loan->loan_completed_date = $last_recovery->receive_date;
        }
        $connection = \Yii::$app->db;
        $connection->createCommand("UPDATE loans SET 
                                            due = '".$due."'
                                           , overdue = '".$overdue_log."'
                                           , balance = '".$loan->balance."'
                                           , charges_due = '".$charges_due."'
                                           , charges_overdue = '".$charges_overdue_log."'
                                           , charges_balance = '".$loan->charges_balance."'
                                           , status = '".$loan->status."'
                                           , loan_completed_date = '".$loan->loan_completed_date."'
                                             where id = '".$loan->id."'")->execute();

        /*if ($loan->save()) {
            return 1;
        } else {
            echo 'Schedule updated, Loan due overdue not updated' . '<br>';
        }*/
        //die('here');
    }

    public static function addRecoveryForFund($recovery_id){
        $projects = [77,78,79];
        $model = Recoveries::find()->where(['id',$recovery_id])->one();
        if(in_array($model->project_id,$projects)){
            $loanTranches = LoanTranches::find()->where(['loan_id'=>$model->loan_id])->all();
            if(count($loanTranches) == 1){
                $batch = ProjectFundDetail::find()->where(['id'=>$loanTranches[0]->batch_id])->all();
                if(!empty($batch) && $batch!=null){
                    $fund = Funds::find()->where(['id'=>$batch->fund_id])->one();
                    if(!empty($fund) && $fund!=null){
                        $fund->recovery = $fund->recovery+$model->amount;
                        if(!$fund->save()){
                            var_dump($fund->getErrors());
                            die();
                        }
                    }
                }
            }else{
                $batch = ProjectFundDetail::find()->where(['id'=>$loanTranches[1]->batch_id])->all();
                if(!empty($batch) && $batch!=null){
                    $fund = Funds::find()->where(['id'=>$batch->fund_id])->one();
                    if(!empty($fund) && $fund!=null){
                        $fund->recovery = $fund->recovery+$model->amount;
                        if(!$fund->save()){
                            var_dump($fund->getErrors());
                            die();
                        }
                    }
                }
            }
        }
        return true;
    }

    public static function updateRecoveryForFund($recovery_id,$oldRecoveryAmount){
        $projects = [77,78,79];
        $model = Recoveries::find()->where(['id',$recovery_id])->one();
        if(in_array($model->project_id,$projects)){
            $loanTranches = LoanTranches::find()->where(['loan_id'=>$model->loan_id])->all();
            if(count($loanTranches) == 1){
                $batch = ProjectFundDetail::find()->where(['id'=>$loanTranches[0]->batch_id])->all();
                if(!empty($batch) && $batch!=null){
                    $fund = Funds::find()->where(['id'=>$batch->fund_id])->one();
                    if(!empty($fund) && $fund!=null){
                        $amountUpdate = $fund->recovery-$oldRecoveryAmount;
                        $fund->recovery = $amountUpdate+$model->amount;
                        if(!$fund->save()){
                            var_dump($fund->getErrors());
                            die();
                        }
                    }
                }
            }else{
                $batch = ProjectFundDetail::find()->where(['id'=>$loanTranches[1]->batch_id])->all();
                if(!empty($batch) && $batch!=null){
                    $fund = Funds::find()->where(['id'=>$batch->fund_id])->one();
                    if(!empty($fund) && $fund!=null){
                        $amountUpdate = $fund->recovery-$oldRecoveryAmount;
                        $fund->recovery = $amountUpdate+$model->amount;
                        if(!$fund->save()){
                            var_dump($fund->getErrors());
                            die();
                        }
                    }
                }
            }
        }
        return true;
    }

    public static function deleteRecoveryForFund($recovery_id){
        $projects = [77,78,79];
        $model = Recoveries::find()->where(['id',$recovery_id])->one();
        if(in_array($model->project_id,$projects)){
            $loanTranches = LoanTranches::find()->where(['loan_id'=>$model->loan_id])->all();
            if(count($loanTranches) == 1){
                $batch = ProjectFundDetail::find()->where(['id'=>$loanTranches[0]->batch_id])->all();
                if(!empty($batch) && $batch!=null){
                    $fund = Funds::find()->where(['id'=>$batch->fund_id])->one();
                    if(!empty($fund) && $fund!=null){
                        $fund->recovery = $fund->recovery-$model->amount;
                        if(!$fund->save()){
                            var_dump($fund->getErrors());
                            die();
                        }
                    }
                }
            }else{
                $batch = ProjectFundDetail::find()->where(['id'=>$loanTranches[1]->batch_id])->all();
                if(!empty($batch) && $batch!=null){
                    $fund = Funds::find()->where(['id'=>$batch->fund_id])->one();
                    if(!empty($fund) && $fund!=null){
                        $fund->recovery = $fund->recovery-$model->amount;
                        if(!$fund->save()){
                            var_dump($fund->getErrors());
                            die();
                        }
                    }
                }
            }
        }
        return true;
    }

    public static function LedgerGeneratesExtra($loan_id)
    {
        $loan_tranche = LoanTranches::find()->where(['loan_id' => $loan_id])->andWhere(['!=', 'date_disbursed', 0])
            ->andWhere(['deleted' => 0])->sum('tranch_amount');
        $ledger = Schedules::find()->where(['loan_id' => $loan_id])
            ->all();

        SchedulesHousingPre::deleteAll(['loan_id' => $loan_id]);
        foreach ($ledger as $d) {
            if(($d->credit !=0) || ($d->charges_credit !=0)){
                $preScheduleNew = new SchedulesHousingPre();
                $preScheduleNew->setAttributes($d->attributes);
                $preScheduleNew->save();
            }else{
                $checkDate = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", $d->due_date))));
                $CheckLedger = Schedules::find()
                    ->where(['loan_id' => $loan_id])
                    ->andWhere(['due_date' => strtotime($checkDate)])
                    ->one();
                if(!empty($CheckLedger) && $CheckLedger!=null && ($CheckLedger->credit !=0) || ($CheckLedger->charges_credit !=0)){
                    $preScheduleNew = new SchedulesHousingPre();
                    $preScheduleNew->setAttributes($d->attributes);
                    $preScheduleNew->save();
                }
            }
        }

        Schedules::deleteAll(['loan_id' => $loan_id]);
        $tranches_updated = LoanTranches::find()->where(['loan_id' => $loan_id])->andWhere(['!=', 'date_disbursed', 0])->andWhere(['deleted' => 0])->all();
        foreach ($tranches_updated as $tranche) {
            $tranche->status = 6;
            $tranche->updated_by = 1;
            $tranche->save();
            $d_tranches = DisbursementDetails::find()->where(['tranche_id' => $tranche->id])->one();
            if (!empty($d_tranches) && $d_tranches != null) {
                $d_tranches->status = 3;
                $d_tranches->save();
            }
        }
        $loan = Loans::find()->where(['id' => $loan_id])->one();
        $loan->status = 'collected';
        $loan->disbursed_amount = $loan_tranche;
        $loan->updated_by = 1;
        if ($loan->save()) {
            $date = date("Y-m-t 00:00:00");
            DisbursementHelper::GenerateScheduleHousingExtraTwo($loan, true);
            $ledgerpre = SchedulesHousingPre::find()->where(['loan_id' => $loan->id])->all();
            foreach ($ledgerpre as $data) {
                $ledgerold = Schedules::find()->where(['loan_id' => $loan->id])->andWhere(['due_date' => $data->due_date])->one();
                if ($ledgerold) {
                    $ledgerold->schdl_amnt = $data->schdl_amnt;
                    $ledgerold->charges_schdl_amount = $data->charges_schdl_amount;
                    $ledgerold->charges_schdl_amnt_tax = $data->charges_schdl_amnt_tax;
                    $ledgerold->charges_due_amount = $data->charges_due_amount;
                    $ledgerold->charges_credit = $data->charges_credit;
                    $ledgerold->overdue_log = $data->overdue_log;
//                    $ledgerold->advance_log = $data->advance_log;
                    $ledgerold->credit_tax = $data->credit_tax;
                    $ledgerold->overdue = $data->overdue;
                    $ledgerold->advance = $data->advance;
                    $ledgerold->due_amnt = $data->due_amnt;
//                    $ledgerold->credit = $data->credit;
                    if ($ledgerold->save()) {
                        $sync_rec = Recoveries::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['due_date' => $data->due_date])->andWhere(['deleted' => 0])->all();
                        foreach ($sync_rec as $rec) {
                            $rec->schedule_id = $ledgerold->id;
                            $rec->save(false);
                        }

                    }
                }

            }

            $last_recovery = Recoveries::find()->where(['loan_id' => $loan_id])->andWhere(['deleted' => 0])->orderBy(['due_date' => SORT_DESC])->one();
            $preLedger = Schedules::find()->where(['loan_id' => $loan_id])->andWhere(['>','due_date' , $last_recovery->due_date])->all();
            $loanModel = Loans::find()->where(['id'=>$loan_id])->one();
            $ledgerCount = count($preLedger);
            $balance = $loanModel->balance;
            $chargesPercent = 5;
            $newFixRentInit = ((($balance*$chargesPercent)/100)/12);
            $newFixRentTotal = round($newFixRentInit*$ledgerCount);
            $newFixRent = round($newFixRentTotal/$ledgerCount);
            $lastScheduleRentDifference = $newFixRentTotal-($newFixRent*($ledgerCount-1));

            foreach ($preLedger as $ledger){
                $ledger->charges_schdl_amount = $newFixRent;
                $ledger->save(false);
            }

            $preLedgerLast = Schedules::find()->where(['loan_id' => $loan_id])->orderBy(['due_date' => SORT_DESC])->one();
            if ($preLedgerLast){
                $preLedgerLast->charges_schdl_amount = $lastScheduleRentDifference;
                $preLedgerLast->save(false);
            }
            if($loan->project_id == 132){
                $status = 'Loan Disbursed';
                $statusReason = 'Loan Disbursed';
                AcagHelper::actionPush($loan->application,$status,$statusReason,$loan->disbursed_amount,date('Y-m-d'),0,$loan);
            }
        }

    }
}