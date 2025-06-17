<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Loans */
$this->title = 'Create Loans';
$this->params['breadcrumbs'][] = ['label' => 'Loans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Loan Approval committee (LAC)</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?= $this->render('_search_group', [
            'application' => $application,
        ]) ?>
    </div>
    <?php if(!empty($applications)){ ?>
    <div class="box-typical box-typical-padding">
        <table id="table-edit" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th width="1">#</th>
                <th>Application No</th>
                <th>Name</th>
                <th>Parentage</th>
                <th>CNIC</th>
                <th>Sanction No</th>
                <th>Req Amount</th>
                <th>Loan Amount</th>
                <th>Activity</th>
                <th>Project</th>
                <th>Product</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1;
            foreach ($applications as $a) { ?>
                <tr>
                    <td><?= $i ?></td>
                    <?php $_loan=\common\models\Loans::find()->where(['application_id'=>$a->id])->one();?>
                    <td><?= $a->application_no ?></td>
                    <td><?= $a->member->full_name ?></td>
                    <td><?= $a->member->parentage ?></td>
                    <td><?= $a->member->cnic ?></td>
                    <th><?= !empty($_loan) ? $_loan->sanction_no : 'YTBA' ?></th>
                    <td><?= number_format($a->req_amount) ?></td>
                    <th><?= !empty($_loan) ? number_format($_loan->loan_amount) : 'YTBA' ?></th>
                    <td><?= isset($a->activity->name) ? $a->activity->name : '' ?></td>
                    <td><?= $a->project->name ?></td>
                    <td><?= $a->product->name ?></td>
                    <td>
                       <?php $loan=\common\models\Loans::find()->where(['application_id'=>$a->id,'deleted'=>0])->one();?>
                        <?php if(empty($loan)){ ?>
                            <?php if(in_array($a->project_id,\common\components\Helpers\StructureHelper::kamyaabPakitanProjects())){
                                $pmtStatus = \common\components\Helpers\LoanHelper::PmtVerify($a);
                                if($pmtStatus==0){ ?>
                                <?php }elseif($pmtStatus>=1 && $pmtStatus<=4){ ?>
                                    <a href="/loans/create?id=<?= $a->id ?>" class="btn btn-success btn-sm">
                                        <i class="fa fa-plus" role='modal-remote' data-toggle='tooltip' title="Create Loan"></i>
                                    </a>
                                <?php }else{ ?>
                                <?php }?>
                            <?php }else{?>
                                <a href="/loans/create?id=<?= $a->id ?>" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus" role='modal-remote' data-toggle='tooltip' title="Create Loan"></i>
                                </a>
                            <?php } ?>
                        <?php }elseif($loan->date_disbursed==0){ ?>
                            <a href="/loans/update?id=<?= $loan->id ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-pencil" role='modal-remote' data-toggle='tooltip' title="Edit Loan"></i>
                            </a>
                        <?php } ?>
                        <?php if(in_array($loan->project_id,\common\components\Helpers\StructureHelper::trancheProjects())){?>
                            <a href="/loans/update-tranch?id=<?= $loan->id ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-send" role='modal-remote' data-toggle='tooltip' title="Update Tranch"></i>
                            </a>
                        <?php }?>
                    </td>
                </tr>
                <?php $i++;
            } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>

</div>
