<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Actions */
/* @var $form yii\widgets\ActiveForm */
$js = "
window.setTimeout(function() {
    $(\".alert\").fadeTo(3000, 0).slideUp(1000, function(){
        $(this).remove(); 
    });
}, 500);
";
$this->registerJs($js);
$permissions = Yii::$app->session->get('permissions');
?>
<style>
    .success {
        background: #46c35f;
        border-radius: 5px;
        color: #ffffff;
        padding: 5px;
    }

    .error {
        background: #fa424a;
        border-radius: 5px;
        color: #ffffff;
        padding: 5px;
    }
</style>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading">
            <b>Search Loan</b>
        </h6>
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-lg-4">
                <?= $form->field($model, 'sanction_no')->textInput(['maxlength' => true, 'placeholder' => 'Search Sanction No', 'class' => 'form-control form-control-sm']) ?>
            </div>
            <div class="col-lg-1">
                <?= Html::submitButton('Search', ['class' => 'btn btn-success pull-right', 'style' => 'margin-top:20px']) ?>
            </div>
        </div>
        <br>
        <?php if (Yii::$app->session->hasFlash('error')) { ?>
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                <h4><i class="icon fa fa-remove"></i> Error!</h4>
                <?= Yii::$app->session->getFlash('error')[0] ?>
            </div>
        <?php } ?>
        <?php ActiveForm::end(); ?>
    </div>
    <?php if (isset($model->id)) { ?>
        <div class="box-typical box-typical-padding">
            <h4>
                <b><?php echo isset($model->application->member->full_name) ? $model->application->member->full_name : 'Not Set';
                    echo isset($model->application->member->cnic) ? ' (' . $model->application->member->cnic . ')' : '(Not Set)';
                    ?></b>
            </h4>
            <br>
            <?php if (Yii::$app->session->hasFlash('success')) { ?>
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Updated!</h4>
                    <?= Yii::$app->session->getFlash('success')[0] ?>
                </div>
            <?php } ?>
            <?php $form = \yii\widgets\ActiveForm::begin(['action' => 'reject-loan?id=' . $model->id, 'method' => 'post']); ?>

            <div class="row">
                <div class="col-sm-12 pull-right">

                    <?php
                    echo \yii\helpers\Html::button('Reject Loan', [
                        'class' => 'btn btn-danger pull-right',
                        'id' => 'BtnModalId',
                        'style' => 'margin-top: 30px',
                        'data-toggle' => 'modal',
                        'data-target' => '#your-modal',

                    ]); ?>
                </div>
                <?php

                \yii\bootstrap\Modal::begin([

                    'header' => '',

                    'id' => 'your-modal',

                    'size' => 'modal-md',
                    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false]
                ]);
                ?>
                <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'status')->dropDownList(['permanent_reject' => 'Permanent Reject', 'ready_for_disbursement' => 'Ready For Disbursement', 'ready_for_fund_request' => 'Ready To Fund Request'])->label('Action') ?>
                <?= $form->field($model, 'reject_reason')->textInput(['maxlength' => true]) ?>
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?php \yii\bootstrap\Modal::end(); ?>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>CNIC</th>
                        <th>Sanction No</th>
                        <th>Disbursement Date</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $model->application->member->full_name ?></td>
                        <td><?= $model->application->member->cnic ?></td>
                        <td><?= $model->sanction_no ?></td>
                        <td><?= ($model->date_disbursed != 0) ? date('d M,Y', $model->date_disbursed) : 'Not Set' ?></td>
                        <td><?= $model->status ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <hr>
            <?php foreach ($model->tranchesSorted as $tranch) { ?>
                <section class="card mb-3">
                    <header class="card-header card-header-lg">
                        Tranche-<?= $tranch->tranch_no ?> Info
                    </header>
                    <div class="profile-info-item" style="margin-top: 15px;">
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <b>Tranche Status</b> : <?php echo $tranch->status; ?>
                                </p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Step</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Ready For Fund Request</td>
                                    <td><?= ($tranch->status > 3) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                                <tr>
                                    <td>Fund Request</td>
                                    <td><?= ($tranch->fund_request_id != 0) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                                <tr>
                                    <td>Cheque Printing</td>
                                    <td><?= ($tranch->cheque_no != 0 && $tranch->cheque_no != '' && $tranch->cheque_no != null) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                                <tr>
                                    <td>Disbursement</td>
                                    <td><?= ($tranch->status > 5) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                                <tr>
                                    <td>Publish</td>
                                    <td><?= ((empty($tranch->publish) && $tranch->publish==null) || $tranch->publish->status == 2) ? '<span class="glyphicon glyphicon-remove" style="color:red"></span>' : '<span class="glyphicon glyphicon-ok" style="color:green"></span>' ?></td>
                                </tr>
                                <tr>
                                    <td>Funds Transfered</td>
                                    <td><?= ($tranch->status == 6) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            <?php } ?>
        </div>
    <?php } ?>
</div>
