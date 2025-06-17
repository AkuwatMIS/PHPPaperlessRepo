<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */

/*echo '<pre>';
print_r(count($model->recoveries));
die("we die here");*/
$this->title = 'Loans';
$this->params['breadcrumbs'][] = $this->title;
?>

<div id="ledger" class="container-fluid">
    <div class="box-typical box-typical-padding">
        <div class="ledger-view" style="page-break-after: always;">
            <?php if (!empty($model)) { ?>
            <div class="panel panel-success">
                <h4><b>Member's Legder(<?php echo $model->sanction_no?>)</b></h4>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-3 padding-0">
                            <?= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    [
                                        'label' => 'Name',
                                        'attribute' => 'application.member.full_name',
                                        'format' => 'raw',

                                    ],
                                    [
                                        'label' => 'Parentage',
                                        'attribute' => 'application.member.parentage',
                                        'format' => 'raw',

                                    ],
                                    [
                                        'label' => 'CNIC',
                                        'attribute' => 'application.member.cnic',
                                        'format' => 'raw',

                                    ],
                                    [
                                        'attribute'=>'application.member.gender',
                                        'value'=>function($data){
                                            if ($data->application->member->gender == 'm') {
                                                return 'Male';
                                            }else if($data->application->member->gender == 'f'){
                                                return 'Female';
                                            }else{
                                                return 'Transgender';
                                            }

                                        },
                                    ],
                                    [
                                        'label' => 'Branch',
                                        'attribute' => 'branch.name'
                                    ],
                                    /*[
                                        'label' => 'Group No',
                                        'attribute' => 'application.group.grp_no',
                                    ],*/
                                    [
                                        'label' => 'Project',
                                        'attribute' => 'project.name'
                                    ],
                                    [
                                        'label' => 'Type of Business',
                                        'attribute' => 'application.activity.name'
                                    ],
                                    /*[
                                        'label' => 'Cheque Date',
                                        'attribute' => 'cheque_date',
                                        'value' => function($data) {
                                            $date = '';
                                            if (isset($data->processedTranches)) {
                                                $i = 1;
                                                foreach ($data->processedTranches as $t) {
                                                    if($i != 1)
                                                    {
                                                        $date .= ' , ';
                                                    }
                                                    $date .= date('Y-M-d',$t->cheque_date);

                                                    $i++;
                                                }
                                            }
                                            return $date;
                                        }
                                    ],*/
                                    [
                                        'label' => 'Disbursbursed on',
                                        'attribute' => 'date_disbursed',
                                        'value' => function($data) {
                                            $date = '';
                                            if (isset($data->disbTranches)) {
                                                $i = 1;
                                                foreach ($data->disbTranches as $t) {
                                                    if($i != 1)
                                                    {
                                                        $date .= ' , ';
                                                    }
                                                    $date .= date('Y-M-d',$t->date_disbursed);

                                                    $i++;
                                                }
                                            }
                                            return $date;
                                        }
                                    ],
                                    [
                                        'label' => 'Cheque No',
                                        'attribute' => 'cheque_no',
                                        'value' => function($data) {
                                            $cheque_no = '';
                                            if (isset($data->processedTranches)) {
                                                $i = 1;
                                                foreach ($data->processedTranches as $t) {
                                                    if($i != 1)
                                                    {
                                                        $cheque_no .= ' , ';
                                                    }
                                                    $cheque_no .= $t->cheque_no;

                                                    $i++;
                                                }
                                            }
                                            return $cheque_no;
                                        }
                                    ],
                                    [
                                        'label' => 'Loan Status',
                                        'attribute' => 'status',
                                        'format'=>'html',
                                        'value'=>function($model){
                                            if ($model->status == 'not collected') {
                                                return '<span style="color: red" color="red">Rejected</span>';
                                            } elseif ($model->status == 'collected') {
                                                return '<span style="color: green" color="red"><b>Active</b></span>';
                                            } elseif ($model->status == 'loan completed') {
                                                return '<span style="color: blue" color="red">Completed</span>';
                                            } else {
                                                return '<span style="color: red" color="red">Pending</span>';
                                            }
                                        }
                                    ],

                                ],
                            ]) ?>
                        </div>
                        <div class="col-md-3 padding-0">
                            <?= DetailView::widget([
                                'model' => $model,
                                'attributes' => [



                                ],
                            ]) ?>
                        </div>
                        <div class="col-md-3 padding-0">
                            <?= DetailView::widget([
                                'model' => $model,
                                'attributes' => [


                                ],
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <table class="table table-hover table-bordered table-striped table-condensed text-size">
                            <thead>
                            <tr>
                            <th style="background-color:lightgrey">Loan Amount</th>
                            <th style="background-color:lightgrey"><?= isset($model->loan_amount)?number_format($model->loan_amount):'Not Set' ?></th>
                            <th style="background-color:lightgrey">Disbursed Amount</th>
                            <th style="background-color:lightgrey"><?= isset($model->disbursed_amount)?number_format($model->disbursed_amount):'Not Set' ?></th>
                            <th style="background-color:lightgrey">No of Installments</th>
                            <th style="background-color:lightgrey"><?= isset($model->inst_months)?number_format($model->inst_months):'Not Set' ?></th>
                            <th style="background-color:lightgrey">Installment Amount</th>
                            <th style="background-color:lightgrey"><?= isset($model->inst_amnt)?number_format($model->inst_amnt):'Not Set' ?></th>
                            <th style="background-color:lightgrey">Mode of Payment</th>
                            <th style="background-color:lightgrey"><?= isset($model->inst_type)?ucfirst($model->inst_type):'Not Set' ?></th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                </div>
                <?php
                $schedules = $model->schedules;
                if (!empty($schedules)) {

                    ?>
                    <table class="table table-hover table-bordered table-striped table-condensed text-size"
                           style="margin-top: 5px;vertical-align: middle;">
                        <thead class="table-heading" style="vertical-align: middle">
                        <tr>
                        <th colspan="1"  rowspan="2" class="ladger-table-align"
                            style="background-color:lightgrey;vertical-align: middle;border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                            Instal. No.
                        </th>
                        <th colspan="4" class="ladger-table-align table-border text-center"
                            style="vertical-align: middle;background-color:lightgrey;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;">
                            Schedules
                        </th>
                        <th colspan="2" class="ladger-table-align text-center"
                            style="background-color:lightgrey;border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45">
                            Due
                        </th>
                        <th colspan="7" class="ladger-table-align text-center"
                            style="background-color:lightgrey;background-color:lightgrey; border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45">
                            Recovery
                        </th>
                        <th colspan="1" rowspan="2" class="ladger-table-align text-center"
                            style="background-color:lightgrey;vertical-align: middle;border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                            Overdue
                        </th>
                        <th colspan="3" rowspan="2" class="ladger-table-align text-center"
                            style="background-color:lightgrey;vertical-align: middle;border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                            Outstanding Balance
                        </th>
                        </tr>
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
                                style="vertical-align: middle;background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Date'; ?></th>
                            <th class="ladger-table-align text-center"
                                style="vertical-align: middle;background-color:lightgrey;border-right: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"><?php echo 'Amount'; ?></th>
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
                        $outstanding = $model->disbursed_amount;
                        if($model->disbursed_amount != $model->loan_amount) {
                            $charges_outstanding = $model->service_charges / 2;
                        } else {
                            $charges_outstanding = $model->service_charges;
                        }
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
                        $sch_tot=0;

                        foreach ($schedules as $s) {
                            $outstanding = $outstanding - $s->credit;
                            $charges_outstanding = $charges_outstanding - $s->charges_credit;
                            $credit_sum += $s->credit;
                            $charges_sum += $s->charges_credit;
                            $schdl_sum += $s->schdl_amnt;
                            $schdl_charges_sum += $s->charges_schdl_amount;
                            $schdl_charges_tax_sum += $s->charges_schdl_amnt_tax;
                            $credit_tax_sum += $s->credit_tax;
                            $recv_info = \common\models\Loans::getRecoveryInfo($s->id);

                            $sch_tot+=$s['schdl_amnt'] + $s['charges_schdl_amount'];
                            foreach ($recv_info as $rec){
                                $current_recovery+=$rec['amount'];
                                $current_recovery+=$rec['charges_amount'];
                            }

                            $advance = $current_recovery - ($sch_tot);
                            if(($advance) > 0){
                                $advance_amount = $advance;
                            }else{
                                $advance_amount = 0;
                            }

                            ?>

                            <?php
                            $tranches = isset($model->tranches)?number_format(count($model->tranches)):0;
                            if ($count == 1) { ?>
                                <tr>
                                    <td style="background-color:#dff0d8" colspan="16" class="text-center">
                                        <b>Tranche No. 1</b>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if($tranches == 2) {
                                if ($count == $model->inst_months / 2 + 1) {

                                    ?>
                                    <tr>
                                        <td style="background-color:#dff0d8" colspan="16" class="text-center">
                                            <b>Tranche No. 2</b>
                                        </td>
                                    </tr>
                                <?php }} if($tranches == 3) {

                                if ($count == $model->inst_months / 3 + 1) {

                                    ?>
                                    <tr>
                                        <td style="background-color:#dff0d8" colspan="16" class="text-center">
                                            <b>Tranche No. 2</b>
                                        </td>
                                    </tr>
                                <?php } else if($count == ($model->inst_months /3)*2+1) {?>
                                    <tr>
                                        <td style="background-color:#dff0d8" colspan="16" class="text-center">
                                            <b>Tranche No. 3</b>
                                        </td>
                                    </tr>
                                <?php }} if($tranches == 4) {

                                if ($count == $model->inst_months / 4 + 1) {

                                    ?>
                                    <tr>
                                        <td style="background-color:#dff0d8" colspan="16" class="text-center">
                                            <b>Tranche No. 2</b>
                                        </td>
                                    </tr>
                                <?php } else if($count == ($model->inst_months /4)*2+1) {?>
                                    <tr>
                                        <td style="background-color:#dff0d8" colspan="16" class="text-center">
                                            <b>Tranche No. 3</b>
                                        </td>
                                    </tr>
                                <?php } else if($count == ($model->inst_months /4)*3+1) {?>
                                    <tr>
                                        <td style="background-color:#dff0d8" colspan="16" class="text-center">
                                            <b>Tranche No. 4</b>
                                        </td>
                                    </tr>
                                <?php }} ?>

                            <tr>
                                <td class="text-center"
                                    style="border-left: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"><?php echo $count; ?></td>
                                <td class="text-center table-border"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->schdl_amnt) ?></td>
                                <td class="text-center table-border"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->charges_schdl_amount) ?></td>
                                <td class="text-center table-border"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->charges_schdl_amnt_tax) ?></td>
                                <td class="text-center table-border"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->schdl_amnt + $s->charges_schdl_amount + $s->charges_schdl_amnt_tax) ?></td>
                                <td class="text-center">
                                    <?php echo date('d-M-y', ($s->due_date)) ?>
                                </td>
                                <td class="text-center"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->due_amnt + $s->charges_due_amount) ?></td>
                                <td class="text-center">
                                    <?php
                                    foreach ($recv_info as $r) {
                                        echo date('d-M-y', ($r->receive_date)) . "<br>";
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    foreach ($recv_info as $r) {
                                        echo $r->receipt_no . "<br>";
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $recv_credit_sum = 0;
                                    foreach ($recv_info as $r) {
                                        echo number_format($r->amount) . "<br>";
                                        $recv_credit_sum += ($r->amount);
                                    }
                                    //echo number_format($recv_credit_sum)."<br>";
                                    //echo ($recv_credit_sum != $s->credit) ? "<span class='text-bold'>".number_format($recv_credit_sum)."</span><br>" : '' ;
                                    if (count($recv_info) > 1) {
                                        echo "<span style='font-weight: bold;'>" . number_format($recv_credit_sum) . "</span><br>";
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $recv_charges_sum = 0;
                                    foreach ($recv_info as $r) {
                                        echo number_format($r->charges_amount) . "<br>";
                                        $recv_charges_sum += $r->charges_amount;
                                    }
                                    //echo number_format($recv_credit_sum)."<br>";
                                    //echo ($recv_credit_sum != $s->credit) ? "<span class='text-bold'>".number_format($recv_credit_sum)."</span><br>" : '' ;
                                    if (count($recv_info) > 1) {
                                        echo "<span style='font-weight: bold;'>" . number_format($recv_charges_sum) . "</span><br>";
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $recv_tax_sum = 0;
                                    echo number_format($s->credit_tax ) . "<br>";
                                    $recv_tax_sum += $s->credit_tax;
                                    foreach ($recv_info as $r) {
                                        if($r->charges_amount == 0){
                                            echo number_format($r->charges_amount) . "<br>";
                                        }
                                    }
                                    //echo number_format($recv_credit_sum)."<br>";
                                    //echo ($recv_credit_sum != $s->credit) ? "<span class='text-bold'>".number_format($recv_credit_sum)."</span><br>" : '' ;
                                    if (count($recv_info) > 1) {
                                        echo "<span style='font-weight: bold;'>" . number_format($recv_tax_sum) . "</span><br>";
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $recv_credit_sum = 0;
                                    foreach ($recv_info as $r) {
                                        if($r->charges_amount == 0){
                                            $taxAmount = number_format($r->charges_amount) . "<br>";
                                        } else {
                                            $taxAmount = $s->credit_tax;;
                                        }
                                        echo number_format($r->amount + $r->charges_amount + $taxAmount) . "<br>";
                                        $recv_credit_sum += $r->amount + $r->charges_amount + $taxAmount;
                                    }
                                    //echo number_format($recv_credit_sum)."<br>";
                                    //echo ($recv_credit_sum != $s->credit) ? "<span class='text-bold'>".number_format($recv_credit_sum)."</span><br>" : '' ;
                                    if (count($recv_info) > 1) {
                                        echo "<span style='font-weight: bold;'>" . number_format($recv_credit_sum) . "</span><br>";
                                    }
                                    ?>
                                </td>
                                <td class="text-center"
                                    style="border-right: solid 1px #0e7b45;">
                                    <?php echo number_format($advance_amount) ?>
                                    <?php /*echo number_format($s->advance_log) */ ?>
                                </td>
                                <!--<td class="text-center"
                                style="border-right: solid 1px #0e7b45;"><?php /*echo number_format($s->overdue_log) */?></td>
                            <td class="text-center"
                                style="border-right: solid 1px #0e7b45;"><?php /*echo number_format($s->overdue) */?></td>-->
                                <?php if(date('Y-m')>=date('Y-m',$s->	due_date)) {?>
                                    <td class="text-center"
                                        style="border-right: solid 1px #0e7b45;"><?php /*echo number_format($s->overdue_log + $s->overdue)*/ echo ($advance<0)?abs($advance):0 ?></td>
                                <?php }else{ ?>
                                    <td class="text-center"
                                        style="border-right: solid 1px #0e7b45;"><?php /*echo number_format($s->overdue_log + $s->overdue)*/ echo 0 ?></td>
                                <?php } ?>
                                <!--<td class="text-center"
                                style="border-right: solid 1px #0e7b45;"><?php /*echo number_format($outstanding) */?></td>
                            <td class="text-center"
                                style="border-right: solid 1px #0e7b45;"><?php /*echo number_format($charges_outstanding) */?></td>-->
                                <td class="text-center"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($outstanding) ?></td>
                            </tr>
                            <?php
                            $count++;
                        }
                        ?>
                        <tr class="success">
                            <td class="text-center" style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"><b>Total:</b></td>
                            <td class="ladger-table-align text-center" style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"> <b><?php echo number_format($schdl_sum) ?></b></td>
                            <td class="ladger-table-align text-center" style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"> <b><?php echo number_format($schdl_charges_sum) ?></b></td>
                            <td class="ladger-table-align text-center" style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"> <b><?php echo number_format($schdl_charges_tax_sum) ?></b></td>
                            <td class="ladger-table-align text-center" style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"> <b><?php echo number_format($schdl_sum + $schdl_charges_sum + $schdl_charges_tax_sum) ?></b></td>
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
                                <b><?php echo number_format($credit_sum + $charges_sum + $credit_tax_sum) ?></b></td>
                            <td style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"></td>
                            <td style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"></td>
                            <td style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"></td>

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