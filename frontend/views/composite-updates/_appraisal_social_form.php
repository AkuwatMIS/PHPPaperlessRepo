<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsSocial */
/* @var $form yii\widgets\ActiveForm */

$js = "
$(document).ready(function(){
     $(\".field-appraisalssocial-house_rent_amount\").hide();
     var selected_value =  $(\"#appraisalssocial-house_ownership\").val();
        if(selected_value == 'rented'){
               $('#houserent').show();
               $(\".field-appraisalssocial-house_rent_amount\").show();
               $('#appraisalssocial-house_rent_amount').attr('required', 'required');
        }else{
               $(\"#appraisalssocial-house_rent_amount\").val('');
               $('#houserent').hide();
               $(\".field-appraisalssocial-house_rent_amount\").hide();
               $('#appraisalssocial-house_rent_amount').removeAttr('required');
        }
    $(\"#appraisalssocial-total_expenses\").prop(\"disabled\", true);
    $(\"#appraisalssocial-house_ownership\").change(function(){
        var selected_value =  $(\"#appraisalssocial-house_ownership\").val();
        if(selected_value == 'rented'){
               $('#houserent').show();
               $(\".field-appraisalssocial-house_rent_amount\").show();
               $('#appraisalssocial-house_rent_amount').attr('required', 'required');
        }else{
               $(\"#appraisalssocial-house_rent_amount\").val('');
               $('#houserent').hide();
               $(\".field-appraisalssocial-house_rent_amount\").hide();
               $('#appraisalssocial-house_rent_amount').removeAttr('required');
        }
    });
    $(\".field-appraisalssocial-amount\").hide();
    $(\".field-appraisalssocial-date_of_maturity\").hide();
    var selected_value =  $(\"#appraisalssocial-monthly_savings\").val();
        if(selected_value == 'committee'){
               $('#amount').show();
               $('#maturity').show();
               $(\".field-appraisalssocial-amount\").show();
               $(\".field-appraisalssocial-date_of_maturity\").show();
               $('#appraisalssocial-amount').attr('required', 'required');
               $('#appraisalssocial-date_of_maturity').attr('required', 'required');
        }
        else if(selected_value == 'bank_saving'){
            $(\"#appraisalssocial-date_of_maturity\").val('');
            $(\".field-appraisalssocial-date_of_maturity\").hide();
            $('#amount').show();
            $(\".field-appraisalssocial-amount\").show();
            $('#appraisalssocial-amount').attr('required', 'required');
            $('#appraisalssocial-date_of_maturity').removeAttr('required');
        }
        else if(selected_value == 'at_home'){
            $(\"#appraisalssocial-date_of_maturity\").val('');
            $(\".field-appraisalssocial-date_of_maturity\").hide();
            $('#amount').show();
            $(\".field-appraisalssocial-amount\").show();
            $('#appraisalssocial-amount').attr('required', 'required');
            $('#appraisalssocial-date_of_maturity').removeAttr('required');
        }
        else{
               $('#amount').hide();
               $('#maturity').hide();
               $(\"#appraisalssocial-amount\").val('');
               $(\"#appraisalssocial-date_of_maturity\").val('');
               $(\".field-appraisalssocial-amount\").hide();
               $(\".field-appraisalssocial-date_of_maturity\").hide();
               $('#appraisalssocial-amount').removeAttr('required');
               $('#appraisalssocial-date_of_maturity').removeAttr('required');
        }
    $(\"#appraisalssocial-monthly_savings\").change(function(){
        var selected_value =  $(\"#appraisalssocial-monthly_savings\").val();
        if(selected_value == 'committee'){
               $('#amount').show();
               $('#maturity').show();
               $(\".field-appraisalssocial-amount\").show();
               $(\".field-appraisalssocial-date_of_maturity\").show();
               $('#appraisalssocial-amount').attr('required', 'required');
               $('#appraisalssocial-date_of_maturity').attr('required', 'required');
        }
        else if(selected_value == 'bank_saving'){
            $(\"#appraisalssocial-date_of_maturity\").val('');
            $(\".field-appraisalssocial-date_of_maturity\").hide();
            $('#amount').show();
            $(\".field-appraisalssocial-amount\").show();
            $('#appraisalssocial-amount').attr('required', 'required');
            $('#appraisalssocial-date_of_maturity').removeAttr('required');
        }
        else if(selected_value == 'at_home'){
            $(\"#appraisalssocial-date_of_maturity\").val('');
            $(\".field-appraisalssocial-date_of_maturity\").hide();
            $('#amount').show();
            $(\".field-appraisalssocial-amount\").show();
            $('#appraisalssocial-amount').attr('required', 'required');
            $('#appraisalssocial-date_of_maturity').removeAttr('required');
        }
        else{
               $('#amount').hide();
               $('#maturity').hide();
               $(\"#appraisalssocial-amount\").val('');
               $(\"#appraisalssocial-date_of_maturity\").val('');
               $(\".field-appraisalssocial-amount\").hide();
               $(\".field-appraisalssocial-date_of_maturity\").hide();
               $('#appraisalssocial-amount').removeAttr('required');
               $('#appraisalssocial-date_of_maturity').removeAttr('required');
        }
    });
    
     $(\".field-appraisalssocial-loan_amount\").hide();
     var selected_value =  $(\"#appraisalssocial-other_loan\").val();
        if(selected_value == '1'){
               $('#loanamount').show();
               $(\".field-appraisalssocial-loan_amount\").show();
        }else{
               $(\"#appraisalssocial-loan_amount\").val('');
               $('#loanamount').hide();
               $(\".field-appraisalssocial-loan_amount\").hide();
        }
    $(\"#appraisalssocial-other_loan\").change(function(){
        var selected_value =  $(\"#appraisalssocial-other_loan\").val();
        if(selected_value == '1'){
               $('#loanamount').show();
               $(\".field-appraisalssocial-loan_amount\").show();
        }else{
               $(\"#appraisalssocial-loan_amount\").val('');
               $('#loanamount').hide();
               $(\".field-appraisalssocial-loan_amount\").hide();
        }
    });
    
    
    var job_income = $.isNumeric($(\"#appraisalssocial-job_income\").val()) ?  $(\"#appraisalssocial-job_income\").val(): 0;
    var business_income = $.isNumeric($(\"#appraisalssocial-business_income\").val()) ?  $(\"#appraisalssocial-business_income\").val(): 0;
    var house_rent_income = $.isNumeric($(\"#appraisalssocial-house_rent_income\").val()) ?  $(\"#appraisalssocial-house_rent_income\").val(): 0;
    var other_income = $.isNumeric($(\"#appraisalssocial-other_income\").val()) ?  $(\"#appraisalssocial-other_income\").val(): 0;
    var total=parseInt(job_income) + parseInt(business_income)+ parseInt(house_rent_income)+ parseInt(other_income);
    $(\"#total-income\").text('');
    $(\"#total-income\").append(total);
    
    var educational_expenses = $.isNumeric($(\"#appraisalssocial-educational_expenses\").val()) ?  $(\"#appraisalssocial-educational_expenses\").val(): 0;
    var medical_expenses = $.isNumeric($(\"#appraisalssocial-medical_expenses\").val()) ?  $(\"#appraisalssocial-medical_expenses\").val(): 0;
    var kitchen_expenses = $.isNumeric($(\"#appraisalssocial-kitchen_expenses\").val()) ?  $(\"#appraisalssocial-kitchen_expenses\").val(): 0;
    var utility_bills = $.isNumeric($(\"#appraisalssocial-utility_bills\").val()) ?  $(\"#appraisalssocial-utility_bills\").val(): 0;
    var other_expenses = $.isNumeric($(\"#appraisalssocial-other_expenses\").val()) ?  $(\"#appraisalssocial-other_expenses\").val(): 0;

    var house_rent_amount = $.isNumeric($(\"#appraisalssocial-house_rent_amount\").val()) ?  $(\"#appraisalssocial-house_rent_amount\").val(): 0;
    var loan_amount = $.isNumeric($(\"#appraisalssocial-loan_amount\").val()) ?  $(\"#appraisalssocial-loan_amount\").val(): 0;
    var saving = $.isNumeric($(\"#appraisalssocial-amount\").val()) ?  $(\"#appraisalssocial-amount\").val(): 0;
    var others=(parseInt(house_rent_amount) + parseInt(loan_amount) + parseInt(saving));
    var expenses=(parseInt(others)+parseInt(educational_expenses) + parseInt(medical_expenses) + parseInt(kitchen_expenses) + parseInt(utility_bills) + parseInt(other_expenses));
    $(\"#total-expenses\").text('');
    $(\"#total-expenses\").append(expenses);
            
   var source = $(\"#appraisalssocial-source_of_income\").val();
        if (source == 'employment') {
            $(\".field-appraisalssocial-job_income\").show();
            $('#appraisalssocial-job_income').attr('required','required');
        
            $(\"#appraisalssocial-business_income\").val('');
            $(\".field-appraisalssocial-business_income\").hide();
            $('#appraisalssocial-business_income').removeAttr('required');
        } else if(source==\"business\") {
            $(\".field-appraisalssocial-business_income\").show();
            $('#appraisalssocial-business_income').attr('required','required');
        
            $(\"#appraisalssocial-job_income\").val('');
            $(\".field-appraisalssocial-job_income\").hide();
            $('#appraisalssocial-job_income').removeAttr('required');
        }else{
            $(\".field-appraisalssocial-job_income\").show();
            $('#appraisalssocial-job_income').attr('required','required');
        
            $(\".field-appraisalssocial-business_income\").show();
            $('#appraisalssocial-business_income').attr('required','required');
        }     
            
            
            
    
    
    document.addEventListener('change', function (e) {
        if (e.target.id == 'appraisalssocial-source_of_income') {
                var source = $(\"#appraisalssocial-source_of_income\").val();
                if (source == 'employment') {
                    $(\".field-appraisalssocial-job_income\").show();
                    $('#appraisalssocial-job_income').attr('required','required');
    
                    $(\"#appraisalssocial-business_income\").val('');
                    $(\".field-appraisalssocial-business_income\").hide();
                    $('#appraisalssocial-business_income').removeAttr('required');
                } else if(source==\"business\") {
                    $(\".field-appraisalssocial-business_income\").show();
                    $('#appraisalssocial-business_income').attr('required','required');
    
                    $(\"#appraisalssocial-job_income\").val('');
                    $(\".field-appraisalssocial-job_income\").hide();
                    $('#appraisalssocial-job_income').removeAttr('required');
                }else{
                    $(\".field-appraisalssocial-job_income\").show();
                    $('#appraisalssocial-job_income').attr('required','required');
    
                    $(\".field-appraisalssocial-business_income\").show();
                    $('#appraisalssocial-business_income').attr('required','required');
                }
        }
        else if (e.target.id == 'appraisalssocial-business_income' || e.target.id == 'appraisalssocial-job_income' || e.target.id == 'appraisalssocial-house_rent_income' || e.target.id == 'appraisalssocial-other_income') {
            var job_income = $.isNumeric($(\"#appraisalssocial-job_income\").val()) ?  $(\"#appraisalssocial-job_income\").val(): 0;
            var business_income = $.isNumeric($(\"#appraisalssocial-business_income\").val()) ?  $(\"#appraisalssocial-business_income\").val(): 0;
            var house_rent_income = $.isNumeric($(\"#appraisalssocial-house_rent_income\").val()) ?  $(\"#appraisalssocial-house_rent_income\").val(): 0;
            var other_income = $.isNumeric($(\"#appraisalssocial-other_income\").val()) ?  $(\"#appraisalssocial-other_income\").val(): 0;
            var total=parseInt(job_income) + parseInt(business_income)+ parseInt(house_rent_income)+ parseInt(other_income);

            $(\"#total-income\").text('');
            $(\"#total-income\").append(total);
        }
        else if (e.target.id == 'appraisalssocial-utility_bills' || e.target.id == 'appraisalssocial-educational_expenses' || e.target.id == 'appraisalssocial-medical_expenses' || e.target.id == 'appraisalssocial-kitchen_expenses' || e.target.id == 'appraisalssocial-other_expenses' || e.target.id == 'appraisalssocial-amount' || e.target.id == 'appraisalssocial-loan_amount' || e.target.id == 'appraisalssocial-house_rent_amount') {
            var educational_expenses = $.isNumeric($(\"#appraisalssocial-educational_expenses\").val()) ?  $(\"#appraisalssocial-educational_expenses\").val(): 0;
            var medical_expenses = $.isNumeric($(\"#appraisalssocial-medical_expenses\").val()) ?  $(\"#appraisalssocial-medical_expenses\").val(): 0;
            var kitchen_expenses = $.isNumeric($(\"#appraisalssocial-kitchen_expenses\").val()) ?  $(\"#appraisalssocial-kitchen_expenses\").val(): 0;
            var utility_bills = $.isNumeric($(\"#appraisalssocial-utility_bills\").val()) ?  $(\"#appraisalssocial-utility_bills\").val(): 0;
            var other_expenses = $.isNumeric($(\"#appraisalssocial-other_expenses\").val()) ?  $(\"#appraisalssocial-other_expenses\").val(): 0;

            var house_rent_amount = $.isNumeric($(\"#appraisalssocial-house_rent_amount\").val()) ?  $(\"#appraisalssocial-house_rent_amount\").val(): 0;
            var loan_amount = $.isNumeric($(\"#appraisalssocial-loan_amount\").val()) ?  $(\"#appraisalssocial-loan_amount\").val(): 0;
            var saving = $.isNumeric($(\"#appraisalssocial-amount\").val()) ?  $(\"#appraisalssocial-amount\").val(): 0;
            var others=(parseInt(house_rent_amount) + parseInt(loan_amount) + parseInt(saving));

            var expenses=(parseInt(others)+parseInt(educational_expenses) + parseInt(medical_expenses) + parseInt(kitchen_expenses) + parseInt(utility_bills) + parseInt(other_expenses));


            $(\"#total-expenses\").text('');
            $(\"#total-expenses\").append(expenses);
        }

    }, false);
    
});

";
$js .= '
var form = document.getElementById("form");
document.getElementById("btn-create").addEventListener("click", function (event) {
var educational_expenses = $.isNumeric($("#appraisalssocial-educational_expenses").val()) ?  $("#appraisalssocial-educational_expenses").val(): 0;
var medical_expenses = $.isNumeric($("#appraisalssocial-medical_expenses").val()) ?  $("#appraisalssocial-medical_expenses").val(): 0;
var kitchen_expenses = $.isNumeric($("#appraisalssocial-kitchen_expenses").val()) ?  $("#appraisalssocial-kitchen_expenses").val(): 0;
var utility_bills = $.isNumeric($("#appraisalssocial-utility_bills").val()) ?  $("#appraisalssocial-utility_bills").val(): 0;
var other_expenses = $.isNumeric($("#appraisalssocial-other_expenses").val()) ?  $("#appraisalssocial-other_expenses").val(): 0;
var expenses=(parseInt(educational_expenses) + parseInt(medical_expenses) + parseInt(kitchen_expenses) + parseInt(utility_bills) + parseInt(other_expenses));

var house_rent_amount = $.isNumeric($("#appraisalssocial-house_rent_amount").val()) ?  $("#appraisalssocial-house_rent_amount").val(): 0;
var loan_amount = $.isNumeric($("#appraisalssocial-loan_amount").val()) ?  $("#appraisalssocial-loan_amount").val(): 0;
var others=(parseInt(house_rent_amount) + parseInt(loan_amount));

var saving = $.isNumeric($("#appraisalssocial-amount").val()) ?  $("#appraisalssocial-amount").val(): 0;

var job_income = $.isNumeric($("#appraisalssocial-job_income").val()) ?  $("#appraisalssocial-job_income").val(): 0;
var business_income = $.isNumeric($("#appraisalssocial-business_income").val()) ?  $("#appraisalssocial-business_income").val(): 0;
var house_rent_income = $.isNumeric($("#appraisalssocial-house_rent_income").val()) ?  $("#appraisalssocial-house_rent_income").val(): 0;
var other_income = $.isNumeric($("#appraisalssocial-other_income").val()) ?  $("#appraisalssocial-other_income").val(): 0;
var income=(parseInt(job_income) + parseInt(business_income) + parseInt(house_rent_income) + parseInt(other_income));
if(parseInt(income)!=(parseInt(expenses)+parseInt(others)+parseInt(saving))){
alert("(Expenses + Savings + Others) Not Equal to "+ income);
  event.preventDefault();
}
var earning_hands = $.isNumeric($("#appraisalssocial-no_of_earning_hands").val()) ?  $("#appraisalssocial-no_of_earning_hands").val(): 0;
var males = $.isNumeric($("#appraisalssocial-gents").val()) ?  $("#appraisalssocial-gents").val(): 0;
var females = $.isNumeric($("#appraisalssocial-ladies").val()) ?  $("#appraisalssocial-ladies").val(): 0;
var member=parseInt(males)+parseInt(females);
    if(earning_hands>member){
        alert("Earning hands more then total family members");
        event.preventDefault();
    }
});



';
$this->registerJs($js);

?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Update Social Appraisal</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="appraisals-social-form">

            <?php $form = \yii\widgets\ActiveForm::begin(['action' => 'update-social-appraisal?id=' . $model->application_id]); ?>
            <div id="total" class="label label-success pull-right" style="display: block;">
                <div class="row">
                    <div class="col-sm-4" style="margin-left: 20px">
                        Total Income:<b id="total-income">0</b>
                    </div>
                    <div class="col-sm-4" style="margin-left: 50px">
                        Total Expenses:<b id="total-expenses">0</b>
                    </div>
                </div>
            </div>
            <h3 class="m-t-lg with-border">Property Information</h3>
            <div class="row">

                <div class="col-sm-3">
                    <?= $form->field($model, 'house_ownership')->dropDownList(\common\components\Helpers\ListHelper::getLists('house_ownership'), ['prompt' => 'Select House Ownership', 'class' => 'form-control form-control-sm']) ?>
                </div>
                <?= $form->field($model, 'poverty_index')->hiddenInput(['value' => 0])->label(false) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'land_size')->textInput(['placeholder' => "Enter Land Size"])->label('Land Area(Marlas)') ?>
                </div>
                <div class="col-sm-3" id="houserent" style="display:none">
                    <?= $form->field($model, 'house_rent_amount')->textInput(['placeholder' => "Enter House Rent", 'maxlength' => true, 'type' => 'number', 'min' => 0]) ?>
                </div>
            </div>
            <h3 class="m-t-lg with-border">Family Information</h3>
            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'ladies')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_earning_hands'), ['prompt' => "Enter Total Ladies", 'class' => 'form-control form-control-sm'])->label('No. of Female Members') ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'gents')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_earning_hands'), ['prompt' => "Enter Total Gents", 'class' => 'form-control form-control-sm'])->label('No. of Male Members') ?>
                </div>
            </div>
            <br>
            <h3 class="m-t-lg with-border">Income Information</h3>

            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'source_of_income')->dropDownList(\common\components\Helpers\ListHelper::getLists('source_of_income'), ['prompt' => 'Select Source Of Income', 'class' => 'form-control form-control-sm']) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'no_of_earning_hands')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_earning_hands'), ['prompt' => 'Select No Of Earning Hands', 'class' => 'form-control form-control-sm']) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'business_income')->textInput(['maxlength' => true, 'placeholder' => "Enter Business Income", 'type' => 'number', 'min' => 0]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'job_income')->textInput(['maxlength' => true, 'placeholder' => "Enter Job Income", 'type' => 'number', 'min' => 0]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'house_rent_income')->textInput(['maxlength' => true, 'placeholder' => "Enter House Rent Income", 'type' => 'number', 'min' => 0]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'other_income')->textInput(['maxlength' => true, 'placeholder' => "Enter Other Income", 'type' => 'number', 'min' => 0]) ?>
                </div>
            </div>
            <h3 class="m-t-lg with-border">Expenses Information</h3>

            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'educational_expenses')->textInput(['maxlength' => true, 'placeholder' => "Enter Educational Expenses", 'type' => 'number', 'min' => 0]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'medical_expenses')->textInput(['maxlength' => true, 'placeholder' => "Enter Medical Expenses", 'type' => 'number', 'min' => 0]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'kitchen_expenses')->textInput(['maxlength' => true, 'placeholder' => "Enter Kitchen Expenses", 'type' => 'number', 'min' => 0]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'utility_bills')->textInput(['maxlength' => true, 'placeholder' => "Enter Utility Bills Expenses", 'type' => 'number', 'min' => 0])->label('Amount of Utility Bills') ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'other_expenses')->textInput(['maxlength' => true, 'placeholder' => "Enter Other Expenses", 'type' => 'number', 'min' => 0]) ?>
                </div>
            </div>
            <h3 class="m-t-lg with-border">Saving Types</h3>

            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'monthly_savings')->dropDownList(\common\components\Helpers\ListHelper::getLists('monthly_savings'), ['prompt' => 'Select Monthly Savings', 'class' => 'form-control form-control-sm']) ?>
                </div>
                <div class="col-sm-3" id="amount" style="display:none">
                    <?= $form->field($model, 'amount')->textInput(['maxlength' => true, 'placeholder' => "Enter Amount", 'type' => 'number', 'min' => 0]) ?>
                </div>
                <div class="col-sm-3" id="maturity" style="display:none">
                    <?= $form->field($model, 'date_of_maturity')->widget(\kartik\date\DatePicker::className(), [
                        'name' => 'report_date',
                        'options' => ['placeholder' => 'Maturity Date', 'value' => date('Y-m-d')],
                        'type' => \kartik\date\DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-d',
                        ]]); ?>
                </div>
            </div>
            <h3 class="m-t-lg with-border">Other Loan Information</h3>

            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'other_loan')->dropDownList(\common\components\Helpers\ListHelper::getLists('other_loan'), ['prompt' => 'Select Other Loan', 'class' => 'form-control form-control-sm']) ?>
                </div>
                <div class="col-sm-3" id="loanamount" style="display:none">
                    <?= $form->field($model, 'loan_amount')->textInput(['maxlength' => true, 'placeholder' => "Enter Loan Amount", 'type' => 'number', 'min' => 0]) ?>
                </div>
            </div>
            <br>
            <h3 class="m-t-lg with-border">Character Information</h3>
            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'economic_dealings')->dropDownList(\common\components\Helpers\ListHelper::getLists('economic_dealings'), ['prompt' => 'Select Economic Dealings', 'class' => 'form-control form-control-sm']) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'social_behaviour')->dropDownList(\common\components\Helpers\ListHelper::getLists('social_behaviour'), ['prompt' => 'Select Social Behaviour', 'class' => 'form-control form-control-sm'])->label('Social Behaviour') ?>
                </div>
            </div>
            <h3 class="m-t-lg with-border">Fatal Disease</h3>

            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'fatal_disease')->dropDownList(\common\components\Helpers\ListHelper::getLists('fatal_disease'), ['prompt' => 'Select Fetal Dicease', 'class' => 'form-control form-control-sm']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'description')->textarea(['placeholder' => "Enter Description"]) ?>
                </div>
            </div>
            <!--form-->

            <?php if (!Yii::$app->request->isAjax) { ?>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'btn-create']) ?>
                </div>
            <?php } ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>