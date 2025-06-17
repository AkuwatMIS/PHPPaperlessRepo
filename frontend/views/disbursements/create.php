<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Disbursements */
$this->title = 'Create Disbursements';
$this->params['breadcrumbs'][] = ['label' => 'Disbursements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = '';
$js .= '$(".takaf").change(function() {
    var csc=this.className;
    if($(this).not(\':checked\')){
    document.getElementsByClassName(""+csc+"-receive-date")[0].disabled = true;
    document.getElementsByClassName(""+csc+"-credit")[0].disabled = true;
    document.getElementsByClassName(""+csc+"-receipt-no")[0].disabled = true;
    }
    if($(this).is(\':checked\')){
    document.getElementsByClassName(""+csc+"-receive-date")[0].disabled = false;
    document.getElementsByClassName(""+csc+"-credit")[0].disabled = false;
    document.getElementsByClassName(""+csc+"-receipt-no")[0].disabled = false;
    }
});

';
$js .= "

$('body').on('beforeSubmit', 'form#disb-place', function () {
     var form = $(this);
     // return false if form still have some validation errors
     if (form.find('.has-error').length) {
          return false;
     }
     // submit form
     $.ajax({
          url: '/disbursements/save-disbursement',
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                    //alert(obj.status_type);
                    $('.disbursement_id').val(obj.data.id);
                    $('.date_disbursed').val(obj.data.date_disbursed);
                    $('#disbursements-date_disbursed').attr('disabled','disabled');
                    $('#disbursements-venue').attr('disabled','disabled');
                    $('.disb-place').prop('disabled', true);
                    $('.disb').prop('disabled', false);
                    $('#tick').show();
                }else{
                    alert(obj.errors.date_disbursed);
                    //alert(obj.status_type);
                    $('#disb_error').show();                }
          }
     });
     return false;
});

$('body').on('beforeSubmit', 'form.formInstantDisb', function () {
     
     var id = this.id;
     var id_array = this.id.split(\"-\");
     var serial_no = id_array[2];
     //alert(serial_no);
     var loan_id = $('#loantranches-'+serial_no+'-id').val();
     //alert(loan_id);
     var form = $(this);
     // return false if form still have some validation errors
     if (form.find('.has-error').length) {
          return false;
     }
     // submit form
     $.ajax({
          url: '/disbursements/save-disbursement-loans?id='+loan_id,
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                    //alert(obj.data.message);
                    $('#loans-'+serial_no+'-cheque_no').attr('disabled','disabled');
                    $('#loans-'+serial_no+'-status').attr('disabled','disabled');
                    $('#save-button-'+serial_no).prop('disabled', true);
                    $('#'+serial_no+'-tick').show();
                    $('#status-'+serial_no).hide();
                    //$('#status-'+serial_no).addClass('success');
                    //$('#status-'+serial_no).text(obj.data.message);
                    //$('#status-'+serial_no).show();
                }else{
                    var c='';
                    $('#status-'+serial_no).addClass('error');
                    $('#status-'+serial_no).text(obj.errors);
                    jQuery.each(obj.errors, function(index, item) {
                    if(c!=''){
                      c += ',';
                    }
                        c +=item;
                  });
                  $('#status-'+serial_no).text(c);
                  $('#status-'+serial_no).show();
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
?>
<style>
    .success{
        background: #46c35f;
        border-radius: 5px;
        color: #ffffff;
        padding: 5px;
    }
    .error{
        background: #fa424a;
        border-radius: 5px;
        color: #ffffff;
        padding: 5px;
    }
</style>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h4>Select Branch for Disbursements</h4>
                    </div>
                </div>
            </div>
        </header>
        <div class="disbursements-form">
            <?php $form = ActiveForm::begin(['id' => 'disb-branch','method'=>'get']); ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'branch_id')->dropDownList($branches,['prompt'=>'Select Branch'])->label('Select Branch') ?>
                </div>
                <div class="col-md-4" style="margin-top:1.3%">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php if(isset($model->branch_id)){?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Create Disbursements</h4>
                </div>
            </div>
        </div>
    </header>
    <?php if (!empty($loans)) { ?>
    <div class="box-typical box-typical-padding">
        <!--<?php /*echo $this->render('_loans_search', [
            'model' => $loans_search,
            'regions_by_id' => $regions_by_id,
        ]); */?>
        <br>
        <br>-->

        <div class="disbursements-form">
            <?php $form = ActiveForm::begin(['id'=>'disb-place']); ?>
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'date_disbursed')->widget(\kartik\date\DatePicker::className(), [
                        'name' => 'report_date',
                        'options' => ['placeholder' => 'Disbursement Date'],
                        'type' => \kartik\date\DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                        ]]);
                    ?>
                </div>
                <?= $form->field($model, 'branch_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'area_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'region_id')->hiddenInput()->label(false) ?>
                <div class="col-lg-4">
                    <?= $form->field($model, 'venue')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-ld-4" style="margin-top:1.3%">
                    <?= Html::submitButton('Save Disbursement Place', ['class' => 'btn btn-success disb-place']) ?>
                    <span class="glyphicon glyphicon-ok" style="color:green;font-size: 20px;display: none;" id = "tick"></span>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
    <?php }else {?>
    <div class="box-typical box-typical-padding">
        <div class="disbursements-form">
            <h3>No loan found to disburse.</h3>
        </div>
    </div>
    <?php }?>

    <?php if (!empty($loans)) { ?>
        <div class="box-typical box-typical-padding">
            <table id="table-edit" class="table table-bordered table-hover">
                <h6>Showing <b><?= ($pagination->page) * $pagination->pageSize + 1?>-<?= ($pagination->page) * $pagination->pageSize + count($loans->getModels())?></b> of <b><?= $pagination->totalCount ?></b> items. </h6>

                <thead>
                <tr>
                    <th width="1">#</th>
                    <th>Group No</th>
                    <th>Sanction No</th>
                    <th>Name</th>
                    <th>Parentage</th>
                    <th>Req Amount</th>
                    <th>Cheque no</th>
                    <th>Tranch no</th>
                    <th></th>
                    <th>Takaful Date</th>
                    <th>Takaful Receipt</th>
                    <th>Takaful Amount</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 0;
                foreach ($loans->getModels() as $key=>$loan) { ?>
                    <tr>
                        <?php $form = ActiveForm::begin(['action' => '#', 'id' => 'loans-form-' . $i, 'options' => [
                            'class' => 'formInstantDisb'
                        ]
                        ]);
                        ?>
                        <td><?= $i+1 ?></td>
                        <td><?= isset($loan->loan->group->grp_no)?$loan->loan->group->grp_no:'Not Set' ?></td>
                        <td><?= isset($loan->loan->sanction_no)?$loan->loan->sanction_no:'Not Set' ?></td>
                        <td><?= isset($loan->loan->application->member->full_name)?$loan->loan->application->member->full_name:'Not Set' ?></td>
                        <td><?= isset($loan->loan->application->member->parentage)?$loan->loan->application->member->parentage:'Not Set' ?></td>
                        <td><?= number_format($loan->tranch_amount)?></td>
                        <?= $form->field($loan, "[{$i}]id")->hiddenInput(['class' => 'id','value'=>$loan->id])->label(false) ?>
                        <?= $form->field($loan, "[{$i}]disbursement_id")->hiddenInput(['class' => 'disbursement_id','name'=>'LoanTranches[disbursement_id]'])->label(false) ?>
                        <?= $form->field($loan, "[{$i}]date_disbursed")->hiddenInput(['class' => 'date_disbursed','name'=>'LoanTranches[date_disbursed]'])->label(false) ?>
                        <td><?= $form->field($loan, "[{$i}]cheque_no")->textInput(['maxlength' => true, 'class' => 'form-control input-sm','name'=>'LoanTranches[cheque_no]'])->label(false) ?></td>
                        <td><b><?php echo $loan->tranch_no?></b></td>
                        <!-- <td><input class='form-control input-sm takaf -<?php /*echo $i*/?>-receive-date' type="text" id="receive_date"
                                   name="Operations[receive_date]"></td>-->

                        <?php
                        if(!in_array($loan->loan->project_id,\common\components\Helpers\StructureHelper::trancheProjects())){?>
                            <td><input class="takaf -<?php echo $i?>" type="checkbox" id="takaf" checked="checked"></td>
                            <td>
                            <?php echo \yii\jui\DatePicker::widget([
                            'name' => 'Operations[receive_date]',
                            'value' => date('d-M-Y'),
                            'options' => ['placeholder' => 'Select date',
                                'class'=>'form-control input-sm takaf -'.$i.'-receive-date',
                                'type' => \kartik\date\DatePicker::TYPE_INPUT,
                                'format' => 'dd-M-yyyy',
                                'todayHighlight' => true
                            ],
                        ]);?>
                            </td>
                            <td><input class='form-control input-sm takaf -<?php echo $i?>-receipt-no' type="text" id="receipt_no"
                                       name="Operations[receipt_no]"></td>
                            <td><input class="form-control input-sm takaf -<?php echo $i?>-credit" type="text" id="credit" name="Operations[credit]" value=<?php echo (($loan->tranch_amount*1)/100)?>></td>


                       <?php }
                       else { ?>
                           <td><input disabled="disabled" class="takaf -<?php echo $i?>" type="checkbox" id="takaf""></td>
                           <td>
                               <?php echo \yii\jui\DatePicker::widget([
                                   'name' => 'Operations[receive_date]',
                                   'value' => date('d-M-Y'),
                                   'options' => ['placeholder' => 'Select date',
                                       'class'=>'form-control input-sm takaf -'.$i.'-receive-date',
                                       'type' => \kartik\date\DatePicker::TYPE_INPUT,
                                       'format' => 'dd-M-yyyy',
                                       'todayHighlight' => true,
                                       'disabled'=>'disabled',
                                   ],
                               ]);?>
                           </td>
                           <td><input disabled="disabled" class='form-control input-sm takaf -<?php echo $i?>-receipt-no' type="text" id="receipt_no"
                                      name="Operations[receipt_no]"></td>
                           <td><input disabled="disabled" class="form-control input-sm takaf -<?php echo $i?>-credit" type="text" id="credit" name="Operations[credit]"></td>

                           <?php } ?>
                        <input type="hidden" id="application_id" value="<?php echo $loan->loan->application_id ?>"
                               name="Operations[application_id]">
                        <input type="hidden" id="operation_type_id" value="2"
                               name="Operations[operation_type_id]">
                        <input type="hidden" id="region_id" value="<?php echo $loan->loan->region_id ?>"
                               name="Operations[region_id]">
                        <input type="hidden" id="area_id" value="<?php echo $loan->loan->area_id ?>"
                               name="Operations[area_id]">
                        <input type="hidden" id="branch_id" value="<?php echo $loan->loan->branch_id ?>"
                               name="Operations[branch_id]">
                        <input type="hidden" id="team_id" value="<?php echo $loan->loan->team_id ?>"
                               name="Operations[team_id]">
                        <input type="hidden" id="field_id" value="<?php echo $loan->loan->field_id ?>"
                               name="Operations[field_id]">
                        <input type="hidden" id="project_id" value="<?php echo $loan->loan->project_id ?>"
                               name="Operations[project_id]">
                        <td><?= $form->field($loan, "[{$i}]status")->dropDownList(\common\components\Helpers\LoanHelper::getTranchStatus(), ['prompt' => 'Select Status', 'class' => 'form-control input-sm','name'=>'LoanTranches[status]','required'=>true])->label(false); ?></td>
                        <td>
                            <span class="glyphicon glyphicon-ok" style="color:green;display: none;" id = <?= $i."-tick" ?> ></span>
                            <?= Html::submitButton('save', ['id'=>'save-button-' . $i,'class' => 'btn btn-success btn-sm disb','disabled'=>'disabled']) ?>
                            <div id="status-<?php echo $i; ?>" style="display: none;" class="status"></div>
                        </td>
                        <?php $form->end(); ?>

                    </tr>

                    <?php $i++;
                } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
    <?php echo \yii\widgets\LinkPager::widget(['pagination' => $pagination]);?>

</div>
<?php }?>