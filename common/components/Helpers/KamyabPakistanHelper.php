<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;

use common\models\Blacklist;
use common\models\DisbursementDetails;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Recoveries;
use common\models\Schedules;
use common\models\Users;
use common\models\BranchProjectsMapping;
use Yii;
use DateTime;

class KamyabPakistanHelper
{

    public static function kppLedgerGenerate($loan)
    {
        $schedules = self::KppHousingLedger($loan);
        if (!empty($schedules)) {
            foreach ($schedules as $schedule) {
                $modelSchedules = new Schedules();
                $modelSchedules->application_id = $loan->application_id;
                $modelSchedules->loan_id = $loan->id;
                $modelSchedules->branch_id = $loan->branch_id;
                $modelSchedules->due_date = strtotime($schedule['due_date']);
                $modelSchedules->schdl_amnt = $schedule['p_baseRent'];
                $modelSchedules->charges_schdl_amount = $schedule['p_fixedRent'];
                $modelSchedules->due_amnt = $schedule['due_amount'];
                $modelSchedules->assigned_to = '1';
                $modelSchedules->created_by = '1';
                if ($modelSchedules->save()) {

                } else {
                    var_dump($modelSchedules->getErrors());
                    die();
                }
            }
        }
    }

    public static function ledgerReGeneratesKppSingle($loan)
    {
        if (!empty($loan) && $loan != null) {
            $loan_tranche = LoanTranches::find()
                ->where(['loan_id' => $loan->id])
                ->andWhere(['!=', 'date_disbursed', 0])
                ->andWhere(['status' => 6])
                ->andWhere(['deleted' => 0])
                ->sum('tranch_amount');

            Schedules::deleteAll(['loan_id' => $loan->id]);
//            $tranches_updated = LoanTranches::find()
//                ->where(['loan_id' => $loan->id])
//                ->andWhere(['!=', 'date_disbursed', 0])
//                ->andWhere(['deleted' => 0])
//                ->all();
//
//            foreach ($tranches_updated as $tranche) {
//                $tranche->status = 6;
//                $tranche->updated_by = 1;
//                $tranche->save();
//                $d_tranches = DisbursementDetails::find()->where(['tranche_id' => $tranche->id])->one();
//                if (!empty($d_tranches) && $d_tranches != null) {
//                    $d_tranches->status = 3;
//                    $d_tranches->save();
//                }
//            }

            $loan = Loans::find()->where(['id' => $loan->id])->one();
            $loan->status = 'collected';
            $loan->disbursed_amount = $loan_tranche;
            $loan->updated_by = 1;
            if ($loan->save()) {
                self::KppHousingLedgerReGenerate($loan);
                ///update loan expiry
                FixesHelper::update_loan_expiry($loan);
                //adjust donation
                FixesHelper::adjust_donation($loan);
            }
        }
    }

    public static function KppHousingLedgerReGenerate($loan)
    {
        $resultArray = [];
        $rentRate5Yrs = 0.02;
        $rentRate10Yrs = 0.04;
        $rentRate15Yrs = 0.05;
        $rentRate20Yrs = 0.1158;

        $disburse_date = date('Y-m-d', $loan->date_disbursed);
        if ($loan->date_disbursed == 0) {
            $disburse_date = date('Y-m-d', $loan->readyTranche->cheque_date);
        }
        $initDate = date("Y-M-10", strtotime($disburse_date));
        $installment_date = strtotime(date("Y-M-01", strtotime("+" . 2 . " months", strtotime($initDate))));

        $disbursed_amount = LoanTranches::find()
            ->where(['loan_id' => $loan->id])
            ->andWhere(['<', 'date_disbursed', $installment_date])
            ->andWhere(['!=', 'date_disbursed', 0])
            ->andWhere(['status' => 6])
            ->sum('tranch_amount');

        $total_months = $loan->inst_months;
        $p_amount = $disbursed_amount;
        $p_months = $loan->inst_months;
        $p_rentRate = 0;

        for ($i = 1; $i <= $total_months; $i++) {
            switch (ceil($i / 60)) {
                case 1:
                    $p_rentRate = $rentRate5Yrs;
                    break;
                case 2:
                    $p_rentRate = $rentRate10Yrs;
                    break;
                case 3:
                    $p_rentRate = $rentRate15Yrs;
                    break;
                case 4:
                    $p_rentRate = $rentRate20Yrs;
                    break;
                default:
                    //System.out.println("None matched");
            }

            $p_monthlyRent = self::calculatePMT($p_amount, $p_rentRate, $p_months);
            $p_fixedRent = self::calculateFixedRent($p_amount, $p_rentRate);
            $p_baseRent = self::calculateBaseRent($p_monthlyRent['result'], $p_fixedRent);

            $month = $i + 1;
            $due_date = date("Y-m-10", strtotime($disburse_date));
            $inst_date = date("Y-m-10", strtotime("+" . $month . " months", strtotime($due_date)));

            $fromDate = strtotime(date("Y-M-01", strtotime($inst_date)));
            $toDate = strtotime(date("Y-M-t", strtotime($inst_date)));

            $AdditionalTranches = LoanTranches::find()
                ->where(['loan_id' => $loan->id])
                ->andWhere(['>=', 'date_disbursed', $fromDate])
                ->andWhere(['<=', 'date_disbursed', $toDate])
                ->andWhere(['!=', 'date_disbursed', 0])
                ->andWhere(['status' => 6])
                ->sum('tranch_amount');

            if (!empty($AdditionalTranches) && $AdditionalTranches != null) {
                $p_amount += $AdditionalTranches;
            }



            $fromDate_pre = date("Y-m-01", strtotime('-1 month', strtotime($inst_date)));
            $toDate_pre = date("Y-m-t", strtotime('-1 month', strtotime($inst_date)));

            $recoveryArray_pre = Recoveries::find()
                ->where(['loan_id' => $loan->id])
                ->andWhere(['>=', 'receive_date', strtotime($fromDate_pre)])
                ->andWhere(['<=', 'receive_date', strtotime($toDate_pre)])
                ->andWhere(['deleted' => 0])
                ->count();


            $recoveryArray = Recoveries::find()
                ->where(['loan_id' => $loan->id])
                ->andWhere(['>=', 'receive_date', $fromDate])
                ->andWhere(['<=', 'receive_date', $toDate])
                ->andWhere(['deleted' => 0])
                ->all();
            $advanceLog = 0;
            if (!empty($recoveryArray) && $recoveryArray != null) {
                $recoveryBaseRent = 0;
                $recoveryChargesAmount = 0;
                foreach ($recoveryArray as $recovery) {
                    $recoveryBaseRent += $recovery->amount;
                    $recoveryChargesAmount += $recovery->charges_amount;
                }

                $totalRecovery = $recoveryBaseRent + $recoveryChargesAmount;
                $advanceLog = ($totalRecovery - $p_monthlyRent['result']);
                $p_baseRent  = $p_monthlyRent['result']-$p_fixedRent;
                $p_amount = ($p_amount - $recoveryBaseRent);
            } else {
                if ($recoveryArray_pre>0) {
                    $p_amount = ($p_amount - $p_baseRent);
                }
            }


            $p_months = $total_months - $i;

            $resultArray[$i]['due_date'] = $inst_date;
            $resultArray[$i]['due_amount'] = $p_monthlyRent['result'];
            $resultArray[$i]['p_fixedRent'] = $p_fixedRent;
            $resultArray[$i]['p_baseRent'] = $p_baseRent;
            $resultArray[$i]['advanceLog'] = $advanceLog;
            $resultArray[$i]['fromDate_pre'] = strtotime($fromDate_pre);
            $resultArray[$i]['toDate_pre'] = strtotime($toDate_pre);
            $resultArray[$i]['pre_data'] = $recoveryArray_pre;
        }

        if (!empty($resultArray)) {
//            echo '<pre>';
//            print_r($resultArray);
//            die();
            foreach ($resultArray as $schedule) {
                $fromDate = strtotime(date("Y-M-01", strtotime($schedule['due_date'])));
                $toDate = strtotime(date("Y-M-t", strtotime($schedule['due_date'])));
                $recoveryData = Recoveries::find()
                    ->where(['loan_id' => $loan->id])
                    ->andWhere(['>=', 'due_date', $fromDate])
                    ->andWhere(['<=', 'due_date', $toDate])
                    ->andWhere(['deleted' => 0])
                    ->all();

                $recoveryBaseRent = 0;
                $recoveryChargesAmount = 0;

                if ($recoveryData) {
                    foreach ($recoveryData as $recovery) {
                        $recoveryBaseRent = $recoveryBaseRent + $recovery->amount;
                        $recoveryChargesAmount = $recoveryChargesAmount + $recovery->charges_amount;
                    }

                }

                $modelSchedules = new Schedules();
                $modelSchedules->application_id = $loan->application_id;
                $modelSchedules->loan_id = $loan->id;
                $modelSchedules->branch_id = $loan->branch_id;
                $modelSchedules->due_date = strtotime($schedule['due_date']);
                $modelSchedules->schdl_amnt = $schedule['p_baseRent'];
                $modelSchedules->charges_schdl_amount = $schedule['p_fixedRent'];
                $modelSchedules->due_amnt = $schedule['due_amount'];
                $modelSchedules->advance_log = $schedule['advanceLog'];
//                $modelSchedules->credit = $recoveryBaseRent;
//                $modelSchedules->charges_credit = $recoveryChargesAmount;
                $modelSchedules->assigned_to = 1;
                if ($modelSchedules->save()) {
                    if ($recoveryData) {
                        foreach ($recoveryData as $rec) {
                            $rec->schedule_id = $modelSchedules->id;
                            $rec->save(false);
                        }
                    }
                }
            }


            $date_disburse = date('Y-m-d', $loan->date_disbursed);
//            if ($date_disburse < date("2024-02-01")){
//
//                $chargesScheduleAmount =  Schedules::find()
//                    ->where(['loan_id' => $loan->id])
//                    ->sum('charges_schdl_amount');
//
//                $scheduleCount =  Schedules::find()
//                    ->where(['loan_id' => $loan->id])
//                    ->andWhere(['>','due_date' , strtotime(date("2024-03-10"))])
//                    ->count('id');
//                $gst_percentage = $loan->branch->province->gst;
//                $taxPrcAmount = ($chargesScheduleAmount/$scheduleCount)*$gst_percentage/100;
//                $scheduleModel =  Schedules::find()
//                    ->where(['loan_id' => $loan->id])
//                    ->andWhere(['>','due_date' , strtotime(date("2024-03-10"))])
//                    ->all();
//                foreach ($scheduleModel as $model){
//                    $model->charges_schdl_amnt_tax = ceil($taxPrcAmount);
//                    $model->save();
//                }
//            }else{
//                $scheduleModel =  Schedules::find()
//                    ->where(['loan_id' => $loan->id])
//                    ->all();
//                $gst_percentage = $loan->branch->province->gst;
//                foreach ($scheduleModel as $model){
//                    $charges_schdl_amount_tax = $model->charges_schdl_amount * ($gst_percentage / 100);
//                    $model->charges_schdl_amnt_tax = ceil($charges_schdl_amount_tax);
//                    $model->save();
//                }
//            }
        }
        return true;

    }

    public static function KppHousingLedger($loan)
    {
        $tranches = LoanTranches::find()
            ->where(['loan_id' => $loan->id])
            ->andWhere(['>', 'date_disbursed', 0])
            ->select(['date_disbursed', 'tranch_amount', 'tranch_no'])
            ->all();

        $resultArray = [];
        $rentRate5Yrs = 0.02;
        $rentRate10Yrs = 0.04;
        $rentRate15Yrs = 0.05;
        $rentRate20Yrs = 0.1158;
        $trancheCount = '';
        $disburse_date = date('Y-m-d', $loan->date_disbursed);
        if ($loan->date_disbursed == 0) {
            $disburse_date = date('Y-m-d', $loan->readyTranche->cheque_date);
        }

        $p_amount = $loan->disbursed_amount;
        $total_months = $loan->inst_months;
        $p_months = $loan->inst_months;
        $due_date = date("Y-m-10", strtotime($disburse_date));
        $p_rentRate = 0;

        for ($i = 1; $i <= $total_months; $i++) {
            switch (ceil($i / 60)) {
                case 1:
                    $p_rentRate = $rentRate5Yrs;
                    break;
                case 2:
                    $p_rentRate = $rentRate10Yrs;
                    break;
                case 3:
                    $p_rentRate = $rentRate15Yrs;
                    break;
                case 4:
                    $p_rentRate = $rentRate20Yrs;
                    break;
                default:
                    //System.out.println("None matched");
            }

            $p_monthlyRent = self::calculatePMT($p_amount, $p_rentRate, $p_months);
            $p_fixedRent = self::calculateFixedRent($p_amount, $p_rentRate);
            $p_baseRent = self::calculateBaseRent($p_monthlyRent['result'], $p_fixedRent);

            $due_date_first_inst = date("Y-M-10", strtotime("+" . 2 . " months", strtotime($due_date)));
            $set_due_date = date("Y-M-10", strtotime("+" . 1 . " months", strtotime($due_date)));
            $inst_due_date = date("Y-M-10", strtotime("+" . $i . " months", strtotime($set_due_date)));

            foreach ($tranches as $tranche) {
                $fromDate = date('Y-m-d H:i:s ', $tranche->date_disbursed);
                $toDate = date('Y-m-d H:i:s ', $disburse_date);
                $ts1 = new DateTime($fromDate);
                $ts2 = new DateTime($toDate);
                $interval = $ts1->diff($ts2);
                $monthNo = $interval->m;
                $tranche_amount = $tranche->tranch_amount;

                if ($due_date_first_inst < $fromDate) {
                    if ($monthNo == $i) {
                        $p_amount += $tranche_amount;
                        $trancheCount = $trancheCount . ',' . $tranche->tranch_no;
                    }
                }
            }

            $p_amount = ($p_amount - $p_baseRent);
            $p_months = $total_months - $i;

            $resultArray[$i]['due_date_first_inst'] = $due_date_first_inst;
            $resultArray[$i]['due_date'] = $inst_due_date;
            $resultArray[$i]['rate'] = $p_monthlyRent['rate'];
            $resultArray[$i]['term'] = $p_monthlyRent['term'];
            $resultArray[$i]['due_amount'] = $p_monthlyRent['result'];
            $resultArray[$i]['p_fixedRent'] = $p_fixedRent;
            $resultArray[$i]['p_baseRent'] = $p_baseRent;
            $resultArray[$i]['olp'] = $p_amount;
            $resultArray[$i]['trancheCount'] = $trancheCount;
        }

        return $resultArray;

    }

    public static function tentativeLedger($loan)
    {

        $tranches = LoanTranches::find()
            ->where(['loan_id' => $loan->id])
            ->andWhere(['>', 'date_disbursed', 0])
            ->select(['date_disbursed', 'tranch_amount', 'tranch_no'])
            ->all();

        $resultArray = [];
        $rentRate5Yrs = 0.02;
        $rentRate10Yrs = 0.04;
        $rentRate15Yrs = 0.05;
        $rentRate20Yrs = 0.1158;
        $trancheCount = '';

        $disburse_date = date('Y-m-d', $loan->date_disbursed);
        if ($loan->date_disbursed == 0) {
            $disburse_date = date('Y-m-d', $loan->readyTranche->cheque_date);
        }

        $p_amount = $loan->disbursed_amount;
        $total_months = $loan->inst_months;
        $p_months = $loan->inst_months;
        $due_date = date("Y-m-10", strtotime($disburse_date));
        $p_rentRate = 0;

        for ($i = 1; $i <= $total_months; $i++) {
            switch (ceil($i / 60)) {
                case 1:
                    $p_rentRate = $rentRate5Yrs;
                    break;
                case 2:
                    $p_rentRate = $rentRate10Yrs;
                    break;
                case 3:
                    $p_rentRate = $rentRate15Yrs;
                    break;
                case 4:
                    $p_rentRate = $rentRate20Yrs;
                    break;
                default:
                    //System.out.println("None matched");
            }

            $p_monthlyRent = self::calculatePMT($p_amount, $p_rentRate, $p_months);
            $p_fixedRent = self::calculateFixedRent($p_amount, $p_rentRate);
            $p_baseRent = self::calculateBaseRent($p_monthlyRent['result'], $p_fixedRent);

            foreach ($tranches as $tranche) {
                $fromDate = date('Y-m-d H:i:s ', $tranche->date_disbursed);
                $toDate = date('Y-m-d H:i:s ', $disburse_date);
                $ts1 = new DateTime($fromDate);
                $ts2 = new DateTime($toDate);
                $interval = $ts1->diff($ts2);
                $monthNo = $interval->m;
                $tranche_amount = $tranche->tranch_amount;

                $due_date_first_inst = date("Y-M-10", strtotime("+" . 2 . " months", strtotime($due_date)));
                $set_due_date = date("Y-M-10", strtotime("+" . 1 . " months", strtotime($due_date)));
                $inst_due_date = date("Y-M-10", strtotime("+" . $i . " months", strtotime($set_due_date)));

                if ($due_date_first_inst < $fromDate) {
                    if ($monthNo == $i) {
                        $p_amount += $tranche_amount;
                        $trancheCount = $trancheCount . ',' . $tranche->tranch_no;
                    }
                }
            }

            $p_amount = ($p_amount - $p_baseRent);
            $p_months = $total_months - $i;

            $resultArray[$i]['due_date_first_inst'] = $due_date_first_inst;
            $resultArray[$i]['due_date'] = $inst_due_date;
            $resultArray[$i]['year_rate'] = $p_monthlyRent['rate'];
            $resultArray[$i]['term'] = $p_monthlyRent['term'];
            $resultArray[$i]['monthly_rental'] = $p_monthlyRent['result'];
            $resultArray[$i]['rent'] = $p_fixedRent;
            $resultArray[$i]['principle_amt'] = $p_baseRent;
            $resultArray[$i]['out_standing'] = $p_amount;
            $resultArray[$i]['installments'] = $total_months;

        }

        return $resultArray;

    }

    public static function KppHousingReport($sanction, $disbursed_amount, $tenor, $recovery_till_date)
    {
        $loan = Loans::find()->where(['sanction_no' => $sanction])->select(['id'])->one();
        $resultFixRent = 0;
        $rentRate5Yrs = 0.02;
        $rentRate10Yrs = 0.04;
        $rentRate15Yrs = 0.05;
        $rentRate20Yrs = 0.1158;

        $p_amount = $disbursed_amount;
        $total_months = $tenor;
        $p_months = $tenor;

        $p_rentRate = 0;

        for ($i = 1; $i <= $total_months; $i++) {
            switch (ceil($i / 60)) {
                case 1:
                    $p_rentRate = $rentRate5Yrs;
                    break;
                case 2:
                    $p_rentRate = $rentRate10Yrs;
                    break;
                case 3:
                    $p_rentRate = $rentRate15Yrs;
                    break;
                case 4:
                    $p_rentRate = $rentRate20Yrs;
                    break;
                default:
                    //System.out.println("None matched");
            }

            $p_monthlyRent = self::calculatePMT($p_amount, $p_rentRate, $p_months);
            $p_fixedRent = self::calculateFixedRent($p_amount, $p_rentRate);
            $p_baseRent = self::calculateBaseRent($p_monthlyRent['result'], $p_fixedRent);


            $p_amount = ($p_amount - $p_baseRent);
            $p_months = $total_months - $i;

            $resultFixRent += $p_fixedRent;
        }

        $charges_amount = Recoveries::find()->where(['loan_id' => $loan->id])->andWhere(['<=', 'receive_date', $recovery_till_date])->sum('charges_amount');

        if (!empty($charges_amount) && $charges_amount != null) {
            $resultFixRent = $resultFixRent - $charges_amount;
        }

        return $resultFixRent;

    }

//========================================================
    public static function templateLedger($loan)
    {

        $resultArray = [];
        $rentRate5Yrs = 0.02;
        $rentRate10Yrs = 0.04;
        $rentRate15Yrs = 0.05;
        $rentRate20Yrs = 0.1158;

        $disburse_date = date('Y-m-d', $loan->date_disbursed);
        if ($loan->date_disbursed == 0) {
            $disburse_date = date('Y-m-d', $loan->readyTranche->cheque_date);
        }

        $p_amount = $loan->disbursed_amount;
        $total_months = $loan->inst_months;
        $p_months = $loan->inst_months;
        $due_date = date("Y-m-10", strtotime($disburse_date));
        $p_rentRate = 0;

        for ($i = 1; $i <= $total_months; $i++) {
            switch (ceil($i / 60)) {
                case 1:
                    $p_rentRate = $rentRate5Yrs;
                    break;
                case 2:
                    $p_rentRate = $rentRate10Yrs;
                    break;
                case 3:
                    $p_rentRate = $rentRate15Yrs;
                    break;
                case 4:
                    $p_rentRate = $rentRate20Yrs;
                    break;
                default:
                    //System.out.println("None matched");
            }

            $p_monthlyRent = self::calculatePMT($p_amount, $p_rentRate, $p_months);
            $p_fixedRent = self::calculateFixedRent($p_amount, $p_rentRate);
            $p_baseRent = self::calculateBaseRent($p_monthlyRent['result'], $p_fixedRent);

            $p_amount = ($p_amount - $p_baseRent);
            $p_months = $total_months - $i;
            $due_date_first_inst = date("Y-M-10", strtotime("+" . 2 . " months", strtotime($due_date)));
            $set_due_date = date("Y-M-10", strtotime("+" . 1 . " months", strtotime($due_date)));
            $inst_due_date = date("Y-M-10", strtotime("+" . $i . " months", strtotime($set_due_date)));

            $resultArray[$i]['due_date_first_inst'] = $due_date_first_inst;
            $resultArray[$i]['due_date'] = $inst_due_date;
            $resultArray[$i]['year_rate'] = $p_monthlyRent['rate'];
            $resultArray[$i]['term'] = $p_monthlyRent['term'];
            $resultArray[$i]['monthly_rental'] = $p_monthlyRent['result'];
            $resultArray[$i]['rent'] = $p_fixedRent;
            $resultArray[$i]['principle_amt'] = $p_baseRent;
            $resultArray[$i]['out_standing'] = $p_amount;
            $resultArray[$i]['installments'] = $total_months;

        }

        return $resultArray;

    }

    public static function calculatePMT($financingAmount, $rate, $term)
    {
        $result = 0;
        $v = (1 + ($rate / 12));
        $t = (-($term / 12) * 12);
        $result = ($financingAmount * ($rate / 12)) / (1 - pow($v, $t));

        $rArray['rate'] = $rate;
        $rArray['term'] = $term;
        $rArray['result'] = round($result);
//        return round($result);
        return $rArray;
    }

    private static function calculateFixedRent($balanceAmount, $rate)
    {
        $result = 0;
        $result = $balanceAmount * $rate / 12;
        return round($result);
    }

    private static function calculateBaseRent($monthlyRent, $fixedRent)
    {
        $result = 0;
        $result = $monthlyRent - $fixedRent;
        return round($result);
    }

    public static function old_tentativeLedger($loan)
    {
        //return self::templateLedger($loan);

//echo '<pre>';print_r($loan->disbTranches);die('here');
        $total_instalments = $loan->inst_months;
        //$loan_amount = $loan->loan_amount;
        $loan_amount = 0;
        $disb_amount = $loan->disbursed_amount;
        $rec_amount = 0;
        $rec_count = 0;


        $five_year_rate = 0.02;
        $ten_year_rate = 0.04;
        $disburse_date = date('Y-m-d', $loan->date_disbursed);
        if ($loan->date_disbursed == 0) {
            $disburse_date = date('Y-m-d', $loan->readyTranche->cheque_date);
        }
        $installments = $total_instalments;
        $rent = 0;
        $year_rate = $five_year_rate;
        $due_date = date("Y-m-10", strtotime($disburse_date));

        if (isset($loan->disbTranches)) {
            foreach ($loan->disbTranches as $disb) {
                $loan_amount = $loan_amount + $disb->tranch_amount;
            }
        }
        $recoveries_count = Recoveries::find()->where(['loan_id' => $loan->id])->groupBy('due_date')->all();
        if (isset($loan->recoveries)) {
            //$rec_count = count($loan->recoveries);
            $rec_count = count($recoveries_count);
            $due_date = date("Y-m-10", strtotime("+" . $rec_count . " months", strtotime($due_date)));
            foreach ($loan->recoveries as $rec) {
                $rec_amount = $rec_amount + $rec->amount;
            }
        }

        if (count($loan->disbTranches) > 1) {
            $loan_amount_pmt = $loan_amount - $rec_amount;

        }
        $loan_amount_pmt = $loan_amount;
        $loan_amount = $loan_amount - $rec_amount;
        $out_standing = $loan_amount;

        for ($i = 1 + $rec_count; $i <= $total_instalments; $i++) {

            if ($i - $rec_count == 1) {
                $first_monthly_rental = round(CalculationHelper::calculatePMT($five_year_rate / 12, $total_instalments, -$loan_amount_pmt));
                $rent = round($loan_amount * $five_year_rate / 12);
                $principle_amt = $first_monthly_rental - $rent;
                //$out_standing = $loan_amount;
            } else if ($i == 61) {

                $first_monthly_rental = round(CalculationHelper::calculatePMT($ten_year_rate / 12, $installments, -$out_standing));
                $year_rate = $ten_year_rate;
            } /*else if($i == $total_instalments) {
                $first_monthly_rental = $first_monthly_rental+$out_standing;
                $out_standing = 0;
            } */
            $out_standing = $out_standing - $principle_amt;
            if ($out_standing < 0) {
                $out_standing = 0;
            }

            $due_date = date("Y-M-10", strtotime("+" . 1 . " months", strtotime($due_date)));

            $result[$i]['monthly_rental'] = $first_monthly_rental;
            $result[$i]['principle_amt'] = $principle_amt;
            $result[$i]['rent'] = $rent;
            $result[$i]['year_rate'] = $year_rate * 100 . '%';
            $result[$i]['installments'] = $installments;
            $result[$i]['out_standing'] = $out_standing;
            $result[$i]['due_date'] = $due_date;

            if ($i < 60) {
                $rent = round($out_standing * $five_year_rate / 12);
            } elseif ($i >= 60 && $i < 120) {
                $rent = round($out_standing * $ten_year_rate / 12);
            }
            $principle_amt = round($first_monthly_rental - $rent);
            $installments--;
            // }

        }

        $installments = $rec_count;
        if (isset($loan->disbTranches)) {
            $loan_amount = 0;

            if (count($loan->disbTranches) > 1) {
                $disb_previous = count($loan->disbTranches) - 1;
            } else {
                $disb_previous = count($loan->disbTranches);
            }

            for ($t = 1; $t <= $disb_previous; $t++) {
                $loan_amount = $loan_amount + $disb->tranch_amount;
            }

            $out_standing = $loan_amount;

            for ($j = 1; $j <= $rec_count; $j++) {

                if ($j == 1) {
                    $first_monthly_rental = round(CalculationHelper::calculatePMT($five_year_rate / 12, $total_instalments, -$loan_amount));
                    $rent = round($loan_amount * $five_year_rate / 12);
                    $principle_amt = $first_monthly_rental - $rent;

                }

                $out_standing = $out_standing - $principle_amt;
                $due_date = date("Y-M-10", strtotime("+" . 1 . " months", strtotime($disburse_date)));

                $recv[$j]['monthly_rental'] = $first_monthly_rental;
                $recv[$j]['principle_amt'] = $principle_amt;
                $recv[$j]['rent'] = $rent;
                $recv[$j]['year_rate'] = $five_year_rate * 100 . '%';
                $recv[$j]['installments'] = $installments;
                $recv[$j]['out_standing'] = $out_standing;
                $recv[$j]['due_date'] = $due_date;


                if ($j < 60) {
                    $rent = round($out_standing * $five_year_rate / 12);
                }
                $principle_amt = round($first_monthly_rental - $rent);
                $installments--;
            }
            if (!empty($recv)) {
                $result = array_merge($recv, $result);
            }

        }
        return $result;
    }

    public static function old_templateLedger($loan)
    {
        $total_instalments = $loan->inst_months;
        $loan_amount = $loan->loan_amount;
        $disb_amount = $loan->disbursed_amount;
        $five_year_rate = 0.02;
        $ten_year_rate = 0.04;
        $disburse_date = date('Y-m-d', $loan->date_disbursed);
        if ($loan->date_disbursed == 0) {
            $disburse_date = date('Y-m-d', $loan->readyTranche->cheque_date);
        }
        $installments = $total_instalments;
        $rent = 0;
        $out_standing = $loan_amount;
        $monthly_rental = 0;
        $year_rate = $five_year_rate;
        $due_date = date("Y-m-10", strtotime("+" . 1 . " months", strtotime($disburse_date)));
        for ($i = 1; $i <= $total_instalments; $i++) {

            if ($i == 1) {
                $monthly_rental = round(CalculationHelper::calculatePMT($five_year_rate / 12, $total_instalments, -$loan_amount));
                $rent = round($loan_amount * $five_year_rate / 12);
                $principle_amt = $monthly_rental - $rent;
            } else if ($i == 61) {
                $monthly_rental = round(CalculationHelper::calculatePMT($ten_year_rate / 12, $installments, -$out_standing));
                $year_rate = $ten_year_rate;
            }
            $out_standing = $out_standing - $principle_amt;
            if ($i == $total_instalments) {
                $monthly_rental = $monthly_rental + $out_standing;
                $out_standing = 0;
            }
            $due_date = date("Y-m-10", strtotime("+" . 1 . " months", strtotime($due_date)));
            $result[$i]['monthly_rental'] = $monthly_rental;
            $result[$i]['principle_amt'] = $principle_amt;
            $result[$i]['rent'] = $rent;
            $result[$i]['year_rate'] = $year_rate * 100 . '%';
            $result[$i]['installments'] = $installments;
            $result[$i]['out_standing'] = $out_standing;
            $result[$i]['due_date'] = $due_date;

            if ($i < 60) {
                $rent = round($out_standing * $five_year_rate / 12);
            } elseif ($i >= 60 && $i < 120) {
                $rent = round($out_standing * $ten_year_rate / 12);
            }
            $principle_amt = round($monthly_rental - $rent);
            $installments--;
        }
        return $result;
    }

    public static function tentativeRecovery($loan_id, $due_date)
    {
        $recoveries = Recoveries::find()
            ->where(['loan_id' => $loan_id])
            ->andWhere(['due_date' => $due_date])
            ->andWhere(['deleted' => 0])
            ->all();
        return $recoveries;
    }


    public static function trancheAmount($schedule, $k)
    {
        $dateTo = strtotime(date('Y-m-t', $schedule->due_date));
        $amountDisbursed = 0;

        if ($k == 0) {
            $loanTranches = LoanTranches::find()->where(['<=', 'date_disbursed', $dateTo])
                ->andWhere(['loan_id' => $schedule->loan_id])
                ->andWhere(['>', 'disbursement_id', 0])
                ->andWhere(['>', 'date_disbursed', 0])
                ->andWhere(['status' => 6])
                ->all();
        } else {
            $startDate = strtotime(date('Y-m-01', $schedule->due_date));
            $endDate = strtotime(date('Y-m-t', $schedule->due_date));
            $loanTranches = \common\models\LoanTranches::find()
                ->where(['loan_id' => $schedule->loan_id])
                ->andWhere(['>=', 'date_disbursed', $startDate])
                ->andWhere(['<=', 'date_disbursed', $endDate])
                ->andWhere(['status' => 6])
                ->all();
        }


        if (!empty($loanTranches) && $loanTranches != null) {
            foreach ($loanTranches as $tranche) {
                $amountDisbursed = $amountDisbursed + $tranche->tranch_amount;
            }
        }

        return $amountDisbursed;
    }
}