<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Actions */
/* @var $form yii\widgets\ActiveForm */
$js = "
$('#fix-schedule').on('click', function() {
});
window.setTimeout(function() {
    $(\".alert\").fadeTo(3000, 0).slideUp(1000, function(){
        $(this).remove(); 
    });
}, 500);
";
$js .= "
$('body').on('beforeSubmit', 'form.UpdateChequeNo', function () {
   
     var loan_id = $('#loans-id').val();
     //alert(loan_id);
     var form = $(this);      
     $('#cheque-update').prop('disabled', true);
     // submit form
     $.ajax({
          url: '/fixes/update-cheque?id='+loan_id,
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){ 
                
                  $('#loans-date_disbursed').prop(\"value\",obj.data.date_disbursed);

                  $('#cheque-update').prop('disabled', true);
                  $('#status').removeClass('error');
                  $('#status').addClass('success');
                  $('#status').text('Updated Successfully');
                  $('#status').show();
                }else{
                $('#loans-date_disbursed').prop(\"value\",obj.data.date_disbursed);
                $('#cheque-update').prop('disabled', false);
                var c='';
                 $('#status').addClass('error');
                  jQuery.each(obj.errors, function(index, item) {
                    if(c!=''){
                      c += ',';
                    }
                        c +=item;
                  });
                 $('#status').text(c);
                 $('#status').show();
                }
          }
     });
     return false;
});

window.setTimeout(function() {
    $(\".sttus\").fadeTo(500, 0).slideUp(500, function(){
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
            <div class="row">
                <div class="col-md-12">
                    <h4>
                        <b><?php echo isset($model->application->member->full_name) ? $model->application->member->full_name : 'Not Set';
                            echo isset($model->application->member->cnic) ? ' (' . $model->application->member->cnic . ')' : '(Not Set)';
                            ?></b>
                        <a id='fix-schedule' href="/fixes/fixes-schedules?id=<?php echo $model->id ?>"
                           class="btn btn-primary pull-right mr-2">Fix Schedules</a>
                        <?php if(in_array('frontend_ledger-generatefixes',$permissions))
                        { ?>
                            <a id='fix-schedule' href="/fixes/ledger-generate?id=<?php echo $model->id ?>"
                               class="btn btn-success pull-right mr-2">Ledger Generate</a>
                        <?php }?>
                        <?php if(in_array('frontend_housing-ledgerfixes',$permissions))
                        { ?>
                            <a id='fix-schedule' href="/fixes/housing-ledger?id=<?php echo $model->id ?>"
                               class="btn btn-primary pull-right mr-2">Fix Housing Schedules</a>
                        <?php }?>
                    </h4>
                </div>
            </div>
            <br>
            <?php if (Yii::$app->session->hasFlash('success')) { ?>
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Updated!</h4>
                    <?= Yii::$app->session->getFlash('success')[0] ?>
                </div>
            <?php }?>
            <div class="row">
                <div class="col-lg-2">
                    <?= $form->field($model, 'sanction_no')->textInput(['maxlength' => true, 'disabled' => 'disabled', 'class' => 'form-control form-control-sm']) ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'region_id')->textInput(['maxlength' => true, 'value' => $model->region->name, 'disabled' => 'disabled', 'class' => 'form-control form-control-sm'])->label('Region') ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'area_id')->textInput(['maxlength' => true, 'value' => $model->area->name, 'disabled' => 'disabled', 'class' => 'form-control form-control-sm'])->label('Area') ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'branch_id')->textInput(['maxlength' => true, 'value' => $model->branch->name, 'disabled' => 'disabled', 'class' => 'form-control form-control-sm'])->label('Branch') ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'project_id')->textInput(['maxlength' => true, 'value' => $model->project->name, 'disabled' => 'disabled', 'class' => 'form-control form-control-sm'])->label('Project') ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'loan_amount')->textInput(['maxlength' => true, 'disabled' => 'disabled', 'class' => 'form-control form-control-sm'])->label('Loan Amount') ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'inst_amnt')->textInput(['maxlength' => true, 'disabled' => 'disabled', 'class' => 'form-control form-control-sm'])->label('Installment Amount') ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'inst_type')->textInput(['maxlength' => true, 'disabled' => 'disabled', 'class' => 'form-control form-control-sm'])->label('Installment Type') ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'inst_months')->textInput(['maxlength' => true, 'value' => number_format($model->inst_months), 'disabled' => 'disabled', 'class' => 'form-control form-control-sm'])->label('Total Installments') ?>
                </div>
            </div>
            <hr>
            <h4><b>Edit Loan</b></h4>
            <?php if(isset($model->disbTranches)){ foreach($model->disbTranches as $tranche) { ?>
            <?php $form = ActiveForm::begin(['enableClientValidation' => false,
                'action' => ['update-cheque?id=' . $tranche->id],
                'options' => ['class' => 'UpdateChequeNo'],
                'method' => 'post']); ?>
            <?= $form->field($tranche, 'id')->hiddenInput()->label(false) ?>
            <div class="row">
                <div class="col-lg-2">
                    <?= $form->field($tranche, 'cheque_no')->textInput(['maxlength' => true, 'class' => 'form-control form-control-sm', 'required' => true])->label('Cheque No') ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($tranche, "cheque_date")->widget(\yii\jui\DatePicker::className(), [
                        'dateFormat' => 'yyyy-MM-dd',
                        'options' => ['class' => 'form-control input-sm cheque_dt', 'placeholder' => 'Cheque Date', 'required' => true],

                    ])->label('Cheque Date'); ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($tranche, "date_disbursed")->widget(\yii\jui\DatePicker::className(), [
                        'dateFormat' => 'yyyy-MM-dd',
                        'options' => ['class' => 'form-control input-sm cheque_dt', 'placeholder' => 'Disbursement Date', 'required' => true,'disabled'=>(date('Y-m',$model->date_disbursed)==date('Y-m'))?false:true],

                    ])->label('Disbursement Date'); ?>
                </div>
                <div class="col-lg-1">
                    <?= Html::submitButton('Update', ['id' => 'cheque-update', 'class' => 'btn btn-primary pull-right', 'style' => 'margin-top:20px']) ?>
                </div>
                <div class="col-lg-3">
                    <div id="status" style="display: none;margin-top: 20px" class="status"></div>
                    <span class="glyphicon glyphicon-ok" style="color:green;display: none;"
                          id="tick"></span>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <?php }} ?>
        </div>
    <?php } ?>

</div>
