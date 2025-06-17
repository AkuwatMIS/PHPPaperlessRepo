<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;

use common\models\LoanTranches;
use common\models\Users;
use common\models\BranchProjectsMapping;
use Yii;

class TemplateHelper
{
    static public function replacePlaceholder($placeholder,$model)
    {
        $gst_percentage = $model->branch->province->gst;
        $rep_val='';
        if ($placeholder == 'Schedules Housing1') {
            $rep_val = '<table class="table table-bordered">
            <tbody>
            <tr>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">بقیہ فائنانسنگ</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">کل ماہانہ ادائیگی</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">کرایہ پر ٹیکس</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">بنیادی&nbsp; کرایہ</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">متعین کرایہ</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">مہینہ&nbsp;</span><br></td>
                <td style="width: 20px;"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">سیریل نمبر</span><br></td>
            </tr>';



            if($model->loan_amount>200000){
                $total_instalments = $model->inst_months/2;
            }else{
                $total_instalments = $model->inst_months;
            }
            $tranchee=LoanTranches::find()->where(['loan_id'=>$model->id,'tranch_no'=>1])->one();
            $loan_amount = $tranchee->tranch_amount;
            $disb_amount = $tranchee->tranch_amount;
            $disburse_date = date('Y-m-d', $model->readyTranche->cheque_date);
            //echo '<pre>';print_r($model->readyTranche->cheque_date);die();

            $schedule_amount = round(($loan_amount / $total_instalments));
            $total_instalments_count = $total_instalments;

            $service_charges = $model->service_charges;
            if($model->loan_amount>200000){
                $tranche_charges = $service_charges/2;
            }else{
                $tranche_charges = $service_charges;
            }

            /*if ($model->disbursed_amount != $model->loan_amount) {
                $tranche_charges = $service_charges / 2;
            } else {
                $tranche_charges = $service_charges;
            }*/

            $charges_amount = round(($tranche_charges / $total_instalments_count));

            if ($disburse_date > date("Y-m-10", strtotime($disburse_date)) || in_array($model->project_id, StructureHelper::trancheProjects())) {
                $due_date = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", strtotime($disburse_date)))));
            } else {
                $due_date = date("Y-m-10", strtotime($disburse_date));
            }

            $months = DisbursementHelper::getSchdlMonths()[$model->inst_type];
            $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
            $f = true;
            $sc_sum = 0;
            $balance=$disb_amount;
            for ($i = 1; $i <= $total_instalments_count; $i++) {
                $rep_val.='<tr>';
                if($i!=1){
                    $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                }
                $diff = $tranche_charges - $sc_sum;

                if ($i == $total_instalments_count) {
                    if ($f) {
                        if ($diff == 0) {
                            $charges_schdl_amount = 0;
                        } else if ($diff < $charges_amount) {
                            $charges_schdl_amount = $diff;
                        } else {
                            $charges_schdl_amount = $tranche_charges - ($charges_amount * ($total_instalments_count - 1));
                        }
                        $schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count - 1));
                        $balance=$balance-$schdl_amnt;

                        if(in_array($model->project_id,[52,76])){
                            $tax_amount = round($charges_schdl_amount * ($gst_percentage / 100));
                        }else{
                            $tax_amount = 0;
                        }

                        $rep_val.='<td style="text-align: center">';
                        $rep_val.=number_format($balance);
                        $rep_val.='</td>';
                        $rep_val.='<td style="text-align: center">';
                        $rep_val.=number_format($charges_schdl_amount+$schdl_amnt+$tax_amount);
                        $rep_val.='</td>';

                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($tax_amount);
                        $rep_val.='</td>';

                        $rep_val.='<td style="text-align: center">';
                        $rep_val.=number_format($schdl_amnt);
                        $rep_val.='</td>';
                        $rep_val.='<td style="text-align: center">';
                        $rep_val.=number_format($charges_schdl_amount);
                        $rep_val.='</td>';

                    } else {
                        $charges_schdl_amount = $charges_amount;
                        $schdl_amnt = $schedule_amount;
                        $balance=$balance-$schdl_amnt;
                        $tax_amount = round($charges_schdl_amount * ($gst_percentage / 100));


                        $rep_val.='<td style="text-align: center">';
                        $rep_val.=number_format($balance);
                        $rep_val.='</td>';
                        $rep_val.='<td style="text-align: center">';
                        $rep_val.=number_format($charges_schdl_amount+$schdl_amnt+$tax_amount);
                        $rep_val.='</td>';

                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($tax_amount);
                        $rep_val.='</td>';


                        $rep_val.='<td style="text-align: center">';
                        $rep_val.=number_format($schdl_amnt);
                        $rep_val.='</td>';
                        $rep_val.='<td style="text-align: center">';
                        $rep_val.=number_format($charges_schdl_amount);
                        $rep_val.='</td>';
                    }
                } else {
                    if ($diff == 0) {
                        $charges_schdl_amount = 0;
                    } else if ($diff < $charges_amount) {
                        $charges_schdl_amount = $diff;
                    } else {
                        $charges_schdl_amount = $charges_amount;
                    }
                    $schdl_amnt = $schedule_amount;
                    $balance=$balance-$schdl_amnt;
                    if(in_array($model->project_id,[52,76])){
                        $tax_amount = round($charges_schdl_amount * ($gst_percentage / 100));
                    }else{
                        $tax_amount = 0;
                    }

                    $rep_val.='<td style="text-align: center">';
                    $rep_val.=number_format($balance);
                    $rep_val.='</td>';
                    $rep_val.='<td style="text-align: center">';
                    $rep_val.=number_format($charges_schdl_amount+$schdl_amnt+$tax_amount);
                    $rep_val.='</td>';

                    $rep_val.='<td  style="text-align: center">';
                    $rep_val.=number_format($tax_amount);
                    $rep_val.='</td>';

                    $rep_val.='<td style="text-align: center">';
                    $rep_val.=number_format($schdl_amnt);
                    $rep_val.='</td>';
                    $rep_val.='<td style="text-align: center">';
                    $rep_val.=number_format($charges_schdl_amount);
                    $rep_val.='</td>';
                }
                $sc_sum += $charges_schdl_amount;

                if ($i == 1) {
                    $charges_due_amount = $charges_schdl_amount;
                    $due_amnt = $schdl_amnt;
                }
                $rep_val.='<td style="text-align: center">';
                $rep_val.=date('d-M-Y',strtotime($due_date));
                $rep_val.='</td>';
                $rep_val.='<td style="text-align: center">';
                $rep_val.=$i;
                $rep_val.='</td>';
                $rep_val.='</tr>';
            }
            $rep_val.='</tbody></table>';
        }else if($placeholder == 'Schedules Housing'){
            $rep_val = '<table class="table table-bordered">
            <tbody>
            <tr>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">بقیہ فائنانسنگ</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">کل ماہانہ ادائیگی</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">کرایہ پر ٹیکس</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">بنیادی&nbsp; کرایہ</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">متعین کرایہ</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">مہینہ&nbsp;</span><br></td>
                <td style="width: 20px;"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">سیریل نمبر</span><br></td>
            </tr>';


            $total_instalments = $model->inst_months;
            $loan_amount = $model->loan_amount;
            $disb_amount = $model->loan_amount;
            $disburse_date = date('Y-m-d', $model->readyTranche->cheque_date);
            //echo '<pre>';print_r($model->readyTranche->cheque_date);die();

            $schedule_amount = round(($loan_amount / $total_instalments));
            $total_instalments_count = $total_instalments;

            $service_charges = $model->service_charges;
            $tranche_charges = $service_charges;

            /*if ($model->disbursed_amount != $model->loan_amount) {
                $tranche_charges = $service_charges / 2;
            } else {
                $tranche_charges = $service_charges;
            }*/

            $charges_amount = round(($tranche_charges / $total_instalments_count));

            if ($disburse_date > date("Y-m-10", strtotime($disburse_date)) || in_array($model->project_id, StructureHelper::trancheProjects())) {
                $due_date = date("Y-m-10", strtotime('+1 month', strtotime(date("Y-m", strtotime($disburse_date)))));
            } else {
                $due_date = date("Y-m-10", strtotime($disburse_date));
            }

            $months = DisbursementHelper::getSchdlMonths()[$model->inst_type];
            $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
            $f = true;
            $sc_sum = 0;
            $balance=$disb_amount;
            for ($i = 1; $i <= $total_instalments_count; $i++) {
                $rep_val.='<tr>';
                if($i!=1){
                    $due_date = date("Y-m-10", strtotime("+" . $months . " months", strtotime($due_date)));
                }
                $diff = $tranche_charges - $sc_sum;

                if ($i == $total_instalments_count) {
                    if ($f) {
                        if ($diff == 0) {
                            $charges_schdl_amount = 0;
                        } else if ($diff < $charges_amount) {
                            $charges_schdl_amount = $diff;
                        } else {
                            $charges_schdl_amount = $tranche_charges - ($charges_amount * ($total_instalments_count - 1));
                        }
                        $schdl_amnt = $disb_amount - ($schedule_amount * ($total_instalments_count - 1));

                        $balance=$balance-$schdl_amnt;
                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($balance);
                        $rep_val.='</td>';
                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($charges_schdl_amount+$schdl_amnt+$tax_amount);
                        $rep_val.='</td>';

                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($tax_amount);
                        $rep_val.='</td>';

                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($schdl_amnt);
                        $rep_val.='</td>';
                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($charges_schdl_amount);
                        $rep_val.='</td>';

                    } else {
                        $charges_schdl_amount = $charges_amount;
                        $schdl_amnt = $schedule_amount;
                        $balance=$balance-$schdl_amnt;

                        if(in_array($model->project_id,[52,76])){
                            $tax_amount = round($charges_schdl_amount * ($gst_percentage / 100));
                        }else{
                            $tax_amount = 0;
                        }

                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($balance);
                        $rep_val.='</td>';
                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($charges_schdl_amount+$schdl_amnt+$tax_amount);
                        $rep_val.='</td>';

                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($tax_amount);
                        $rep_val.='</td>';

                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($schdl_amnt);
                        $rep_val.='</td>';
                        $rep_val.='<td  style="text-align: center">';
                        $rep_val.=number_format($charges_schdl_amount);
                        $rep_val.='</td>';
                    }
                } else {
                    if ($diff == 0) {
                        $charges_schdl_amount = 0;
                    } else if ($diff < $charges_amount) {
                        $charges_schdl_amount = $diff;
                    } else {
                        $charges_schdl_amount = $charges_amount;
                    }
                    $schdl_amnt = $schedule_amount;
                    $balance=$balance-$schdl_amnt;

                    if(in_array($model->project_id,[52,76])){
                        $tax_amount = round($charges_schdl_amount * ($gst_percentage / 100));
                    }else{
                        $tax_amount = 0;
                    }

                    $rep_val.='<td  style="text-align: center">';
                    $rep_val.=number_format($balance);
                    $rep_val.='</td>';
                    $rep_val.='<td  style="text-align: center">';
                    $rep_val.=number_format($charges_schdl_amount+$schdl_amnt+$tax_amount);
                    $rep_val.='</td>';

                    $rep_val.='<td  style="text-align: center">';
                    $rep_val.=number_format($tax_amount);
                    $rep_val.='</td>';


                    $rep_val.='<td  style="text-align: center">';
                    $rep_val.=number_format($schdl_amnt);
                    $rep_val.='</td>';
                    $rep_val.='<td  style="text-align: center">';
                    $rep_val.=number_format($charges_schdl_amount);
                    $rep_val.='</td>';
                }
                $sc_sum += $charges_schdl_amount;

                if ($i == 1) {
                    $charges_due_amount = $charges_schdl_amount;
                    $due_amnt = $schdl_amnt;
                }
                $rep_val.='<td  style="text-align: center">';
                $rep_val.=date('d-M-Y',strtotime($due_date));
                $rep_val.='</td>';
                $rep_val.='<td style="text-align: center">';
                $rep_val.=$i;
                $rep_val.='</td>';
                $rep_val.='</tr>';
            }
            $rep_val.='</tbody></table>';
        }else if($placeholder=='Project Wise Details'){
            $rep_val='
            <div class="fund-requests-form" style="color: rgb(52, 52, 52); font-size: 14.4px;">
                <table id="table-edit" class="table table-bordered table-hover" style="font-size:20px;width: 1116px;">
                    <thead>
                    <tr>
                        <th width="2"
                            style="padding: 10px 10px 9px; border-top: 1px solid rgb(216, 226, 231); border-right-color: rgb(222, 226, 230); border-bottom: none; border-left-color: rgb(222, 226, 230);">
                            No.
                        </th>
                        <th style="padding: 10px 10px 9px; border-top: 1px solid rgb(216, 226, 231); border-right-color: rgb(222, 226, 230); border-bottom: none; border-left-color: rgb(222, 226, 230);">
                            Project
                        </th>
                        <th style="padding: 10px 10px 9px; border-top: 1px solid rgb(216, 226, 231); border-right-color: rgb(222, 226, 230); border-bottom: none; border-left-color: rgb(222, 226, 230);">
                            No. of Loans
                        </th>
                        <th style="padding: 10px 10px 9px; border-top: 1px solid rgb(216, 226, 231); border-right-color: rgb(222, 226, 230); border-bottom: none; border-left-color: rgb(222, 226, 230);">
                            Amount
                        </th>
                    </tr>
                    </thead>
                    <tbody>';
            $d_count=1;
            $total_loans=0;
            $total_amount=0;
            foreach ($model->fundRequestDetails as $f) {

                $rep_val.='<tr>
                    <td style="padding-top: 11px; padding-right: 10px; padding-left: 10px; vertical-align: middle; border-color: rgb(216, 226, 231) rgb(222, 226, 230) rgb(222, 226, 230);">
                       
                       ';
                $rep_val.= $d_count;
                $rep_val.='
                    </td>
                    <td style="padding-top: 11px; padding-right: 10px; padding-left: 10px; vertical-align: middle; border-color: rgb(216, 226, 231) rgb(222, 226, 230) rgb(222, 226, 230);">
                        ';
                $rep_val.= \common\models\Projects::findOne($f["project_id"])->name;
                $rep_val.='
                    </td>
                    <td style="padding-top: 11px; padding-right: 10px; padding-left: 10px; vertical-align: middle; border-color: rgb(216, 226, 231) rgb(222, 226, 230) rgb(222, 226, 230);">
                        ';
                $rep_val.= number_format($f["total_loans"]);
                $rep_val.='
                    </td>
                    <td style="padding-top: 11px; padding-right: 10px; padding-left: 10px; vertical-align: middle; border-color: rgb(216, 226, 231) rgb(222, 226, 230) rgb(222, 226, 230);">
                        ';
                $rep_val.= number_format($f["total_requested_amount"]);
                $rep_val.='
                    </td>
                </tr>';
                $d_count++;
                $total_loans=$total_loans+$f["total_loans"];
                $total_amount=$total_amount+$f["total_requested_amount"];
            }
            $rep_val.='<tr>
                        <td></td>
                        <td><b>Total</b></td>
                        <td><b>';
            $rep_val.=$total_loans;
            $rep_val.='</b></td>
                       <td><b>';
            $rep_val.=number_format($total_amount);
            $rep_val.='</b></td>
                        </tr>';
            $rep_val.='
                    </tbody>
                </table>
            </div>
            <br style="color: rgb(52, 52, 52); font-size: 14.4px;">
            ';

        }else if($placeholder=='Signature'){
            $rep_val='
            <span style="color: rgb(52, 52, 52); font-size: 16px; font-weight: 700;">Printed By:&nbsp; &nbsp; &nbsp;</span><u style="color: rgb(52, 52, 52); font-size: 16px;">';
            $name = \common\models\Users::find()->select(['fullname'])->where(['id' => Yii::$app->user->getId()])->one();
            $rep_val.=$name->fullname;
            $rep_val.='
            &nbsp;</u><span style="color: rgb(52, 52, 52); font-size: 16px;">&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</span><span style="color: rgb(52, 52, 52); font-size: 16px; font-weight: 700;">Printed On:&nbsp; &nbsp; &nbsp;</span><u style="color: rgb(52, 52, 52); font-size: 16px;">';
            $rep_val.=date('j-M-Y');
            $rep_val.='&nbsp;</u><span style="color: rgb(52, 52, 52); font-size: 16px;">&nbsp; &nbsp;&nbsp;</span>';
        }
        return $rep_val;
    }

    static public function kamyabJawanPlaceholder($placeholder,$model)
    {
        $rep_val='';

            $rep_val = '<table class="table table-bordered">
            <tbody>
            <tr>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">بقیہ فائنانسنگ</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">کل ماہانہ ادائیگی</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">بنیادی&nbsp; کرایہ</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">متعین کرایہ</span><br></td>
                <td style="text-align: center"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">مہینہ&nbsp;</span><br></td>
                <td style="width: 20px;"><span lang="ER" dir="RTL" style="font-size:16.0pt;
        line-height:107%;font-family:&quot;Jameel Noori Nastaleeq&quot;;mso-fareast-font-family:
        Calibri;mso-fareast-theme-font:minor-latin;mso-ansi-language:EN-GB;mso-fareast-language:
        EN-US;mso-bidi-language:ER">سیریل نمبر</span><br></td>
            </tr>';

            $ledger = KamyabPakistanHelper::templateLedger($model);

            foreach ($ledger as $key=> $l) {
                $rep_val .= '<tr>';

                $rep_val .= '<td style="text-align: center">';
                $rep_val .= number_format($l['out_standing']);
                $rep_val .= '</td>';
                $rep_val .= '<td style="text-align: center">';
                $rep_val .= number_format($l['monthly_rental']);
                $rep_val .= '</td>';
                $rep_val .= '<td style="text-align: center">';
                $rep_val .= number_format($l['principle_amt']);
                $rep_val .= '</td>';
                $rep_val .= '<td style="text-align: center">';
                $rep_val .= number_format($l['rent']);
                $rep_val .= '</td>';
                $rep_val .= '<td style="text-align: center">';
                $rep_val .= date('d-M-Y', strtotime($l['due_date']));
                $rep_val .= '</td>';
                $rep_val .= '<td style="text-align: center">';
                $rep_val .= $key++;
                $rep_val .= '</td>';
                $rep_val .= '</tr>';
            }
            $rep_val.='</tbody></table>';
        return $rep_val;
    }
}