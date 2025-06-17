<?php
/**
 * Created by PhpStorm.
 * User: junaid.fayyaz
 * Date: 2/26/2018
 * Time: 12:27 PM
 */

namespace common\components\Helpers;

use common\models\LoanTranches;
use common\models\Recoveries;
use common\models\Schedules;
use common\models\SchedulesHousingPre;
use Yii;
use yii\db\Exception;

class DisbursementHelper
{
    public static function GenerateSchedule($loan, $crone = false, $close_loan = false)
    {
        if (in_array($loan->project_id, StructureHelper::trancheProjects())) {
            $disburse_date = date('Y-m-d', $loan->date_disbursed);
            if ($disburse_date < date("2024-02-01")) {
                self::GenerateScheduleHousingPrevious($loan, $crone, $close_loan);
            } else {
                self::GenerateScheduleHousing($loan, $crone, $close_loan);
            }

        } else {
            $total_instalments = $loan->inst_months;
            $loan_amount = $loan->loan_amount;
            $disb_amount = $loan->disbursed_amount;
            $disburse_date = date('Y-m-d', $loan->date_disbursed);

            if (in_array($loan->project_id, [105, 106])) {
                $schedule_amount = $loan->inst_amnt;
                $total_instalments_count = $loan->inst_months;
            } else {
                $schedule_amount = ceil(($loan_amount / $total_instalments) / 100) * 100;
                $total_instalments_count = ceil($disb_amount / $schedule_amount);
            }

            $service_charges = $loan->service_charges;
            if ($loan->disbursed_amount != $loan->loan_amount) {
                $tranche_charges = $service_charges / 2;
            } else {
                $tranche_charges = $service_charges;
            }

            if ($tranche_charges == 0) {
                $charges_amount = ceil(($total_instalments_count) / 100) * 100;
            } else {
                $charges_amount = ceil(($tranche_charges / $total_instalments_count) / 100) * 100;
            }


            if (in_array($loan->project_id, [132])) {
                if (($disburse_date >= date("Y-m-01", strtotime($disburse_date))) && ($disburse_date < date("Y-m-11", strtotime($disburse_date)))) {
                    $due_date = date("Y-m-10", strtotime('+2 month', strtotime(date("Y-m", strtotime($disburse_date)))));
                }

                if (($disburse_date > date("Y-m-10", strtotime($disburse_date))) && ($disburse_date <= date("Y-m-t", strtotime($disburse_date)))) {
                    $due_date = date("Y-m-10", strtotime('+3 month', strtotime(date("Y-m", strtotime($disburse_date)))));
                }

            } else {
                if ($disburse_date > date("Y-m-10", strtotime($disburse_date))) {
                    $due_date = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", strtotime($disburse_date)))));
                } else {
                    $due_date = date("Y-m-10", strtotime($disburse_date));
                }
            }

            $months = self::getSchdlMonths()[$loan->inst_type];
            $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
            $f = true;
            $sc_sum = 0;
            $transaction = Yii::$app->db->beginTransaction();

            try {
                for ($i = 1; $i <= $total_instalments_count; $i++) {
                    $model = new Schedules();
                    $model->loan_id = $loan->id;
                    $model->application_id = $loan->application->id;
                    $model->branch_id = $loan->branch_id;
                    $model->due_date = strtotime($due_date);
                    $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                    $diff = $tranche_charges - $sc_sum;
                    if ($i == $total_instalments_count) {
                        if ($f) {
                            if ($diff == 0) {
                                $model->charges_schdl_amount = 0;
                            } else if ($diff < $charges_amount) {
                                $model->charges_schdl_amount = $diff;
                            } else {
                                $model->charges_schdl_amount = $tranche_charges - ($charges_amount * ($total_instalments_count - 1));
                            }
                            $model->schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count - 1));

                        } else {
                            $model->charges_schdl_amount = $charges_amount;
                            $model->schdl_amnt = $schedule_amount;
                        }
                    } else {
                        if ($diff == 0) {
                            $model->charges_schdl_amount = 0;
                        } else if ($diff < $charges_amount) {
                            $model->charges_schdl_amount = $diff;
                        } else {
                            $model->charges_schdl_amount = $charges_amount;
                        }
                        $model->schdl_amnt = $schedule_amount;

                    }
                    $sc_sum += $model->charges_schdl_amount;

                    if ($i == 1) {
                        $model->charges_due_amount = $model->charges_schdl_amount;
                        $model->due_amnt = $model->schdl_amnt;
                    }
                    if ($crone) {
                        $model->assigned_to = '1';
                        $model->created_by = '1';
                    }

                    if (!$flag = $model->save()) {
                        $transaction->rollBack();
                        return false;
                    }

                }
                if ($flag) {

                    if ($model->charges_schdl_amount != 0 && in_array($loan->project_id, [52, 76])) {

                        if ($disburse_date < date("2024-02-01") && $close_loan == false) {
                            $chargesScheduleAmount = Schedules::find()
                                ->where(['loan_id' => $loan->id])
                                ->sum('charges_schdl_amount');

                            $scheduleCount = Schedules::find()
                                ->where(['loan_id' => $loan->id])
                                ->andWhere(['>', 'due_date', strtotime(date("2024-03-10"))])
                                ->count('id');
                            $gst_percentage = $loan->branch->province->gst;
                            $taxPrcAmount = ($chargesScheduleAmount / $scheduleCount) * $gst_percentage / 100;
                            $scheduleModel = Schedules::find()
                                ->where(['loan_id' => $loan->id])
                                ->andWhere(['>', 'due_date', strtotime(date("2024-03-10"))])
                                ->all();
                            foreach ($scheduleModel as $model) {
                                $model->charges_schdl_amnt_tax = ceil($taxPrcAmount);
                                $model->save();
                            }
                        } else {
                            $scheduleModel = Schedules::find()
                                ->where(['loan_id' => $loan->id])
                                ->all();
                            $gst_percentage = $loan->branch->province->gst;
                            foreach ($scheduleModel as $model) {
                                $charges_schdl_amount_tax = $model->charges_schdl_amount * ($gst_percentage / 100);
                                $model->charges_schdl_amnt_tax = ceil($charges_schdl_amount_tax);
                                $model->save();
                            }
                        }
                    }

                    $transaction->commit();
                    return true;
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
    }

    public static function GenerateScheduleHousing($loan, $crone = false, $close_loan = false)
    {
        $total_instalments = $loan->inst_months;
        $loan_amount = $loan->loan_amount;
        $disb_amount = $loan->disbursed_amount;
        $disburse_date = date('Y-m-d', $loan->date_disbursed);

        $first_schedule_date = null;

        if (in_array($loan->project_id, [24]) && $disburse_date < date("2024-06-01")) {
            $schedule_amount = ceil(($loan_amount / $total_instalments) / 100) * 100;
        } else {
            $schedule_amount = round(($loan_amount / $total_instalments));
        }
        //$total_instalments_count = ceil($disb_amount / $schedule_amount);

        $service_charges = $loan->service_charges;

        if ($loan->disbursed_amount != $loan->loan_amount) {
            $total_instalments_count = $total_instalments / 2;
            $tranche_charges = $service_charges / 2;
        } else {
            $total_instalments_count = $total_instalments;
            $tranche_charges = $service_charges;
        }

        $charges_amount = round(($tranche_charges / $total_instalments_count));

        if (($disburse_date > date("Y-m-10", strtotime($disburse_date))) && in_array($loan->project_id, [100, 114, 118, 119, 131,35])) {
            $due_date = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", strtotime($disburse_date)))));
        } else {
            $due_date = date("Y-m-10", strtotime(date("Y-m", strtotime($disburse_date))));
        }
        if (($disburse_date > date("Y-m-01", strtotime($disburse_date))) && in_array($loan->project_id, [110])) {
            $due_date = date("Y-m-10", strtotime(date("Y-m", strtotime($disburse_date))));
        }
        if (!in_array($loan->project_id, [110, 100, 114, 118, 119, 131,35])) {
            $due_date = date("Y-m-10", strtotime("+1 month", strtotime($disburse_date)));
        }
        if (in_array($loan->project_id, [132])) {
            if (($disburse_date >= date("Y-m-01", strtotime($disburse_date))) && ($disburse_date < date("Y-m-11", strtotime($disburse_date)))) {
                $due_date = date("Y-m-10", strtotime('+2 month', strtotime(date("Y-m", strtotime($disburse_date)))));
            }

            if (($disburse_date > date("Y-m-10", strtotime($disburse_date))) && ($disburse_date <= date("Y-m-t", strtotime($disburse_date)))) {
                $due_date = date("Y-m-10", strtotime('+3 month', strtotime(date("Y-m", strtotime($disburse_date)))));
            }

        }
        $months = self::getSchdlMonths()[$loan->inst_type];


        $f = true;
        $sc_sum = 0;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            for ($i = 1; $i <= $total_instalments_count; $i++) {
                $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                $model = new Schedules();
                $model->loan_id = $loan->id;
                $model->application_id = $loan->application->id;
                $model->branch_id = $loan->branch_id;
                $model->due_date = strtotime($due_date);

                $diff = $tranche_charges - $sc_sum;
                if ($i == $total_instalments_count) {
                    if ($f) {
                        if ($diff == 0) {
                            $model->charges_schdl_amount = 0;
                        } else if ($diff < $charges_amount) {
                            $model->charges_schdl_amount = $diff;
                        } else {
                            $model->charges_schdl_amount = $tranche_charges - ($charges_amount * ($total_instalments_count - 1));
                        }
                        $model->schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count - 1));

                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                        $model->schdl_amnt = $schedule_amount;
                    }
                } else {
                    if ($diff == 0) {
                        $model->charges_schdl_amount = 0;
                    } else if ($diff < $charges_amount) {
                        $model->charges_schdl_amount = $diff;
                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                    }
                    $model->schdl_amnt = $schedule_amount;

                }

                if ($model->charges_schdl_amount != 0 && in_array($loan->project_id, [52, 76, 119, 24, 100, 118, 131, 97, 61, 62, 83, 103, 90, 109, 127, 110,35])) {
                    if ($loan->branch->province->id == 2) {
                        if ($model->due_date <= strtotime(date("2024-06-10"))) {
                            $gst_percentage = 13;
                        } else {
                            $gst_percentage = $loan->branch->province->gst;
                        }

                        if ($disburse_date > date("2025-04-01") && in_array($loan->project_id, [119, 24, 100, 118, 131, 97, 61, 62, 83, 103, 90, 109, 127, 110,35])) {
                            $charges_schdl_amount_tax = $model->charges_schdl_amount * ($gst_percentage / 100);
                            $model->charges_schdl_amnt_tax = ceil($charges_schdl_amount_tax);
                        } elseif (in_array($loan->project_id, [52, 76])) {
                            $charges_schdl_amount_tax = $model->charges_schdl_amount * ($gst_percentage / 100);
                            $model->charges_schdl_amnt_tax = ceil($charges_schdl_amount_tax);
                        }

                    } else {
                        if ($disburse_date > date("2025-04-01") && in_array($loan->project_id, [119, 24, 100, 118, 131, 97, 61, 62, 83, 103, 90, 109, 127, 110,35])) {
                            $gst_percentage = $loan->branch->province->gst;
                            $charges_schdl_amount_tax = $model->charges_schdl_amount * ($gst_percentage / 100);
                            $model->charges_schdl_amnt_tax = ceil($charges_schdl_amount_tax);
                        } elseif (in_array($loan->project_id, [52, 76])) {
                            $gst_percentage = $loan->branch->province->gst;
                            $charges_schdl_amount_tax = $model->charges_schdl_amount * ($gst_percentage / 100);
                            $model->charges_schdl_amnt_tax = ceil($charges_schdl_amount_tax);
                        }
                    }
                }

                $sc_sum += $model->charges_schdl_amount;

                if ($i == 1) {
                    $model->charges_due_amount = $model->charges_schdl_amount;
                    $model->due_amnt = $model->schdl_amnt;
                    $first_schedule_date = $due_date;
                }
                if ($crone) {
                    $model->assigned_to = '1';
                    $model->created_by = '1';
                }
                if (!$flag = $model->save(false)) {
                    $transaction->rollBack();
                    return false;
                }

            }

            if ($flag) {
                $transaction->commit();
                if ($loan->project_id == 132) {
                    $status = 'Loan Disbursed';
                    $statusReason = 'Loan Disbursed';
                    AcagHelper::actionPush($loan->application, $status, $statusReason, $loan->disbursed_amount, date('Y-m-d'), 0, $loan);
                    $cnic_without_hyphens = str_replace('-', '', $loan->application->member->cnic);
                    $obj = [
                        "CNIC"=> $cnic_without_hyphens,
                        "FirstDisbursementDate"=> $disburse_date,
                        "NoOfInstallments"=>$total_instalments_count,
                        "MonthlyInstallmentAmount"=> $schedule_amount,
                        "FirstDueDate"=>$first_schedule_date,
                        "SecondDisbursementDate"=> null,
                    ];
                    AcagHelper::actionPushDisbursement($obj);
                }
                return true;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    public static function GenerateScheduleHousingPrevious($loan, $crone = false, $close_loan = false)
    {
        $total_instalments = $loan->inst_months;
        $loan_amount = $loan->loan_amount;
        $disb_amount = $loan->disbursed_amount;
        $disburse_date = date('Y-m-d', $loan->date_disbursed);

        if (in_array($loan->project_id, [24]) && $disburse_date < date("2024-05-01")) {
            $schedule_amount = ceil(($loan_amount / $total_instalments));
        } else {
            $schedule_amount = round($loan_amount / $total_instalments);
        }
//        echo $schedule_amount;
//        die();
        //$total_instalments_count = ceil($disb_amount / $schedule_amount);

        $service_charges = $loan->service_charges;

        if ($loan->disbursed_amount != $loan->loan_amount) {
            $total_instalments_count = $total_instalments / 2;
            $tranche_charges = $service_charges / 2;
        } else {
            $total_instalments_count = $total_instalments;
            $tranche_charges = $service_charges;
        }

        $charges_amount = round(($tranche_charges / $total_instalments_count));

        if (($disburse_date > date("Y-m-10", strtotime($disburse_date))) && in_array($loan->project_id, [100, 114, 118, 119])) {
            $due_date = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", strtotime($disburse_date)))));
        } else {
            $due_date = date("Y-m-10", strtotime(date("Y-m", strtotime($disburse_date))));
        }
        if (($disburse_date > date("Y-m-01", strtotime($disburse_date))) && in_array($loan->project_id, [110])) {
            $due_date = date("Y-m-10", strtotime(date("Y-m", strtotime($disburse_date))));
        }
        if (!in_array($loan->project_id, [110, 100, 114, 118, 119,35])) {
            $due_date = date("Y-m-10", strtotime("+1 month", strtotime($disburse_date)));
        }

        if (in_array($loan->project_id, [132])) {
            if (($disburse_date >= date("Y-m-01", strtotime($disburse_date))) && ($disburse_date < date("Y-m-11", strtotime($disburse_date)))) {
                $due_date = date("Y-m-10", strtotime('+2 month', strtotime(date("Y-m", strtotime($disburse_date)))));
            }

            if (($disburse_date > date("Y-m-10", strtotime($disburse_date))) && ($disburse_date <= date("Y-m-t", strtotime($disburse_date)))) {
                $due_date = date("Y-m-10", strtotime('+3 month', strtotime(date("Y-m", strtotime($disburse_date)))));
            }

        }

        $months = self::getSchdlMonths()[$loan->inst_type];


        $f = true;
        $sc_sum = 0;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            for ($i = 1; $i <= $total_instalments_count; $i++) {
                $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                $model = new Schedules();
                $model->loan_id = $loan->id;
                $model->application_id = $loan->application->id;
                $model->branch_id = $loan->branch_id;
                $model->due_date = strtotime($due_date);

                $diff = $tranche_charges - $sc_sum;
                if ($i == $total_instalments_count) {
                    if ($f) {
                        if ($diff == 0) {
                            $model->charges_schdl_amount = 0;
                        } else if ($diff < $charges_amount) {
                            $model->charges_schdl_amount = $diff;
                        } else {
                            $model->charges_schdl_amount = $tranche_charges - ($charges_amount * ($total_instalments_count - 1));
                        }
                        $model->schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count - 1));

                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                        $model->schdl_amnt = $schedule_amount;
                    }
                } else {
                    if ($diff == 0) {
                        $model->charges_schdl_amount = 0;
                    } else if ($diff < $charges_amount) {
                        $model->charges_schdl_amount = $diff;
                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                    }
                    $model->schdl_amnt = $schedule_amount;

                }
                $sc_sum += $model->charges_schdl_amount;

                if ($i == 1) {
                    $model->charges_due_amount = $model->charges_schdl_amount;
                    $model->due_amnt = $model->schdl_amnt;
                }
                if ($crone) {
                    $model->assigned_to = '1';
                    $model->created_by = '1';
                }
                if (!$flag = $model->save(false)) {
                    $transaction->rollBack();
                    return false;
                }

            }

            if ($flag) {
                if ($disburse_date < date("2024-02-01") && in_array($loan->project_id, [52, 76]) && $close_loan == false) {
                    if ($loan->branch->province->id == 2) {

                        $chargesScheduleAmount = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->select(['charges_schdl_amount'])
                            ->one();
                        $chargesScheduleAmount = $chargesScheduleAmount->charges_schdl_amount;
                        $scheduleTotalCount = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['<', 'due_date', strtotime(date("2024-07-10"))])
                            ->count('id');

                        $taxAmountTL6 = ($chargesScheduleAmount * 13 / 100) * $scheduleTotalCount;

                        $scheduleCountAFTJun = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['>', 'due_date', strtotime(date("2024-06-10"))])
                            ->count('id');

                        $gst_percentage = $loan->branch->province->gst;
                        $taxAmountAft6 = ($chargesScheduleAmount * $gst_percentage / 100) * $scheduleCountAFTJun;

                        $totalChargesScheduleAmount = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->sum('charges_schdl_amount');

                        $scheduleCount = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['>', 'due_date', strtotime(date("2024-03-10"))])
                            ->count('id');
                        $initTaxAmountPerMonth = ($totalChargesScheduleAmount / $scheduleCount) * 13 / 100;

                        $scheduleCountAFTJunBFRSep = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['>', 'due_date', strtotime(date("2024-03-10"))])
                            ->andWhere(['<', 'due_date', strtotime(date("2024-07-10"))])
                            ->count('id');

                        $remainingTaxAmount = $taxAmountTL6 - ($initTaxAmountPerMonth * $scheduleCountAFTJunBFRSep) + $taxAmountAft6;

                        $scheduleModel = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['>', 'due_date', strtotime(date("2024-03-10"))])
                            ->all();
                        foreach ($scheduleModel as $model) {
                            if ($model->due_date <= strtotime(date("2024-06-10"))) {
                                $model->charges_schdl_amnt_tax = ceil($initTaxAmountPerMonth);
                            } else {
                                $taxPrcAmount = $remainingTaxAmount / $scheduleCountAFTJun;
                                $model->charges_schdl_amnt_tax = round($taxPrcAmount);
                            }
                            $model->save();
                        }

                    } else {
                        $chargesScheduleAmount = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->sum('charges_schdl_amount');

                        $scheduleCount = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['>', 'due_date', strtotime(date("2024-03-10"))])
                            ->count('id');
                        $gst_percentage = $loan->branch->province->gst;
                        $taxPrcAmount = ($chargesScheduleAmount / $scheduleCount) * $gst_percentage / 100;

                        $scheduleModel = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->andWhere(['>', 'due_date', strtotime(date("2024-03-10"))])
                            ->all();
                        foreach ($scheduleModel as $model) {
                            $model->charges_schdl_amnt_tax = ceil($taxPrcAmount);
                            $model->save();
                        }
                    }

                } else {
                    if ($loan->branch->province->id == 2) {
                        $scheduleModel = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->all();
                        foreach ($scheduleModel as $model) {
                            if ($model->due_date <= strtotime(date("2024-06-10"))) {
                                $taxAmountTL6 = ($model->charges_schdl_amount * 13 / 100);
                                $model->charges_schdl_amnt_tax = ceil($taxAmountTL6);
                            } else {
                                $gst_percentage = $loan->branch->province->gst;
                                $taxAmountAft6 = ($model->charges_schdl_amount * $gst_percentage / 100);
                                $model->charges_schdl_amnt_tax = ceil($taxAmountAft6);
                            }
                            $model->save();
                        }
                    } else {
                        $scheduleModel = Schedules::find()
                            ->where(['loan_id' => $loan->id])
                            ->all();
                        foreach ($scheduleModel as $model) {
                            $gst_percentage = $loan->branch->province->gst;
                            $charges_schdl_amount_tax = $model->charges_schdl_amount * ($gst_percentage / 100);
                            $model->charges_schdl_amnt_tax = ceil($charges_schdl_amount_tax);
                            $model->save();
                        }
                    }
                }

                $transaction->commit();
                if ($loan->project_id == 132) {
                    $status = 'Loan Disbursed';
                    $statusReason = 'Loan Disbursed';
                    AcagHelper::actionPush($loan->application, $status, $statusReason, $loan->disbursed_amount, date('Y-m-d'), 0, $loan);
                }
                return true;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    public static function GenerateScheduleHousingExtraCase($loan, $previous_schdl, $tranch_amount, $crone = false)
    {

        $total_instalments = $loan->inst_months - $previous_schdl[0]['schdl_count'];
        $loan_amount = $loan->loan_amount - $previous_schdl[0]['credit_sum'];
        $disb_amount = $loan->disbursed_amount;
        $disburse_date = date('Y-m-d', $loan->date_disbursed);

        $schedule_amount = round(($loan_amount / $total_instalments));

        $service_charges = $loan->service_charges;
        if ($loan->disbursed_amount >= 500001 && $loan->disbursed_amount <= 750000) {
            $total_instalments_count = $total_instalments / 3;
            $tranche_charges = $service_charges / 3;
        }
        if ($loan->disbursed_amount >= 750001 && $loan->disbursed_amount <= 1000000) {
            $total_instalments_count = $total_instalments / 4;
            $tranche_charges = $service_charges / 4;
        }

        $total_instalments_count = round($total_instalments_count);
        $charges_amount = round(($tranche_charges / $total_instalments_count));
        print_r($total_instalments_count);
        print_r($schedule_amount);

        /* if ($disburse_date > date("Y-m-10", strtotime($disburse_date)) || in_array($loan->project_id, StructureHelper::trancheProjects())) {
             $due_date = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", strtotime($disburse_date)))));
         } else {
             $due_date = date("Y-m-10", strtotime($disburse_date));
         }*/
        $schdl_date = Schedules::find()->where(['loan_id' => $loan->id])->andWhere(['!=', 'credit', 0])->orderBy(['id' => SORT_DESC])->one();
        $due_date = $schdl_date->due_date;

        $months = self::getSchdlMonths()[$loan->inst_type];
        //$due_date = date("Y-m-10", strtotime("+1 month", strtotime($due_date)));
        $due_date = date('Y-m-d', strtotime("+1 month", $due_date));
        $f = true;
        $sc_sum = 0;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            for ($i = 1; $i <= $total_instalments_count; $i++) {
                $model = new Schedules();
                $model->loan_id = $loan->id;
                $model->application_id = $loan->application->id;
                $model->branch_id = $loan->branch_id;
                $model->due_date = strtotime($due_date);
                //$due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                $due_date = date("Y-m-10", strtotime("+1 month", strtotime($due_date)));
                $diff = $tranche_charges - $sc_sum;
                if ($i == $total_instalments_count) {
                    if ($f) {
                        if ($diff == 0) {
                            $model->charges_schdl_amount = 0;
                        } else if ($diff < $charges_amount) {
                            $model->charges_schdl_amount = $diff;
                        } else {
                            $model->charges_schdl_amount = $tranche_charges - ($charges_amount * ($total_instalments_count - 1));
                        }
                        //$model->schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count - 1));
                        $model->schdl_amnt = $tranch_amount - ($schedule_amount * ($total_instalments_count - 1));

                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                        $model->schdl_amnt = $schedule_amount;
                    }
                } else {
                    if ($diff == 0) {
                        $model->charges_schdl_amount = 0;
                    } else if ($diff < $charges_amount) {
                        $model->charges_schdl_amount = $diff;
                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                    }
                    $model->schdl_amnt = $schedule_amount;

                }
                /*if($loan->branch_id == 814) {
                    if ($model->charges_schdl_amount != 0) {
                        $gst_percentage = $loan->branch->province->gst;
                        $charges_schdl_amount_tax = $model->charges_schdl_amount * ($gst_percentage / 100);
                        $model->charges_schdl_amnt_tax = round($charges_schdl_amount_tax);
                    }
                }*/
                $sc_sum += $model->charges_schdl_amount;

                if ($i == 1) {
                    $model->charges_due_amount = $model->charges_schdl_amount;
                    $model->due_amnt = $model->schdl_amnt;
                }
                if ($crone) {
                    $model->assigned_to = '1';
                    $model->created_by = '1';
                }
                if (!$flag = $model->save()) {
                    $transaction->rollBack();
                    return false;
                }

            }
            if ($flag) {
                $transaction->commit();
                return true;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    public static function GenerateScheduleHousingExtraTwo($loan, $crone = false)
    {
        $rec_sum = Recoveries::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->sum('amount');
        $sechdl = SchedulesHousingPre::find()->where(['loan_id' => $loan->id])->orderBy([
            'id' => SORT_DESC,
        ])->one();
        $rec_count = Recoveries::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->count();
        $loan_tranche = LoanTranches::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->andWhere(['!=', 'date_disbursed', 0])->count();

        $total_instalments = $loan->inst_months - $rec_count;
        $loan_amount = $loan->loan_amount - $rec_sum;
        $schedule_amount = round(($loan_amount / $total_instalments));
        $service_charges = $loan->service_charges;

        $disb_amount = $loan->disbursed_amount;
        $disburse_date = date('Y-m-d', $loan->date_disbursed);


        if ($loan->loan_amount >= 500001 && $loan->loan_amount <= 1000000) {
            if ($loan_tranche == 3) {
                $total_instalments_count = $loan->inst_months / 3;
                $tranche_charges = $service_charges / 3;
            } else {
                $total_instalments_count = $loan->inst_months / 4;
                $tranche_charges = $service_charges / 4;
            }
        } else if ($loan->loan_amount >= 1000001 && $loan->loan_amount <= 1500000) {
            $total_instalments_count = $loan->inst_months / 5;
            $tranche_charges = $service_charges / 5;
        } else if ($loan->loan_amount <= 500000) {
            if ($loan_tranche == 3) {
                $total_instalments_count = $loan->inst_months / 3;
                $tranche_charges = $service_charges / 3;
            } else {
                $total_instalments_count = $loan->inst_months / 2;
                $tranche_charges = $service_charges / 2;
            }
        } else {
            $total_instalments_count = $loan->inst_months;
            $tranche_charges = $service_charges;
        }

        $charges_amount = round(($tranche_charges / $total_instalments_count));

        if ($disburse_date > date("Y-m-10", strtotime($disburse_date)) || in_array($loan->project_id, StructureHelper::trancheProjects())) {
            $due_date = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", strtotime($disburse_date)))));
        } else {
            $due_date = date("Y-m-10", strtotime($disburse_date));
        }

        $months = self::getSchdlMonths()[$loan->inst_type];
        $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
        $f = true;
        $sc_sum = 0;
        $transaction = Yii::$app->db->beginTransaction();

        $total_instalments_count_final = $total_instalments_count * $loan_tranche;
        $last_schdl_difference = ($schedule_amount - $sechdl->schdl_amnt) * $rec_count;
        $tranche_charges = $tranche_charges * $loan_tranche;

        try {
            for ($i = 1; $i <= $total_instalments_count_final; $i++) {
                $model = new Schedules();
                $model->loan_id = $loan->id;
                $model->application_id = $loan->application->id;
                $model->branch_id = $loan->branch_id;
                $model->due_date = strtotime($due_date);
                $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                $diff = $tranche_charges - $sc_sum;
                if ($i == $total_instalments_count_final) {
                    if ($f) {
                        if ($diff == 0) {
                            $model->charges_schdl_amount = 0;
                        } else if ($diff < $charges_amount) {
                            $model->charges_schdl_amount = $diff;
                        } else {
                            $model->charges_schdl_amount = $tranche_charges - ($charges_amount * ($total_instalments_count_final - 1));
                        }
                        $model->schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count_final - 1)) + $last_schdl_difference;

                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                        $model->schdl_amnt = $schedule_amount;
                    }
                } else {
                    if ($diff == 0) {
                        $model->charges_schdl_amount = 0;
                    } else if ($diff < $charges_amount) {
                        $model->charges_schdl_amount = $diff;
                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                    }
                    $model->schdl_amnt = $schedule_amount;

                }
//                if ($loan->branch->id == 814) {
                if ($model->charges_schdl_amount != 0 && in_array($loan->project_id, [52, 76])) {
                    $gst_percentage = $loan->branch->province->gst;
                    $charges_schdl_amount_tax = $model->charges_schdl_amount * ($gst_percentage / 100);
                    $model->charges_schdl_amnt_tax = ceil($charges_schdl_amount_tax);
                }
//                }
                $sc_sum += $model->charges_schdl_amount;

                if ($i == 1) {
                    $model->charges_due_amount = $model->charges_schdl_amount;
                    $model->due_amnt = $model->schdl_amnt;
                }
                if ($crone) {
                    $model->assigned_to = '1';
                    $model->created_by = '1';
                }
                $model->charges_schdl_amount = round($model->charges_schdl_amount);
                if (!$flag = $model->save()) {
                    $transaction->rollBack();
                    return false;
                }

            }
            if ($flag) {
                $transaction->commit();
                return true;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }


    public static function GenerateSchedule_($loan, $crone = false)
    {
        $f = false;
        $total_instalments = $loan->inst_months;
        //$total_instalments = $loan->inst_months;

        /*$loan_amount = $tranch->tranch_amount;
        $total_instalments = $tranch->tranch_amount/$loan->inst_months;
        $disburse_date = date('Y-m-d',$tranch->date_disbursed);*/

        $loan_amount = $loan->loan_amount;
        $disb_amount = $loan->disbursed_amount;
        //$total_instalments = $loan->disbursed_amount/$loan->inst_amnt;
        $disburse_date = date('Y-m-d', $loan->date_disbursed);
        //$schedule_amount = $loan_amount / $total_instalments ;
        $schedule_amount = ceil(($loan_amount / $total_instalments) / 100) * 100;

        $total_instalments_count = ceil($disb_amount / $schedule_amount);
        if ($disburse_date > date("Y-m-10", strtotime($disburse_date))) {
            $due_date = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", strtotime($disburse_date)))));
        } else {
            $due_date = date("Y-m-10", strtotime($disburse_date));
        }

        $months = self::getSchdlMonths()[$loan->inst_type];
        $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
        $f = true;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            for ($i = 1; $i <= $total_instalments_count; $i++) {
                $model = new Schedules();
                $model->loan_id = $loan->id;
                $model->application_id = $loan->application->id;
                $model->branch_id = $loan->branch_id;
                $model->due_date = strtotime($due_date);
                $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));

                if ($i == $total_instalments_count) {
                    if ($f) {
                        $model->schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count - 1));
                    } else {
                        $model->schdl_amnt = $schedule_amount;
                    }
                } else {
                    $model->schdl_amnt = $schedule_amount;
                }

                if ($i == 1) {
                    $model->due_amnt = $model->schdl_amnt;
                }
                if ($crone) {
                    $model->assigned_to = '1';
                    $model->created_by = '1';
                }
                if (!$flag = $model->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
            if ($flag) {
                $transaction->commit();
                return true;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }

    }

    public static function GenerateScheduleHousingDueDateFix($loan, $crone = false)
    {
        $total_instalments = $loan->inst_months;
        $loan_amount = $loan->loan_amount;
        $disb_amount = $loan->disbursed_amount;
        $disburse_date = date('Y-m-d', $loan->date_disbursed);

        $schedule_amount = round(($loan_amount / $total_instalments));
        //$total_instalments_count = ceil($disb_amount / $schedule_amount);

        $service_charges = $loan->service_charges;
        if ($loan->disbursed_amount != $loan->loan_amount) {
            $total_instalments_count = $total_instalments / 2;
            $tranche_charges = $service_charges / 2;
        } else {
            $total_instalments_count = $total_instalments;
            $tranche_charges = $service_charges;
        }

        $charges_amount = round(($tranche_charges / $total_instalments_count));
        if ($disburse_date > date("Y-m-10", strtotime($disburse_date)) || in_array($loan->project_id, StructureHelper::trancheProjects())) {
            $due_date = date("Y-m-10", strtotime('+2 month', strtotime(date("Y-m", strtotime($disburse_date)))));
        } else {
            $due_date = date("Y-m-10", strtotime($disburse_date));
        }

        $months = self::getSchdlMonths()[$loan->inst_type];
        $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
        $f = true;
        $sc_sum = 0;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            for ($i = 1; $i <= $total_instalments_count; $i++) {
                $model = new Schedules();
                $model->loan_id = $loan->id;
                $model->application_id = $loan->application->id;
                $model->branch_id = $loan->branch_id;
                $model->due_date = strtotime($due_date);
                $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                $diff = $tranche_charges - $sc_sum;
                if ($i == $total_instalments_count) {
                    if ($f) {
                        if ($diff == 0) {
                            $model->charges_schdl_amount = 0;
                        } else if ($diff < $charges_amount) {
                            $model->charges_schdl_amount = $diff;
                        } else {
                            $model->charges_schdl_amount = $tranche_charges - ($charges_amount * ($total_instalments_count - 1));
                        }
                        $model->schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count - 1));
                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                        $model->schdl_amnt = $schedule_amount;
                    }
                } else {
                    if ($diff == 0) {
                        $model->charges_schdl_amount = 0;
                    } else if ($diff < $charges_amount) {
                        $model->charges_schdl_amount = $diff;
                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                    }
                    $model->schdl_amnt = $schedule_amount;

                }
                $sc_sum += $model->charges_schdl_amount;
                if ($i == 1) {
                    $model->charges_due_amount = $model->charges_schdl_amount;
                    $model->due_amnt = $model->schdl_amnt;
                }
                if ($crone) {
                    $model->assigned_to = '1';
                    $model->created_by = '1';
                }
                if (!$flag = $model->save()) {
                    $transaction->rollBack();
                    return false;
                }

            }
            if ($flag) {
                $transaction->commit();
                return true;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    public static function GenerateScheduleHousingDueDateFixRupeeDiff($loan, $crone = false)
    {
        $total_instalments = $loan->inst_months;
        $loan_amount = $loan->loan_amount;
        $disb_amount = $loan->disbursed_amount;
        $disburse_date = date('Y-m-d', $loan->date_disbursed);

        $schedule_amount = floor(($loan_amount / $total_instalments));
        //$total_instalments_count = ceil($disb_amount / $schedule_amount);

        $service_charges = $loan->service_charges;
        if ($loan->disbursed_amount != $loan->loan_amount) {
            $total_instalments_count = $total_instalments / 2;
            $tranche_charges = $service_charges / 2;
        } else {
            $total_instalments_count = $total_instalments;
            $tranche_charges = $service_charges;
        }

        $charges_amount = round(($tranche_charges / $total_instalments_count));
        if ($disburse_date > date("Y-m-10", strtotime($disburse_date)) || in_array($loan->project_id, StructureHelper::trancheProjects())) {
            $due_date = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", strtotime($disburse_date)))));
        } else {
            $due_date = date("Y-m-10", strtotime($disburse_date));
        }

        $months = self::getSchdlMonths()[$loan->inst_type];
        $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
        $f = true;
        $sc_sum = 0;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            for ($i = 1; $i <= $total_instalments_count; $i++) {
                $model = new Schedules();
                $model->loan_id = $loan->id;
                $model->application_id = $loan->application->id;
                $model->branch_id = $loan->branch_id;
                $model->due_date = strtotime($due_date);
                $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                $diff = $tranche_charges - $sc_sum;
                if ($i == $total_instalments_count) {
                    if ($f) {
                        if ($diff == 0) {
                            $model->charges_schdl_amount = 0;
                        } else if ($diff < $charges_amount) {
                            $model->charges_schdl_amount = $diff;
                        } else {
                            $model->charges_schdl_amount = $tranche_charges - ($charges_amount * ($total_instalments_count - 1));
                        }
                        $model->schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count - 1));
                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                        $model->schdl_amnt = $schedule_amount;
                    }
                } else {
                    if ($diff == 0) {
                        $model->charges_schdl_amount = 0;
                    } else if ($diff < $charges_amount) {
                        $model->charges_schdl_amount = $diff;
                    } else {
                        $model->charges_schdl_amount = $charges_amount;
                    }
                    $model->schdl_amnt = $schedule_amount;

                }
                $sc_sum += $model->charges_schdl_amount;
                if ($i == 1) {
                    $model->charges_due_amount = $model->charges_schdl_amount;
                    $model->due_amnt = $model->schdl_amnt;
                }
                if ($crone) {
                    $model->assigned_to = '1';
                    $model->created_by = '1';
                }
                if (!$flag = $model->save()) {
                    $transaction->rollBack();
                    return false;
                }

            }
            if ($flag) {
                $transaction->commit();
                return true;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    public static function getDisbursementDetails($loan)
    {
        $data = [
            [
                'key' => 'Name',
                'value' => $loan['application']['member']->full_name,
            ],
            [
                'key' => 'Parentage',
                'value' => $loan['application']['member']->parentage,
            ],
            [
                'key' => 'No. of Installments',
                'value' => isset($loan->inst_months) ? number_format($loan->inst_months, 0, '.', '') : 0,
            ],
            [
                'key' => 'Sanction No',
                'value' => isset($loan->sanction_no) ? $loan->sanction_no : ""
            ],
            /*[
                'key' => 'Purpose of Loan',
                'value' => isset($loan->activity->name) ? $loan->activity->name : ''
            ],*/
            [
                'key' => 'Loan Amount',
                'value' => isset($loan->loan_amount) ? $loan->loan_amount : 0,
            ],
            [
                'key' => 'Disbursement Venue',
                'value' => isset($loan->disbursement) ? $loan->disbursement->venue : '',
            ],
            [
                'key' => 'Disbursement Date',
                'value' => isset($loan->disbursement) && $loan->disbursement->date_disbursed != 0 ? date('Y-m-d', $loan->disbursement->date_disbursed) : 0,
            ]

        ];
        return $data;
    }

    public static function getSchdlMonths()
    {
        return array(
            "nine_monthly" => 9,
            "Annually" => 12,
            "Semi-Annually" => 6,
            "Quarterly" => 3,
            "Monthly" => 1,
            "annually" => 12,
            "semi-annually" => 6,
            "semi_annually" => 6,
            "quarterly" => 3,
            "monthly" => 1
        );
    }
}