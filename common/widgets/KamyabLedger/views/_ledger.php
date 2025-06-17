<?php

use common\models\Loans;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */

/*echo '<pre>';
print_r(count($model->recoveries));
die("we die here");*/
$this->title = 'Loans';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .table td {
        height: 05px;
    }

    .padding-0 {
        padding-right: 0;
        padding-left: 0;
    }

    #printOnly {
        display: none;
    }

    @media print {
        #printOnly {
            display: block;
        }

        .side-menu {
            display: none;
        }

        #ledger {
            margin-left: 19%;
            width: 80%;
        }

        /*#start{
            margin-left:19%;
            width: 80%;
        }
        #end{
            margin-left:19%;
            width:80%;
            font-size:10px;
        }*/
    }

</style>
<div id="ledger" class="container-fluid">
    <div class="box-typical box-typical-padding">
        <div class="ledger-view">
            <?php if (!empty($model)) { ?>
            <div class="panel panel-success">
                <h4><b>Member's Legder</b></h4>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4 padding-0">
                            <div class="profile-card">
                                <div class="profile-card-photo">
                                    <?php
                                    $image = \common\components\Helpers\MemberHelper::getProfileImage($model->application->member_id);

                                    if (!empty($image)) {
                                        $profile_image = \common\components\Helpers\ImageHelper::getImageFromDisk('members', $model->application->member_id, $image->image_name, false);
                                        echo \yii\helpers\Html::img($profile_image, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'margin-left:-80%;margin-top:-30%;height:110px']);

                                    } else {
                                        if ($model->application->member->gender == 'm') {
                                            $pic_url = 'noimage.png';
                                        } else {
                                            $pic_url = 'noimage_female.jpg';
                                        }
                                        echo \yii\helpers\Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'margin-left:-80%;margin-top:-30%;height:110px']);
                                    }
                                    ?>
                                </div>
                            </div>
                            <p style="margin-top: -5%;margin-left: 10%">
                                        <span class="text-bold"><b><?php echo $model->sanction_no; ?></b>
                                            <?php
                                            $operation = \common\models\Operations::find()->where(['loan_id' => $model->id, 'operation_type_id' => 2])->one();
                                            if (!empty($operation)) {
                                                ?>
                                                <img src="https://img.icons8.com/color/25/000000/verified-account.png">
                                            <?php } ?></span>
                            </p>
                            <p style="margin-top: -5%;margin-left: 10%">
                                <b>Financing Status</b>
                                : <?php
                                if (in_array($model->status, ['not collected', 'rejected'])) {
                                    echo '<span style="color: red" color="red">Rejected</span>';
                                } elseif ($model->status == 'collected') {
                                    echo '<span style="color: green" color="red"><b>Active</b></span>';
                                } elseif ($model->status == 'loan completed') {
                                    echo '<span style="color: blue" color="red">Completed</span>';
                                } else {
                                    echo '<span style="color: red" color="red">Pending</span>';
                                }
                                ?>
                            </p>

                        </div><!--.col- -->
                        <div class="col-md-4 padding-0" style="line-height: 8px">
                            <div class="box-typical-inner">
                                <article class="profile-info-item">
                                    <header class="profile-info-item-header">
                                        <i class="font-icon font-icon-doc"></i>
                                        <b style="text-decoration: underline">Member Information</b>
                                    </header>
                                    <p>
                                        <b>Name</b>
                                        : <?= isset($model->application->member->full_name) ? $model->application->member->full_name : 'Not Set'; ?>

                                    </p>
                                    <p>
                                        <b>Parentage</b>
                                        : <?= isset($model->application->member->parentage) ? $model->application->member->parentage : 'Not Set'; ?>
                                    </p>
                                    <p>
                                        <b>Gender</b>
                                        : <?php if ($model->application->member->gender == 'm') {
                                            echo 'Male';
                                        } else if ($model->application->member->gender == 'f') {
                                            echo 'Female';
                                        } else {
                                            echo 'Transgender';
                                        } ?>
                                    </p>
                                    <p>
                                        <b>Area</b>
                                        : <?= isset($model->area->name) ? $model->area->name : 'Not Set'; ?>
                                    </p>
                                    <p>
                                        <b>Branch</b>
                                        : <?= isset($model->branch->name) ? $model->branch->name : 'Not Set'; ?>
                                    </p>
                                </article>
                            </div>
                            <!--.box-typical-->
                        </div><!--.col- -->
                        <div class="col-md-4 padding-0" style="line-height: 8px">
                            <div class="box-typical-inner">
                                <article class="profile-info-item">
                                    <header class="profile-info-item-header">
                                        <i class="font-icon font-icon-doc"></i>
                                        <b style="text-decoration: underline">Financing Information</b>
                                    </header>
                                    <p>
                                        <b>Group No</b>
                                        : <?= isset($model->application->group->grp_no) ? $model->application->group->grp_no : 'Not Set'; ?>
                                    </p>
                                    <p style="line-height: 15px">
                                        <b>Project</b>
                                        : <?= isset($model->project->name) ? $model->project->name : 'Not Set'; ?>
                                    </p>
                                    <p>
                                        <b>Purpose of Financing</b>
                                        : <?= isset($model->application->activity->name) ? $model->application->activity->name : 'Not Set' ?>
                                    </p>
                                    <p>
                                        <b>Disbursed on</b>
                                        : <?php
                                        $date = '';
                                        if (isset($model->disbTranches)) {
                                            $i = 1;
                                            foreach ($model->disbTranches as $t) {
                                                if ($i != 1) {
                                                    $date .= ' , ';
                                                }
                                                $date .= date('Y-M-d', $t->date_disbursed);

                                                $i++;
                                            }
                                        }
                                        echo $date;
                                        ?>
                                    </p>
                                    <p>
                                        <b>Risk Type</b>
                                        : Low Risk
                                    </p>
                                </article>
                            </div>
                        </div><!--.col- -->

                    </div>

                    <div class="row">
                        <table class="table table-hover table-bordered table-striped table-condensed text-size">
                            <thead>
                            <th style="background-color:lightgrey">Financing Amount</th>
                            <th style="background-color:lightgrey"><?= isset($model->loan_amount) ? number_format($model->loan_amount) : 'Not Set' ?></th>
                            <th style="background-color:lightgrey">No of Installments</th>
                            <th style="background-color:lightgrey"><?= isset($model->inst_months) ? number_format($model->inst_months) : 'Not Set' ?></th>
                            <th style="background-color:lightgrey">Mode of Payment</th>
                            <th style="background-color:lightgrey"><?= isset($model->inst_type) ? ucfirst($model->inst_type) : 'Not Set' ?></th>
                            </thead>
                            <tbody>
                            <td style="background-color:lightgrey"><b>Disb. Amount</b></td>
                            <td style="background-color:lightgrey">
                                <b><?= isset($model->disbursed_amount) ? number_format($model->disbursed_amount) : 'Not Set' ?></b>
                            </td>
                            <td style="background-color:lightgrey"><b>Total Fixed Rent</b></td>
                            <td style="background-color:lightgrey">
                                <b><?= isset($model->service_charges) ? number_format($model->service_charges) : 'Not Set' ?></b>
                            </td>
                            <td style="background-color:lightgrey"><b>No of Tranches</b></td>
                            <td style="background-color:lightgrey">
                                <b><?= isset($model->tranches) ? number_format(count($model->tranches)) : 'Not Set' ?></b>
                            </td>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $schedules = $model->schedules;
            if (!empty($schedules)) {

                ?>
                <table id="end" class="table table-hover table-bordered table-striped table-condensed text-size"
                       style="margin-top: 5px">
                    <thead class="table-heading">
                    <th colspan="1" rowspan="2" class="ladger-table-align  text-center"
                        style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                        Instal. No.
                    </th>
                    <th colspan="4" class="ladger-table-align table-border  text-center"
                        style="vertical-align: middle;background-color:lightgrey;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;">
                        Schedules
                    </th>
                    <th colspan="1" class="ladger-table-align  text-center"
                        style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                        Dues
                    </th>
                    <th colspan="7" class="ladger-table-align  text-center"
                        style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                        Recovery
                    </th>
                    <th colspan="2" rowspan="2" class="ladger-table-align  text-center"
                        style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                        Overdue
                    </th>
                    <th colspan="1" rowspan="2" class="ladger-table-align  text-center"
                        style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                        Outstanding Balance
                    </th>
                    <th colspan="1" rowspan="2" class="ladger-table-align  text-center"
                        style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                        Tranche Amount
                    </th>
                    <tr>
                        <th class="ladger-table-align table-border  text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"> <?php echo 'Base Rent'; ?></th>
                        <th class="ladger-table-align table-border  text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"> <?php echo 'Fixed Rent'; ?></th>
                        <th class="ladger-table-align table-border  text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"> <?php echo 'Tax'; ?></th>
                        <th class="ladger-table-align table-border  text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"> <?php echo 'Monthly Rental'; ?></th>
                        <th class="ladger-table-align text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"><?php echo 'Date'; ?></th>
<!--                        <th class="ladger-table-align text-center"-->
<!--                            style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">--><?php //echo 'Amount'; ?><!--</th>-->
                        <th class="ladger-table-align text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Date'; ?></th>
                        <th class="ladger-table-align text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Receipt No'; ?></th>
                        <th class="ladger-table-align text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Base Rent'; ?></th>
                        <th class="ladger-table-align text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Fixed Rent'; ?></th>
                        <th class="ladger-table-align table-border  text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"> <?php echo 'Tax'; ?></th>
                        <th class="ladger-table-align text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Total Rental'; ?></th>
                        <th class="ladger-table-align text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"><?php echo 'Adv. Rent'; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $count = 1;
                    //                    $outstanding = $model->disbursed_amount;
                    $charges_outstanding = $model->service_charges;
                    $credit_sum = 0;
                    $charges_sum = 0;
                    $schdl_sum = 0;
                    $schdl_charges_sum = 0;
                    $schdl_charges_tax_sum = 0;
                    $credit_tax_sum = 0;


                    foreach ($schedules as $key => $part) {
                        $sort[$key] = ($part['due_date']);
                    }
                    array_multisort($sort, SORT_ASC, $schedules);
                    $current_recovery = 0;
                    $overdue_recovery = 0;

                    $sch_overdue_total = 0;
                    $sch_tot = 0;

                    $outstanding = 0;
                    foreach ($schedules as $k => $s) {
                        $disbursedAmount = \common\components\Helpers\KamyabPakistanHelper::trancheAmount($s, $k);

                        if ($disbursedAmount != 0) {
                            $outstanding += $disbursedAmount;
                        }

                        if (!empty($s) && $s != null) {
                            $outstanding = $outstanding - $s->credit;
                        }

                        $charges_outstanding = $charges_outstanding - $s->charges_credit;
                        if ($s->credit > 0) {
                            $credit_sum = $credit_sum + $s->credit;
                        }
                        $charges_sum += $s->charges_credit;
                        $schdl_sum += $s->schdl_amnt;
                        $schdl_charges_sum += $s->charges_schdl_amount;
                        $schdl_charges_tax_sum += $s->charges_schdl_amnt_tax;
                        $credit_tax_sum += $s->credit_tax;

                        $recv_info = \common\models\Loans::getRecoveryInfo($s->id);

                        $sch_tot += ($s->schdl_amnt + $s->charges_schdl_amount);
                        $sch_overdue_total += $s->schdl_amnt;
                        foreach ($recv_info as $rec) {
                            $overdue_recovery += $rec->amount;
                            $current_recovery += $rec->amount;
                            $current_recovery += $rec->charges_amount;
                        }


                        $advance = $overdue_recovery - ($sch_overdue_total);
                        if (($advance) > 0) {
                            $advance_amount = $advance;
                        } else {
                            $advance_amount = 0;
                        }

                        ?>

                        <?php
                        $tranches = isset($model->tranches) ? number_format(count($model->tranches)) : 0;
                        if ($count == 1) { ?>
                            <tr>
                                <td style="background-color:#dff0d8" colspan="17" class="text-center">
                                    <b>Tranche No. 1</b>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($tranches == 2) {
                            if ($count == $model->inst_months / 2 + 1) {

                                ?>
                                <tr>
                                    <td style="background-color:#dff0d8" colspan="17" class="text-center">
                                        <b>Tranche No. 2</b>
                                    </td>
                                </tr>
                            <?php }
                        }
                        if ($tranches == 3) {

                            if ($count == $model->inst_months / 3 + 1) {

                                ?>
                                <tr>
                                    <td style="background-color:#dff0d8" colspan="17" class="text-center">
                                        <b>Tranche No. 2</b>
                                    </td>
                                </tr>
                            <?php } else if ($count == ($model->inst_months / 3) * 2 + 1) { ?>
                                <tr>
                                    <td style="background-color:#dff0d8" colspan="17" class="text-center">
                                        <b>Tranche No. 3</b>
                                    </td>
                                </tr>
                            <?php }
                        }
                        if ($tranches == 4) {

                            if ($count == $model->inst_months / 4 + 1) {

                                ?>
                                <tr>
                                    <td style="background-color:#dff0d8" colspan="17" class="text-center">
                                        <b>Tranche No. 2</b>
                                    </td>
                                </tr>
                            <?php } else if ($count == ($model->inst_months / 4) * 2 + 1) { ?>
                                <tr>
                                    <td style="background-color:#dff0d8" colspan="17" class="text-center">
                                        <b>Tranche No. 3</b>
                                    </td>
                                </tr>
                            <?php } else if ($count == ($model->inst_months / 4) * 3 + 1) { ?>
                                <tr>
                                    <td style="background-color:#dff0d8" colspan="17" class="text-center">
                                        <b>Tranche No. 4</b>
                                    </td>
                                </tr>
                            <?php }
                        } ?>

                        <tr>
                            <td class="text-center"
                                style="border-left: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"><?php echo $count; ?>
                            </td>
                            <td class="text-center table-border"
                                style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->schdl_amnt) ?>
                            </td>
                            <td class="text-center table-border"
                                style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->charges_schdl_amount) ?>
                            </td>
                            <td class="text-center table-border"
                                style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->charges_schdl_amnt_tax) ?>
                            </td>
                            <td class="text-center table-border"
                                style="border-right: solid 1px #0e7b45;">
                                <?php echo number_format($s->schdl_amnt + $s->charges_schdl_amount + $s->charges_schdl_amnt_tax) ?>
                            </td>
                            <td class="text-center" style="border-right: solid 1px #0e7b45;">
                                <?php echo date('d-M-y', ($s->due_date)) ?>
                            </td>
<!--                            <td class="text-center"-->
<!--                                style="border-right: solid 1px #0e7b45;">--><?php //echo number_format($s->due_amnt + $s->charges_due_amount) ?>
<!--                            </td>-->

                            <?php $recv_info = \common\components\Helpers\KamyabPakistanHelper::tentativeRecovery($model->id, $s->due_date); ?>

                            <td class="text-center">
                                <?php
                                if (!empty($recv_info) && $recv_info != null) {
                                    foreach ($recv_info as $r) {
                                        echo date('d-M-y', ($r->receive_date)) . "<br>";
                                    }
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if (!empty($recv_info) && $recv_info != null) {
                                    foreach ($recv_info as $r) {
                                        echo $r->receipt_no . "<br>";
                                    }
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $recv_credit_sum = 0;
                                if (!empty($recv_info) && $recv_info != null) {
                                    foreach ($recv_info as $r) {
                                        echo number_format($r->amount) . "<br>";
                                        $recv_credit_sum += ($r->amount);
                                    }
                                }
                                if (count($recv_info) > 1) {
                                    echo "<span style='font-weight: bold;'>" . number_format($recv_credit_sum) . "</span><br>";
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $recv_charges_sum = 0;
                                if (!empty($recv_info) && $recv_info != null) {
                                    foreach ($recv_info as $r) {
                                        echo number_format($r->charges_amount) . "<br>";
                                        $recv_charges_sum += $r->charges_amount;
                                    }
                                }
                                if (count($recv_info) > 1) {
                                    echo "<span style='font-weight: bold;'>" . number_format($recv_charges_sum) . "</span><br>";
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $recv_tax_sum = 0;
                                if (!empty($recv_info) && $recv_info != null) {
                                    foreach ($recv_info as $r) {
                                        if ($r->charges_amount > 0) {
                                            echo number_format($s->credit_tax) . "<br>";
                                            $recv_tax_sum += $s->credit_tax;
                                        }
                                    }
                                }
                                if (count($recv_info) > 1) {
                                    echo "<span style='font-weight: bold;'>" . number_format($recv_tax_sum) . "</span><br>";
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $recv_credit_sum = 0;
                                if (!empty($recv_info) && $recv_info != null) {
                                    foreach ($recv_info as $r) {
                                        if ($r->charges_amount == 0) {
                                            $taxAmount = number_format($r->charges_amount) . "<br>";
                                        } else {
                                            $taxAmount = $s->credit_tax;;
                                        }
                                        echo number_format($r->amount + $r->charges_amount + $taxAmount) . "<br>";
                                        $recv_credit_sum += $r->amount + $r->charges_amount + $taxAmount;
                                    }
                                }
                                if (count($recv_info) > 1) {
                                    echo "<span style='font-weight: bold;'>" . number_format($recv_credit_sum) . "</span><br>";
                                }
                                ?>
                            </td>
                            <td class="text-center"
                                style="border-right: solid 1px #0e7b45;">
                                0
<!--                                --><?php //if (!empty($recv_credit_sum)) {
//                                    echo number_format(abs(($s->schdl_amnt + $s->charges_schdl_amount + $s->charges_schdl_amnt_tax) - $recv_credit_sum));
//                                } ?>
                            </td>


                            <?php if (date('Y-m') >= date('Y-m', $s->due_date)) { ?>
                                <td class="text-center" colspan="2"
                                    style="border-right: solid 1px #0e7b45;"><?php echo ($advance < 0) ? abs($advance) : 0 ?></td>
                            <?php } else { ?>
                                <td class="text-center" colspan="2"
                                    style="border-right: solid 1px #0e7b45;"><?php echo 0 ?></td>
                            <?php } ?>
                            <?php
                            //                            if (!empty($recv_credit_sum)) {
                            //                                $outstanding = $outstanding - $recv_credit_sum;
                            //                            }
                            ?>
                            <td class="text-center"
                                style="border-right: solid 1px #0e7b45;"><?php echo number_format($outstanding) ?>
                            </td>
                            <?php
                            $tAmount = 0;
                            $loan = \common\models\Loans::find()->where(['sanction_no' => $model->sanction_no])->one();
                            $due_date = date("Y-m-10", strtotime('+2 month', strtotime(date("Y-m-01", $loan->date_disbursed))));

                            if (strtotime($due_date) == $s->due_date) {
                                $toDisbDate = strtotime(date('Y-m-t', strtotime($due_date)));
                                $firstTranches = \common\models\LoanTranches::find()->where(['loan_id' => $s->loan_id])
                                    ->andWhere(['>=', 'date_disbursed', $loan->date_disbursed])
                                    ->andWhere(['<=', 'date_disbursed', $toDisbDate])
                                    ->andWhere(['status' => 6])
                                    ->all();

                                if (!empty($tranches) && $tranches != null) {
                                    foreach ($firstTranches as $fTranche) {
                                        $tAmount = $tAmount + $fTranche->tranch_amount;
                                    }
                                }

                            } else {
                                $startDate = strtotime(date('Y-m-01', $s->due_date));
                                $endDate = strtotime(date('Y-m-t', $s->due_date));
                                $tranches = \common\models\LoanTranches::find()->where(['loan_id' => $s->loan_id])
                                    ->andWhere(['>=', 'date_disbursed', $startDate])
                                    ->andWhere(['<=', 'date_disbursed', $endDate])
                                    ->andWhere(['status' => 6])
                                    ->all();
                                if (!empty($tranches) && $tranches != null) {
                                    foreach ($tranches as $tranche) {
                                        $tAmount = $tAmount + $tranche->tranch_amount;
                                    }
                                }
                            }

                            ?>
                            <td class="text-center"
                                style="border-bottom: solid 1px #0e7b45;">
                                <b>
                                    <?php echo $tAmount; ?>
                                </b>
                            </td>
                        </tr>
                        <?php
                        $count++;
                    }
                    ?>
                    <tr class="success">
                        <td class="text-center"
                            style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                            <b>Total:</b></td>
                        <td class="ladger-table-align text-center"
                            style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                            <b><?php echo number_format($schdl_sum) ?></b></td>
                        <td class="ladger-table-align text-center"
                            style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                            <b><?php echo number_format($schdl_charges_sum) ?></b></td>
                        <td class="ladger-table-align text-center"
                            style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                            <b><?php echo number_format($schdl_charges_tax_sum) ?></b></td>
                        <td class="ladger-table-align text-center"
                            style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;">
                            <b><?php echo number_format($schdl_sum + $schdl_charges_sum + $schdl_charges_tax_sum) ?></b>
                        </td>
                        <td style="border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"></td>
                        <td style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"></td>
                        <td style="border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"></td>
                        <td class="text-center" style="border-bottom: solid 1px #0e7b45;"><b>Total: <b></td>
                        <td class="ladger-table-align text-center"
                            style="border-bottom: solid 1px #0e7b45;">
                            <b><?php echo number_format($credit_sum) ?></b></td>
                        <td class="ladger-table-align text-center"
                            style="border-bottom: solid 1px #0e7b45;">
                            <b><?php echo number_format($charges_sum) ?></b></td>
                        <td class="ladger-table-align text-center"
                            style="border-bottom: solid 1px #0e7b45;">
                            <b><?php echo number_format($credit_tax_sum) ?></b></td>
                        <td class="ladger-table-align text-center"
                            style="border-bottom: solid 1px #0e7b45;">
                            <b><?php echo number_format($credit_sum + $charges_sum + $credit_tax_sum) ?></b>
                        </td>
                        <td style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"></td>
                        <td style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"></td>
                        <td style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"></td>
                        <td class="ladger-table-align text-center"
                            style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;">
                            <b><?php echo number_format($model->disbursed_amount) ?></b>
                        </td>

                    </tr>
                    </tbody>
                </table>
                <?php
            } else {
                ?>
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <strong>Ledger not found!</strong>
                </div>
                <?php
            }
            ?>
            <div id="printOnly">
                <br>
                <br>
                <table border="0" cellpadding="0" cellspacing="0" style="margin-left:0%">

                    <tr>
                        <?php $name = \common\models\Users::find()->select(['fullname'])->where(['id' => Yii::$app->user->getId()])->one() ?>
                        <td style="border-left:white;border-bottom:white;">Printed By:
                            &nbsp;&nbsp;&nbsp;<b><u><?= !empty($name) ? $name->fullname : 'Not set' ?></u></b> &nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td style="border-left:white;border-bottom:white;border-right:white;margin-left: 50px">Printed
                            Date

                            &nbsp;&nbsp;&nbsp;<b><u><?= date('Y-M-j') ?></u></b> &nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                        <td style="border-left:white;border-bottom:white;border-right:white;margin-left: 50px">Reviewed
                            By:
                            _________________
                        </td>
                        <td style="border-left:white;border-bottom:white;border-right:white;">Verified By:
                            _________________
                        </td>
                    </tr>
                </table>
                <br><br>
                <span style="float:right;">
                        <?php
                        date_default_timezone_set('asia/karachi');
                        echo date("d-M-y  h:ia")
                        ?>
                    </span>
            </div>
        </div>
    </div>
</div>
<?php
} else {
    ?>
    <div class="alert alert-danger alert-dismissible fade in" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        <strong>Ledger not found!</strong>
    </div>
    <?php
}
?>

</div>