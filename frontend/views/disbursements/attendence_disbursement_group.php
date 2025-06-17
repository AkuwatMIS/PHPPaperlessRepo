<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Disbursements */
$this->title = 'Create Disbursements';
$this->params['breadcrumbs'][] = ['label' => 'Disbursements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = '';
$js = "$(document).ready(function(){
  $('.add_to_disburse').each(function(i, obj) {
     var id = this.id;
     //var id_array = this.id.split(\"-\");
     //var serial_no = id_array[2];
   
        var flag=0;
        $('.disb-'+i).each(function(j, obj1) {
           if($('#'+obj1.id).is(':disabled')){}
            else{flag=1;}
         });
         if(flag==0){
         $('#disburse_all-button-'+i).prop('disabled', false);
        }
        /*if(flag==1){
         $('#disburse_all-button-'+i).prop('disabled', false);
        }
        else{
        $('#disburse_all-button-'+i).prop('innerText', 'Added to disbursement');
        }*/
  });
});";
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
$js .= '$(".detail").click(function() {
     var id=this.id;
     if ($("#group-detail-"+id).css("display") == "none") {
        //$("#group-detail-"+id).fadeIn(500)
        $("#group-detail-"+id).slideDown(1000);
        $( "#icon-"+id).removeClass( "fa-chevron-down");
        $( "#icon-"+id).addClass( "fa-chevron-up" );
     }
     else{
        //$("#group-detail-"+id).fadeOut(500)
        $("#group-detail-"+id).slideUp(1000);
        $( "#icon-"+id).removeClass( "fa-chevron-up" );
        $( "#icon-"+id).addClass( "fa-chevron-down" );
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
          url: '/disbursements/save-disbursement-all',
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                    $('#disbursements-date_disbursed').attr('disabled','disabled');
                    $('#disbursements-venue').attr('disabled','disabled');
                    $('.disb-place').prop('disabled', true);
                    $('#disburse_all').prop('disabled', false);
                    $('#tick').show();
                    $('#your-modal').modal('hide');
                    location.reload();
                //set disbursement_id
                   var disbursement_id_input = document.createElement(\"input\");
                   disbursement_id_input.setAttribute('type', 'hidden');
                   disbursement_id_input.setAttribute('name', 'Loans[disbursement_id]');
                   disbursement_id_input.setAttribute('value', obj.data.id);
                   document.getElementById('disburse-all').appendChild(disbursement_id_input);
                //set disbursement_date
                   var disbursement_date_input = document.createElement(\"input\");
                   disbursement_date_input.setAttribute('type', 'hidden');
                   disbursement_date_input.setAttribute('name', 'Loans[date_disbursed]');
                   disbursement_date_input.setAttribute('value', obj.data.date_disbursed);
                   document.getElementById('disburse-all').appendChild(disbursement_date_input);
                    
                }else{
                    alert(obj.errors.date_disbursed);
                    //alert(obj.status_type);
                    $('#disb_error').show();                }
          }
     });
     return false;
});
$('body').on('beforeSubmit', 'form.formAddDisb', function () {
 var form = $(this);
 var id = this.id;
     var id_array = this.id.split(\"-\");
     var serial_no = id_array[2];
$.ajax({
          url: '/disbursements/add-disburse',
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                alert('Group No '+obj.data.group+' is added to disbursement.')
                  $('#disburse_all-button-'+serial_no).attr('disabled','disabled');
                }else{
                  //$('#'+this.id).attr('disabled','disabled');

                }
          }
     });
     return false;
});
$('body').on('beforeSubmit', 'form.formInstantDisb', function () {
     
     var id = this.id;
     var id_array = this.id.split(\"-\");
     var serial_no1 = id_array[2];
     var serial_no2 = id_array[3];

     var loan_id = $('#loantranches-'+serial_no1+'-'+serial_no2+'-loan_id').val();
    
     var form = $(this);
     // return false if form still have some validation errors
     if (form.find('.has-error').length) {
          return false;
     }
     // submit form
     $.ajax({
          url: '/disbursements/save-attendance-loans?id='+loan_id,
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                    //alert(obj.data.message);
                    $('#loans-'+serial_no1+'-'+serial_no2+'-cheque_no').attr('disabled','disabled');
                    $('#loans-'+serial_no1+'-'+serial_no2+'-status').attr('disabled','disabled');
                    $('#save-button-'+serial_no1+'-'+serial_no2).prop('disabled', true);
                    $('#'+serial_no1+'-'+serial_no2+'-tick').show();
                    $('#status-'+serial_no1+'-'+serial_no2).hide();
                    if(obj.data.status=='collected'){
                        var flag=0;
                        var count=0;
                        var present_count=0;
                        $('.disb-'+serial_no1).each(function(i, obj) {
                           var split_arr = obj.id.split(\"-\");
                           var second_id = split_arr[3];
                           count=count+1;
                           if($('#'+obj.id).is(':disabled')){
                               atndnc_st=$('#loantranches-'+serial_no1+'-'+second_id+'-attendance_status').val();
                               if(atndnc_st=='present'){
                                 present_count=present_count+1;
                               }
                           }
                            else{flag=1;}
                         });
                         if(count>1 && present_count<3){
                             flag=1;
                         }
                         if(count==1 && present_count!=1){
                             flag=1;
                         }
                        if(flag==0){
                         alert('Group No '+obj.data.group+' is ready to add for disbursement.')
                         $('#disburse_all-button-'+serial_no1).prop('disabled', false);
                        }
                           /*var loan_id_iput = document.createElement(\"input\");
        
                           loan_id_iput.setAttribute('type', 'hidden');
        
                           loan_id_iput.setAttribute('name', 'Loans['+loan_id+'][id]');
        
                           loan_id_iput.setAttribute('value', loan_id);
                           document.getElementById('disburse-all-'+serial_no1).appendChild(loan_id_iput);*/
                        
                    }else{
                        var flag=0;
                        var count=0;
                        var present_count=0;
                        $('.disb-'+serial_no1).each(function(i, obj) {
                        var split_arr = obj.id.split(\"-\");
                        var second_id = split_arr[3];
                           count=count+1;
                           if($('#'+obj.id).is(':disabled')){
                               atndnc_st=$('#loantranches-'+serial_no1+'-'+second_id+'-attendance_status').val();
                               if(atndnc_st=='present'){
                                 present_count=present_count+1;
                               }
                           }
                            else{flag=1;}
                         });
                         if(count>1 && present_count<3){
                             flag=1;
                         }
                         if(count==1 && present_count!=1){
                             flag=1;
                         }
                        if(flag==0){
                         alert('Group No '+obj.data.group+' is ready to add for disbursement.')
                         $('#disburse_all-button-'+serial_no1).prop('disabled', false);
                        }
                    }
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

        span.green {
            background: #5EA226;
            border-radius: 0.8em;
            -moz-border-radius: 0.8em;
            -webkit-border-radius: 0.8em;
            color: #ffffff;
            display: inline-block;
            font-weight: bold;
            line-height: 1.6em;
            margin-right: 15px;
            text-align: center;
            width: 1.6em;
        }

        .fa {
            font-size: 1.2em !important; /*size whatever you like*/
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
                <?php $form = ActiveForm::begin(['id' => 'disb-branch', 'method' => 'get']); ?>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'branch_id')->dropDownList($branches, ['prompt' => 'Select Branch'])->label('Select Branch') ?>
                    </div>
                    <div class="col-md-4" style="margin-top:1.3%">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
<?php if (isset($model->branch_id)) { ?>
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

        <?php } else { ?>
            <div class="box-typical box-typical-padding">
                <div class="disbursements-form">
                    <h3>No loan found to disburse.</h3>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($loans)) { ?>
            <div class="box-typical box-typical-padding">

                <!--///-->
                <?php echo \yii\helpers\Html::button('Disburse All', [
                    'class' => 'btn btn-success pull-right',
                    'id' => 'BtnModalId',
                    //'style'=>'margin-top: 30px',
                    'data-toggle' => 'modal',
                    'data-target' => '#your-modal',

                ]); ?>
                <!--//-->
                <?php

                Modal::begin([

                    'header' => '',

                    'id' => 'your-modal',

                    'size' => 'modal-md',

                ]);
                ?>
                <div class="disbursements-form">
                    <?php $form = ActiveForm::begin(['id' => 'disb-place']); ?>
                    <div class="row">
                        <div class="col-lg-12">
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
                        <div class="col-lg-12">
                            <?= $form->field($model, 'venue')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-ld-4" style="margin-top:1.3%;margin-left:2%">
                            <?= Html::submitButton('Save Disbursement Place', ['class' => 'btn btn-success disb-place']) ?>
                            <span class="glyphicon glyphicon-ok" style="color:green;font-size: 20px;display: none;"
                                  id="tick"></span>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <?php


                    Modal::end();

                    ?>
                </div>
                <!--<table id="table-edit" class="table table-bordered table-hover">-->
                <h6>Showing <b><?= ($pagination->page) * $pagination->pageSize + 1 ?>
                        -<?= ($pagination->page) * $pagination->pageSize + count($loans->getModels()) ?></b> of
                    <b><?= $pagination->totalCount ?></b> items. </h6>

                <thead>
                <tr>
                    <th width="1">#</th>
                    <th>Group No</th>
                    <th>Detail</th>
                    <!--</tr>
                    </thead>
                    <tbody>-->
                    <?php
                    $i = 0;
                    foreach ($loans->getModels() as $key => $loan) {
                        ?>
                        <?php $form = ActiveForm::begin(['action' => '#', 'id' => 'loans-form-' . $i, 'options' => [
                            'class' => 'formAddDisb'
                        ]
                        ]);
                        ?>

                        <div id="<?php echo $i ?>" class="alert alert-primary alert-dismissable detail"
                             style="height: 50px">
                            <span class="green"> <?= $i + 1 ?></span><b><?= isset($loan->grp_no) ? $loan->grp_no : 'Not Set' ?></b>
                            <button id="<?php echo $i ?><"
                                    type="button" style="background-color: #cce5ff;border: none"
                                    class="pull-right btn  detail"
                            ><i id="icon-<?php echo $i ?>" class="fa fa-chevron-down"></i></button>
                        </div>



                        <div class="card">
                            <div class="card-body" style="display: none" id="group-detail-<?php echo $i ?>">
                                <?php $form = ActiveForm::begin(['action' => '/disbursements/disburse-all-loans', 'method' => 'post', 'id' => 'loans-form-disburse' . $i, 'options' => [

                                ]]); ?>
                                <div id="disburse-all-<?php echo $i ?>">
                                    <?php foreach ($loan->loans as $loan_id) { ?>
                                        <input type="hidden" name="Loans[<?php echo $loan_id->id ?>][id]"
                                               value="<?php echo $loan_id->id ?>">
                                    <?php } ?>

                                </div>


                                <?= Html::submitButton('Add to Disburse', ['disabled' => 'true', 'id' => 'disburse_all-button-' . $i, 'class' => 'btn btn-primary pull-right add_to_disburse']) ?>
                                <?php $form->end(); ?>

                                <br>
                                <br>

                                <table id="table-edit" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="1">#</th>
                                        <th>Group No</th>
                                        <th>Sanction No</th>
                                        <th>Name</th>
                                        <th>Parentage</th>
                                        <th>Tranch Amount</th>
                                        <th>Tranch No</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $j = 0;
                                    foreach ($loan->loans as $key => $loan) { ?>
                                        <?php $tranch = \common\models\LoanTranches::find()
                                            ->andWhere(['in', 'loan_tranches.status', [4, 5]])
                                            ->andWhere(['loan_tranches.disbursement_id' => 0])
                                            ->andWhere(['in', 'loan_id', [$loan->id]])
                                            ->andWhere(['loan_tranches.date_disbursed' => 0])->one();
                                        $loan_actions = \common\models\LoanActions::find()->where(['parent_id' => $loan->id, 'action' => 'takaful'])->one();
                                        $accVerify_actions = \common\models\LoanActions::find()->where(['parent_id' => $loan->id, 'action' => 'account_verification'])->one();
                                        ?>
                                        <?php if (!empty($tranch) && (in_array($loan->project_id, \common\components\Helpers\StructureHelper::trancheProjects()) || $loan_actions->status == 1)) {
                                            if(!empty($accVerify_actions)){
                                                if($accVerify_actions->status == 1){ ?>
                                                    <tr>
                                                        <?php $form = ActiveForm::begin(['action' => '#', 'id' => 'loans-form-' . $i . '-' . $j, 'options' => [
                                                            'class' => 'formInstantDisb'
                                                        ]
                                                        ]);
                                                        ?>
                                                        <td><?= $j + 1 ?></td>
                                                        <td><?= isset($tranch->loan->group->grp_no) ? $tranch->loan->group->grp_no : 'Not Set' ?></td>
                                                        <td><?= isset($tranch->loan->sanction_no) ? $tranch->loan->sanction_no : 'Not Set' ?></td>
                                                        <td><?= isset($tranch->loan->application->member->full_name) ? $tranch->loan->application->member->full_name : 'Not Set' ?></td>
                                                        <td><?= isset($tranch->loan->application->member->parentage) ? $tranch->loan->application->member->parentage : 'Not Set' ?></td>
                                                        <td><?= number_format($tranch->tranch_amount) ?></td>
                                                        <td><?= number_format($tranch->tranch_no) ?></td>
                                                        <td><?= $form->field($tranch, "[{$i}][$j]attendance_status")->dropDownList(\common\components\Helpers\LoanHelper::getAttendanceStatus(), ['prompt' => 'Select Status', 'class' => 'form-control input-sm', 'name' => 'LoanTranches[attendance_status]', 'required' => true])->label(false); ?></td>
                                                        <?= $form->field($tranch, "[{$i}][$j]loan_id")->hiddenInput(['class' => 'form-control', 'name' => 'LoanTranches[loan_id]', 'required' => true])->label(false); ?>
                                                        <?= $form->field($tranch, "[{$i}][$j]id")->hiddenInput(['class' => 'form-control', 'name' => 'LoanTranches[id]', 'required' => true])->label(false); ?>
                                                        <td>
                                                            <?php $nadraStatus = \common\components\Helpers\LoanHelper::VerifyNadraVerysis($tranch->loan);
                                                            if ($nadraStatus) {
                                                                ?>
                                                                <?php $cib = \common\models\ApplicationsCib::find()->where(['application_id' => $tranch->loan->application->id])->andWhere(['status'=> 1])->one();
                                                                if (in_array($tranch->loan->project_id, \common\components\Helpers\StructureHelper::kamyaabPakitanProjects())) {
                                                                    if (!empty($cib) && $cib != null) { ?>
                                                                        <span class="glyphicon glyphicon-ok"
                                                                              style="color:green;display: <?php echo ($tranch->attendance_status == 'present') ? 'inline' : 'none' ?>;"
                                                                              id= <?= $i . '-' . $j . "-tick" ?>></span>
                                                                        <?= Html::submitButton('save', ['id' => 'save-button-' . $i . '-' . $j, 'class' => 'btn btn-success btn-sm disb-' . $i, 'disabled' => ($tranch->attendance_status == 'present') ? true : false]) ?>
                                                                        <div id="status-<?php echo $i . '-' . $j; ?>"
                                                                             style="display: none;"
                                                                             class="status"></div>
                                                                    <?php } else { ?>
                                                                        <span style="color: red"
                                                                              id= <?= $i . '-' . $j . "-cross-cib" ?>> Cib Pending</span>
                                                                    <?php } ?>
                                                                <?php } else { ?>
                                                                    <span class="glyphicon glyphicon-ok"
                                                                          style="color:green;display: <?php echo ($tranch->attendance_status == 'present') ? 'inline' : 'none' ?>;"
                                                                          id= <?= $i . '-' . $j . "-tick" ?>></span>
                                                                    <?= Html::submitButton('save', ['id' => 'save-button-' . $i . '-' . $j, 'class' => 'btn btn-success btn-sm disb-' . $i, 'disabled' => ($tranch->attendance_status == 'present') ? true : false]) ?>
                                                                    <div id="status-<?php echo $i . '-' . $j; ?>"
                                                                         style="display: none;"
                                                                         class="status"></div>
                                                                <?php } ?>

                                                            <?php } else { ?>
                                                                <span style="color: red"
                                                                      id= <?= $i . '-' . $j . "-cross-nadra" ?>> Nadra-Verysis Pending</span>
                                                            <?php } ?>

                                                        </td>
                                                        <?php $form->end(); ?>

                                                    </tr>
                                               <?php } ?>
                                           <?php }else{ ?>
                                                <tr>
                                                    <?php $form = ActiveForm::begin(['action' => '#', 'id' => 'loans-form-' . $i . '-' . $j, 'options' => [
                                                        'class' => 'formInstantDisb'
                                                    ]
                                                    ]);
                                                    ?>
                                                    <td><?= $j + 1 ?></td>
                                                    <td><?= isset($tranch->loan->group->grp_no) ? $tranch->loan->group->grp_no : 'Not Set' ?></td>
                                                    <td><?= isset($tranch->loan->sanction_no) ? $tranch->loan->sanction_no : 'Not Set' ?></td>
                                                    <td><?= isset($tranch->loan->application->member->full_name) ? $tranch->loan->application->member->full_name : 'Not Set' ?></td>
                                                    <td><?= isset($tranch->loan->application->member->parentage) ? $tranch->loan->application->member->parentage : 'Not Set' ?></td>
                                                    <td><?= number_format($tranch->tranch_amount) ?></td>
                                                    <td><?= number_format($tranch->tranch_no) ?></td>
                                                    <td><?= $form->field($tranch, "[{$i}][$j]attendance_status")->dropDownList(\common\components\Helpers\LoanHelper::getAttendanceStatus(), ['prompt' => 'Select Status', 'class' => 'form-control input-sm', 'name' => 'LoanTranches[attendance_status]', 'required' => true])->label(false); ?></td>
                                                    <?= $form->field($tranch, "[{$i}][$j]loan_id")->hiddenInput(['class' => 'form-control', 'name' => 'LoanTranches[loan_id]', 'required' => true])->label(false); ?>
                                                    <?= $form->field($tranch, "[{$i}][$j]id")->hiddenInput(['class' => 'form-control', 'name' => 'LoanTranches[id]', 'required' => true])->label(false); ?>
                                                    <td>
                                                        <?php $nadraStatus = \common\components\Helpers\LoanHelper::VerifyNadraVerysis($tranch->loan);
                                                        if ($nadraStatus) {
                                                            ?>
                                                            <?php $cib = \common\models\ApplicationsCib::find()->where(['application_id' => $tranch->loan->application->id])->andWhere(['status'=> 1])->one();
                                                            if (in_array($tranch->loan->project_id, \common\components\Helpers\StructureHelper::kamyaabPakitanProjects())) {
                                                                if (!empty($cib) && $cib != null) { ?>
                                                                    <span class="glyphicon glyphicon-ok"
                                                                          style="color:green;display: <?php echo ($tranch->attendance_status == 'present') ? 'inline' : 'none' ?>;"
                                                                          id= <?= $i . '-' . $j . "-tick" ?>></span>
                                                                    <?= Html::submitButton('save', ['id' => 'save-button-' . $i . '-' . $j, 'class' => 'btn btn-success btn-sm disb-' . $i, 'disabled' => ($tranch->attendance_status == 'present') ? true : false]) ?>
                                                                    <div id="status-<?php echo $i . '-' . $j; ?>"
                                                                         style="display: none;"
                                                                         class="status"></div>
                                                                <?php } else { ?>
                                                                    <span style="color: red"
                                                                          id= <?= $i . '-' . $j . "-cross-cib" ?>> Cib Pending</span>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <span class="glyphicon glyphicon-ok"
                                                                      style="color:green;display: <?php echo ($tranch->attendance_status == 'present') ? 'inline' : 'none' ?>;"
                                                                      id= <?= $i . '-' . $j . "-tick" ?>></span>
                                                                <?= Html::submitButton('save', ['id' => 'save-button-' . $i . '-' . $j, 'class' => 'btn btn-success btn-sm disb-' . $i, 'disabled' => ($tranch->attendance_status == 'present') ? true : false]) ?>
                                                                <div id="status-<?php echo $i . '-' . $j; ?>"
                                                                     style="display: none;"
                                                                     class="status"></div>
                                                            <?php } ?>

                                                        <?php } else { ?>
                                                            <span style="color: red"
                                                                  id= <?= $i . '-' . $j . "-cross-nadra" ?>> Nadra-Verysis Pending</span>
                                                        <?php } ?>

                                                    </td>
                                                    <?php $form->end(); ?>

                                                </tr>
                                           <?php }
                                            ?>
                                        <?php }
                                        ?>

                                        <?php $j++;

                                    } ?>
                                    </tbody>
                                </table>


                            </div>
                        </div>
                        <?php $form->end(); ?>


                        <?php $i++;
                    } ?>
                    <!-- </tbody>
                 </table>-->
            </div>
        <?php } ?>
        <?php echo \yii\widgets\LinkPager::widget(['pagination' => $pagination]); ?>

    </div>
<?php } ?>