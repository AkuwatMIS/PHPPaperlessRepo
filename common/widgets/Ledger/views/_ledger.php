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
<style>
    .table td {
        height: 05px;
    }
    .padding-0 {
        padding-right: 0;
        padding-left: 0;
    }
    #printOnly {
        display : none;
    }

    @media print {
        #printOnly {
            display : block;
        }
        .side-menu{
            display : none;
        }
        #ledger{
            margin-left:19%;
            width: 80%;
        }
    }

</style>
<div id="ledger" class="container-fluid">
    <div class="box-typical box-typical-padding">
        <div class="ledger-view">
            <?php if (!empty($model)) { ?>
            <div class="panel panel-success">
                <h4><b>Member's Legder</b></h4>
                <!--<div class="panel-heading">
                    <span class="text-bold"></span>
                    <span class="pull-right"><b>Sanction No: </b><span
                                class="text-bold"><b><?php /*echo $model->sanction_no; */?></b></span></span>
                </div>-->

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4 padding-0">
                            <!--<div class="box-typical-inner">-->
                            <!--<article class="profile-info-item">
                                    <header class="profile-info-item-header">
                                        <i class="font-icon font-icon-doc"></i>
                                        <b>Sanction No: </b><span
                                                class="text-bold"><b><?php /*echo $model->sanction_no; */?></b><?php
                            /*                                            $operation = \common\models\Operations::find()->where(['loan_id' => $model->id, 'operation_type_id' => 2])->one();
                                                                        if (!empty($operation)) {
                                                                            */?>
                                                <img src="https://img.icons8.com/color/25/000000/verified-account.png">
                                            <?php /*} */?></span>
                                    </header>-->
                            <div class="profile-card">
                                <div class="profile-card-photo">
                                    <?php
                                    $image = \common\components\Helpers\MemberHelper::getProfileImage($model->application->member_id);

                                    if (!empty($image)) {
                                        //$user_image = (!empty($image->image_name)) ? ($image->image_name) : 'profile_pic.png';
                                        //$pic_url =  $model->id. "/" .$image->parent_type ."/" . $user_image;
                                        //$pic_url = $image->parent_type . "/" . $model->application->member_id . "/" . $user_image;

                                        $profile_image=\common\components\Helpers\ImageHelper::getImageFromDisk('members',$model->application->member_id,$image->image_name,false);
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



                                    <!--<?php /*echo Html::img('@web/uploads/' . 'noimage.png', ['alt' => Yii::$app->name]); */ ?>-->
                                </div>
                            </div>
                            <p style="margin-top: -5%;margin-left: 10%">
                                        <span  class="text-bold"><b><?php echo $model->sanction_no; ?></b>
                                            <?php
                                            $operation = \common\models\Operations::find()->where(['loan_id' => $model->id, 'operation_type_id' => 2])->one();
                                            if (!empty($operation)) {
                                                ?>
                                                <img src="https://img.icons8.com/color/25/000000/verified-account.png">
                                            <?php } ?></span>
                            </p>
                            <p style="margin-top: -5%;margin-left: 10%">
                                <b>Loan Status</b>
                                : <?php
                                if (in_array($model->status,['not collected','rejected'])) {
                                    echo '<span style="color: red" color="red">Rejected</span>';
                                } elseif ($model->status == 'collected') {
                                    echo '<span style="color: green" color="red"><b>Active</b></span>';
                                } elseif ($model->status == 'loan completed') {
                                    echo '<span style="color: blue" color="red">Completed</span>';
                                }elseif ($model->status == 'grant') {
                                    echo '<span style="color: #ff7e05" color="red">Grant</span>';
                                } else {
                                    echo '<span style="color: red" color="red">Pending</span>';
                                }
                                ?>
                            </p>

                            <!--</article>
                        </div>-->

                        </div><!--.col- -->
                        <div class="col-md-4 padding-0"  style="line-height: 8px">
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
                                        }else if($model->application->member->gender == 'f'){
                                            echo 'Female';
                                        }else{
                                            echo 'Transgender';
                                        }?>
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
                        <div class="col-md-4 padding-0"   style="line-height: 8px">
                            <div class="box-typical-inner">
                                <article class="profile-info-item">
                                    <header class="profile-info-item-header">
                                        <i class="font-icon font-icon-doc"></i>
                                        <b style="text-decoration: underline">Loan Information</b>
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
                                        <b>Purpose of Loan</b>
                                        : <?= isset($model->application->activity->name)?$model->application->activity->name:'Not Set'?>
                                    </p>
                                    <p>
                                        <b>Cheque No</b>
                                        : <?php
                                        $cheque_no='';
                                        if (isset($model->processedTranches)) {
                                            $i = 1;
                                            foreach ($model->processedTranches as $t) {
                                                if($i != 1)
                                                {
                                                    $cheque_no .= ' , ';
                                                }
                                                $cheque_no .= $t->cheque_no;

                                                $i++;
                                            }
                                        }
                                        echo $cheque_no;
                                        ?>
                                    </p>
                                    <p>
                                        <b>Disbursed on</b>
                                        : <?php
                                        $date = '';
                                        if (isset($model->disbTranches)) {
                                            $i = 1;
                                            foreach ($model->disbTranches as $t) {
                                                if($i != 1)
                                                {
                                                    $date .= ' , ';
                                                }
                                                $date .= date('Y-M-d',$t->date_disbursed);

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
                    <!--<div class="row">
                        <div class="col-md-3 padding-0">
                            <?/*= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    [
                                        'label' => 'Member',
                                        'attribute' => 'application.member.full_name',
                                        'format' => 'raw',

                                    ],
                                    [
                                        'label' => 'Group No',
                                        'attribute' => 'application.group.grp_no',
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
                                    ],[
                                        'label' => 'Branch',
                                        'attribute' => 'branch.name'
                                    ]

                                ],
                            ]) */?>
                        </div>
                        <div class="col-md-3 padding-0">
                            <?/*= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    'application.member.parentage',
                                    [
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
                                    ],
                                    [
                                        'label' => 'Date of Disburs.',
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
                                        'label' => 'Project',
                                        'attribute' => 'project.name'
                                    ]

                                ],
                            ]) */?>
                        </div>
                        <div class="col-md-3 padding-0">
                            <?/*= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
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
                                        'label' => 'Loan Amount',
                                        'attribute' => 'loan_amount',
                                        'value'=>function($data){return number_format($data->loan_amount);},
                                    ],
                                    [
                                        'label' => 'Mode of Payment',
                                        'attribute' => 'inst_type',
                                    ],
                                    [
                                        'label' => 'Status',
                                        'attribute' => 'status',
                                        'format'=>'html',
                                        'value'=>function($data){
                                               if($data->status=='loan_completed'){
                                                   return '<b style="color: green">'.$data->status.'</b>';
                                               }
                                               else if($data->status=='collected'){
                                                  return '<b style="color: blue">'. $data->status.'</b>';
                                               }
                                               else{
                                                   return '<b style="color: red">'.$data->status.'</b>';
                                               }
                                        }
                                    ],

                                ],
                            ]) */?>
                        </div>
                        <div class="col-md-3 padding-0">
                            <?/*= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    [
                                        'label' => 'Type of Business',
                                        'attribute' => 'application.activity.name'
                                    ],
                                    [
                                        'label' => 'Period of credit',
                                        'attribute' => 'inst_months',
                                        'value'=>function($data){return number_format($data->inst_months);}
                                    ],
                                    [
                                        'label' => 'Installments',
                                        'attribute' => 'inst_months',
                                        'value'=>function($data){return number_format($data->inst_months);}
                                    ],
                                    [
                                        'label' => 'Installment Amount',
                                        'attribute' => 'inst_amnt',
                                        'value'=>function($data){return number_format($data->inst_amnt);}
                                    ],
                                ],
                            ]) */?>
                        </div>
                    </div>-->
                    <div class="row">
                        <table class="table table-hover table-bordered table-striped table-condensed text-size">
                            <thead>
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
                        <th colspan="1"  rowspan="2" class="ladger-table-align"
                            style="background-color:lightgrey;vertical-align: middle;border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;">
                            Instal. No.
                        </th>
                        <th colspan="1" rowspan="2" class="ladger-table-align table-border text-center"
                            style="background-color:lightgrey;vertical-align: middle;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;">
                            Schedule Amount
                        </th>
                        <th colspan="2" class="ladger-table-align text-center"
                            style="background-color:lightgrey;border-right: solid 1px #0e7b45;border-top: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45">
                            Due
                        </th>
                        <th colspan="4" class="ladger-table-align text-center"
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
                        <tr>
                            <!--<th class="ladger-table-align table-border"
                            style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"> <?php /*echo 'Amount'; */?></th>-->
                            <th class="ladger-table-align text-center"
                                style="background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Date'; ?></th>
                            <th class="ladger-table-align text-center"
                                style="background-color:lightgrey;border-right: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"><?php echo 'Amount'; ?></th>
                            <th class="ladger-table-align text-center"
                                style="background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Date'; ?></th>
                            <th class="ladger-table-align text-center"
                                style="background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Receipt No'; ?></th>
                            <th class="ladger-table-align text-center"
                                style="background-color:lightgrey;border-bottom: solid 1px #0e7b45;"><?php echo 'Amount'; ?></th>
                            <th class="ladger-table-align text-center"
                                style="background-color:lightgrey;border-right: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"><?php echo 'Adv. Amount'; ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 1;
                        $outstanding = $model->disbursed_amount;
                        $credit_sum = 0;


                        foreach ($schedules as $key => $part) {
                            $sort[$key] = ($part['due_date']);
                        }
                        array_multisort($sort, SORT_ASC, $schedules);
                        foreach ($schedules as $s) {
                            $outstanding = $outstanding - $s->credit;
                            $credit_sum += $s->credit;
                            $recv_info = \common\models\Loans::getRecoveryInfo($s->id);
                            ?>
                            <tr>
                                <td class="text-center"
                                    style="border-left: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"><?php echo $count; ?></td>
                                <td class="text-center table-border"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->schdl_amnt) ?></td>
                                <td class="text-center"><?php echo date('d-M-y', ($s->due_date)) ?></td>
                                <td class="text-center"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->due_amnt) ?></td>
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
                                        $recv_credit_sum += $r->amount;
                                    }
                                    //echo number_format($recv_credit_sum)."<br>";
                                    //echo ($recv_credit_sum != $s->credit) ? "<span class='text-bold'>".number_format($recv_credit_sum)."</span><br>" : '' ;
                                    if (count($recv_info) > 1) {
                                        echo "<span style='font-weight: bold;'>" . number_format($recv_credit_sum) . "</span><br>";
                                    }
                                    ?>
                                </td>
                                <td class="text-center"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->advance_log) ?></td>
                                <td class="text-center"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($s->overdue_log) ?></td>
                                <td class="text-center"
                                    style="border-right: solid 1px #0e7b45;"><?php echo number_format($outstanding) ?></td>
                            </tr>
                            <?php
                            $count++;
                        }
                        ?>
                        <tr class="success">
                            <td style="border-right: solid 1px #0e7b45;border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"></td>
                            <td style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"></td>
                            <td style="border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"></td>
                            <td style="border-bottom: solid 1px #0e7b45;border-right: solid 1px #0e7b45;"></td>
                            <td style="border-left: solid 1px #0e7b45;border-bottom: solid 1px #0e7b45;"></td>
                            <td class="text-center" style="border-bottom: solid 1px #0e7b45;"><b>Total: <b></td>
                            <td class="ladger-table-align text-center"
                                style="border-bottom: solid 1px #0e7b45;">
                                <b><?php echo number_format($credit_sum) ?></b></td>
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
                    <table border="0" cellpadding="0" cellspacing="0" style="margin-left:0%">

                        <tr>
                            <?php $name=\common\models\Users::find()->select(['fullname'])->where(['id'=>Yii::$app->user->getId()])->one()?>
                            <td style="border-left:white;border-bottom:white;">Printed By:
                                &nbsp;&nbsp;&nbsp;<b><u><?= !empty($name)?$name->fullname:'Not set'?></u></b> &nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                            <td style="border-left:white;border-bottom:white;border-right:white;margin-left: 50px">Printed Date

                                &nbsp;&nbsp;&nbsp;<b><u><?= date('Y-M-j')?></u></b> &nbsp;&nbsp;&nbsp;&nbsp;
                            </td>
                            <td style="border-left:white;border-bottom:white;border-right:white;margin-left: 50px">Reviewed By:
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