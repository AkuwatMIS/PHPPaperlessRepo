<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\LoanWriteOff */
$js = ""; // Initialize $js as an empty string
$js .= "
$(document).ready(function(){
     type_data = $('#loanwriteoff-type').val();
     if(type_data==0){
        $('#field-relation').text('Relation with borrower')
     }else{
        $('#field-relation').text('Relation With Guardian');
     }


          $('.field-loanwriteoff-reason').hide();
          $('.field-loanwriteoff-deposit_slip_no').hide();
          $('.field-loanwriteoff-bank_name').hide();
          $('.field-loanwriteoff-bank_account_no').hide();
        
        var selected_value =  $(\"#loanwriteoff-who_will_work\").val();
        if(selected_value != \"self\" && selected_value!=''){
             
               $(\".field-loanwriteoff-other_name\").show();
               $(\".field-loanwriteoff-other_cnic\").show();
               $('#loanwriteoff-other_name').attr('required', 'required');
               $('#loanwriteoff-other_cnic').attr('required', 'required');
               
        }else{
               $(\"#loanwriteoff-other_name\").val('');
               $(\"#loanwriteoff-other_cnic\").val('');
               $('#loanwriteoff-other_name').removeAttr('required');
               $(\".field-loanwriteoff-other_name\").hide();
               $(\".field-loanwriteoff-other_cnic\").hide();
        }
        $(\"#loanwriteoff-who_will_work\").change(function(){
            var selected_value =  $(\"#loanwriteoff-who_will_work\").val();
            if(selected_value != \"self\" && selected_value!=''){
                   $(\"#loanwriteoff-other_name\").val('');
                   $(\"#loanwriteoff-other_cnic\").val(''); 
                   $('#loanwriteoff-other_name').attr('required', 'required');
                   $('#loanwriteoff-other_cnic').attr('required', 'required');
                   $(\".field-loanwriteoff-other_name\").show();
                   $(\".field-loanwriteoff-other_cnic\").show();
    
            }else{
                   $(\"#loanwriteoff-other_name\").val('');
                   $(\"#loanwriteoff-other_cnic\").val('');
                   $('#loanwriteoff-other_name').removeAttr('required');
                   $('#loanwriteoff-other_cnic').removeAttr('required');
                   $(\".field-loanwriteoff-other_name\").hide();
                   $(\".field-loanwriteoff-other_cnic\").hide();
            }
       });
    
     $('#loanwriteoff-type').change(function(){
     var type=this.value;
     if(type==0){
        $('#loanwriteoff-reason').attr('required', 'required');  
        $('.field-loanwriteoff-deposit_slip_no').show();
        $('.field-loanwriteoff-reason').show();
         $('.field-loanwriteoff-bank_name').show();
        $('.field-loanwriteoff-bank_account_no').show();
        $('#field-relation').text('Relation with borrower')
     }else{      
        $('#loanwriteoff-reason').removeAttr('required');
        $('.field-loanwriteoff-bank_name').hide();
        $('.field-loanwriteoff-bank_account_no').hide();
        $('.field-loanwriteoff-reason').hide();
        $('.field-loanwriteoff-deposit_slip_no').hide();
        $('#field-relation').text('Relation With Guardian');
     }
    });
    $('#loanwriteoff-sanction_no').blur(function(e) {
        var id = this.id.split('-');
        var sr = id[1];
        //sanction_no = this.value;
        if(this.value){
            sanction_no = this.value;
        }
        
        $.ajax({
           //alert(this.value);
           url: '/loan-write-off/get-member-info',
           type: 'POST',
           dataType: 'JSON',
           data: {sanc_no: sanction_no},
           success: function(data, status){
                if(status == 'success'){
                    if(data.error != null){
                        $('#name'+'-error').text(data.error);
                        $('#name'+'-error').show();
                        $('#name'+'-error').delay(4000).fadeOut(3000);
                    }else{
                        $('#name').val(data.name+' ('+data.cnic+')');
                        $(\"#loanwriteoff-borrower_name\").val(data.name);
                        $(\"#loanwriteoff-borrower_cnic\").val(data.cnic);
                    }
                }else{
                }
           }
        });
    });
    
    $('#loanwriteoff-sanction_no').blur(function(e) {
        var id = this.id.split('-');
        var sr = id[1];
        //sanction_no = this.value;
        if(this.value){
            sanction_no = this.value;
        }
        
        $.ajax({
           //alert(this.value);
           url: '/loan-write-off/get-member-info',
           type: 'POST',
           dataType: 'JSON',
           data: {sanc_no: sanction_no},
           success: function(data, status){
                if(status == 'success'){
                    if(data.error != null){
                        $('#name'+'-error').text(data.error);
                        $('#name'+'-error').show();
                        $('#name'+'-error').delay(4000).fadeOut(3000);
                    }else{
                        $('#name').val(data.name+' ('+data.cnic+')');
                        $(\"#loanwriteoff-borrower_name\").val(data.name);
                        $(\"#loanwriteoff-borrower_cnic\").val(data.cnic);
                    }
                }else{
                }
           }
        });
    });
});
";
$this->registerJs($js);
/* @var $form yii\widgets\ActiveForm */

?>

<div class="loan-write-off-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <?= $form->field($model, 'borrower_name')->hiddenInput(['maxlength' => true])->label(false) ?>
        <?= $form->field($model, 'borrower_cnic')->hiddenInput(['maxlength' => true])->label(false)  ?>
        <div class="col-sm-12">
            <div class="col-sm-3">
                <?= $form->field($model, "write_off_date")->widget(\yii\jui\DatePicker::className(), [
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Write Off Date']
                ]); ?>
            </div>
            <div class="col-sm-3">

                <?= $form->field($model, 'type')->dropDownList([0=>'Recovery',1=>'Funeral Charges'],['prompt'=>'Select Type']) ?>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-3">

                <?= $form->field($model, 'reason')->dropDownList(['disable'=>'Permanently Disable','death'=>'Death'],['prompt'=>'Select Reason']) ?>
            </div>
            <div class="col-sm-3">

                <?= $form->field($model, 'deposit_slip_no')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'bank_name')->dropDownList(\common\components\Helpers\ListHelper::getLists('bank_accounts')/*['hbl'=>'HBL','mcb'=>'MCB','national_bank'=>'National Bank']*/,['prompt' => 'Select Bank Name', 'class' => 'form-control form-control-sm'])->label('Bank Name'); ?>
            </div>
            <div class="col-sm-3">
                <?php
                $value = !empty($model->bank_account_no) ? $model->bank_account_no : null;
                echo $form->field($model, 'bank_account_no')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['loanwriteoff-bank_name'],
                        'placeholder' => 'Select Bank Account No',
                        'url' => Url::to(['/structure/fetch-account-by-bank'])
                    ],
                    'data' => $value ? [$model->bank_account_no => $value] : []
                ])->label('Bank Account No');
                ?>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-3">
                <?= $form->field($model, 'sanction_no')->textInput(['required'=>true]) ?>
            </div>
            <div class="col-sm-3">
                <label for="name">
                    Borrower Name/CNIC
                </label>
                <?= HTML::textInput('name', '', array('class' => 'form-control name', 'id' => "name", 'disabled' => 'disabled')) ?>
                <div class="help-block"></div>

            </div>
            <div class="col-sm-2">

                <?= $form->field($model, 'amount')->textInput() ?>
            </div>
            <div class="col-sm-2">

                <?= $form->field($model, 'cheque_no')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-2">

                <?= $form->field($model, 'voucher_no')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="col-sm-4">
                <label id="field-relation"></label>
                <?= $form->field($model, 'who_will_work')->dropDownList(\common\components\Helpers\ApplicationHelper::getWhoWillWork(), ['prompt' => 'Select Relation with borrower', 'class' => 'form-control form-control-sm'])->label(false)?>
            </div>
            <div class="col-sm-4">

                <?= $form->field($model, 'other_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-4">

                <?= $form->field($model, 'other_cnic')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
