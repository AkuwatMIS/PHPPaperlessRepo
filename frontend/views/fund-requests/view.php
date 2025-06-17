<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\components\Helpers\UsersHelper;
/* @var $this yii\web\View */
/* @var $model common\models\FundRequests */
/* @var $form yii\widgets\ActiveForm */
/*echo '<pre>';
print_r($fund_request_detail);
print_r($fund_requests_details);
die();*/
//$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fund Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
$js="
$('body').on('beforeSubmit', 'form.remove-form', function () {
 if(confirm(\"Are you sure you want to remove this loan from fund request ?\")){
        return true;
    }else{
        event.preventDefault();
    }
});
";
$this->registerJs($js);
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>RM Fund Request Approval</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <h4><b>Branch Details (<?= $model->branch->name.'/ Code: '.$model->branch->code.')'?></b></h4>
        <div class="fund-requests-form">
            <?php $form = ActiveForm::begin(); ?>
            <?php if (!Yii::$app->request->isAjax) { ?>
                <?php if(in_array('frontend_processedfundrequests',$permissions))
                { ?>
                <div class="form-group">
                    <?php if($model->status=='pending'){ ?>
                        <?= \yii\helpers\Html::button('Approve/Reject', [
                            'class' =>'btn btn-success pull-right',
                            'id' => 'BtnModalId',
                            'style'=>'margin-top: 30px',
                            'data-toggle'=> 'modal',
                            'data-target'=> '#your-modal',

                        ]) ?>
                        <!--<?/*= Html::submitButton('Approve Request', ['class' => 'btn btn-success pull-right']) */?>-->
                        <?php ?>
                    <?php }else if($model->status=='approved') { ?>
                        <?= Html::submitButton('Approved', ['disabled'=>true,'class' => 'btn btn-success pull-right glyphicon glyphicon-ok']) ?>
                    <?php }else if($model->status=='rejected') { ?>
                        <?= Html::submitButton('Rejected', ['disabled'=>true,'class' => 'btn btn-success pull-right glyphicon glyphicon-remove']) ?>
                    <?php }?>
                </div>

            <?php } }?>
            <br>
        <br>
            <table id="table-edit" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="2">No.</th>
                    <th>Project</th>
                    <th>No. of Loans</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                $total_loans = 0;
                $total_amount = 0;
                $branch_id = 0;
                foreach ($model->fundRequestDetails as $f) { ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= \common\models\Projects::findOne($f['project_id'])->name ?></td>
                        <td><?= number_format($f['total_loans']) ?></td>
                        <td><?= number_format($f['total_requested_amount']) ?></td>
                    </tr>
                    <?php
                    $i++;
                    $total_loans += $f['total_loans'];
                    $total_amount += $f['total_requested_amount'];
                    $branch_id = $f['branch_id'];
                } ?>
                <th></th>
                <th>Total:</th>
                <th><?= number_format($total_loans) ?></th>
                <th><?= number_format($total_amount) ?></th>
                </tbody>
            </table>

            <?php if ($model->status == 'pending') { ?>
                <?php

                \yii\bootstrap\Modal::begin([

                    'header' => '',

                    'id' => 'your-modal',

                    'size' => 'modal-md',

                ]);?>
                <?php $branch = \common\models\Branches::findOne($branch_id); ?>
                <?= $form->field($model, 'status')->dropDownList(['approved'=>'Approved','rejected'=>'Rejected'],['prompt'=>'Select Status','required'=>true])->label(false) ?>
                <?= $form->field($model, 'approved_by')->hiddenInput(['value' => Yii::$app->user->getId()])->label(false) ?>
                <?= $form->field($model, 'approved_on')->hiddenInput(['value' => time()])->label(false);?>
               <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
               <?php
                \yii\bootstrap\Modal::end();
                ?>
            <?php } ?>
            <?php ActiveForm::end(); ?>
        </div>
        <br>
        <br>

        <section class="box-typical">
            <article class="profile-info-item">
                <header class="profile-info-item-header">
                    <i class="font-icon font-icon-award"></i>
                    Fund Request History
                </header>
                <?php

                //die();
                ?>
                <div class="box-typical-inner">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Action</th>
                                <th>User</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if($model->created_by!=0){?>
                                <tr>
                                    <td>
                                        <?php echo ucfirst('Created') ?>
                                        <br>
                                        <p style="font-size: 10px;color: green;">
                                           <?= ($model->created_at != 0) ? date('d M Y H:i', $model->created_at) : '-' ?>
                                        </p>
                                    </td>
                                    <td><?php echo $model->createUser->fullname.' ('.UsersHelper::getRole($model->created_by).')' ?></td>
                                    <td><?php echo ($model->created_by != 0) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                            <?php }?>
                            <?php if($model->approved_by!=0){?>
                                <tr>
                                    <td>
                                        <?php echo ucfirst('Recommended') ?>
                                        <br>
                                        <p style="font-size: 10px;color: green;">
                                           <?= ($model->approved_on != 0) ? date('d M Y H:i', $model->approved_on) : '-' ?>
                                        </p>
                                    </td>
                                    <td><?php echo $model->recommendUser->fullname.' ('.UsersHelper::getRole($model->approved_by).')'; ?></td>
                                    <td><?php echo ($model->approved_by != 0) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                            <?php }?>
                            <?php if($model->processed_by!=0){?>
                                <tr>
                                    <td>
                                        <?php echo ucfirst('Processed') ?>
                                        <br>
                                        <p style="font-size: 10px;color: green;">
                                            <?= ($model->processed_on != 0) ? date('d M Y H:i', $model->processed_on) : '-' ?>
                                        </p>
                                    </td>
                                    <td><?php echo $model->processUser->fullname.' ('.UsersHelper::getRole($model->processed_by).')'; ?></td>
                                    <td><?php echo ($model->processed_by != 0) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </article><!--.profile-info-item-->
        </section>
    </div>
</div>
<div class="container-fluid">
    <div class="box-typical-inner">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Member Name</th>
                    <th>Memeber CNIC</th>
                    <th>Application No</th>
                    <th>Sanction No</th>
                    <th>Tranch No</th>
                    <th>Tranch Amount</th>
                    <th>Loan Group No</th>
                    <th>Loan Amount</th>
                    <th>Project</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $i=1;?>
                <?php foreach($dataProviderLoans->getModels() as $tranche){ ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= isset($tranche->loan->application->member->full_name)?$tranche->loan->application->member->full_name:'' ?></td>
                        <td><?= isset($tranche->loan->application->member->cnic)?$tranche->loan->application->member->cnic:'' ?></td>
                        <td><?= isset($tranche->loan->application->application_no)?$tranche->loan->application->application_no:'' ?></td>
                        <td><?= isset($tranche->loan->sanction_no)?$tranche->loan->sanction_no:'' ?></td>
                        <td><?= isset($tranche->tranch_no)?$tranche->tranch_no:'' ?></td>
                        <td><?= isset($tranche->tranch_amount)?$tranche->tranch_amount:'' ?></td>
                        <td><?= isset($tranche->loan->group->grp_no)?$tranche->loan->group->grp_no:'' ?></td>
                        <td><?= isset($tranche->loan->loan_amount)?$tranche->loan->loan_amount:'' ?></td>
                        <td><?= isset($tranche->loan->project->name)?$tranche->loan->project->name:'' ?></td>
                        <?php if(in_array('frontend_remove-loanfundrequests',$permissions) && $model->status=='pending')
                        { ?>
                        <?php $form = ActiveForm::begin(['action'=>'remove-loan?id='.$model->id,'id'=>'remove-form','options' => [
                            'class' => 'remove-form'
                        ]]);?>
                        <?= $form->field($tranche, "id")->hiddenInput()->label(false) ?>
                        <td>
                            <?= Html::submitButton('Remove', ['id' => 'save-button', 'class' => 'btn btn-success btn-rounded btn-sm disb'/*,"onclick"=>"this.disabled = true"*/]) ?>
                        </td>
                        <?php $form->end(); ?>
                        <?php } ?>
                    </tr>
                <?php $i++; } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--<div class="box-typical box-typical-padding">
        <div class="fund-requests-form">
            <?/*= \yii\grid\GridView::widget([
                'id' => 'crud-datatable',
                'dataProvider' => $dataProviderLoans,
                'filterModel' => $searchModelLoans,
                'showFooter' => true,
                'columns' => require(__DIR__ . '/_columns_loans.php'),

            ]) */?></div>
    </div>-->
</div>