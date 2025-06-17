<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\SocialAppraisal */
/* @var $form yii\widgets\ActiveForm */

$js="
$(document).ready(function(){
     $(\".field-socialappraisal-house_rent_amount\").hide();
    $(\"#socialappraisal-total_expenses\").prop(\"disabled\", true);
    $(\"#socialappraisal-house_ownership\").change(function(){
        var selected_value =  $(\"#socialappraisal-house_ownership\").val();
        if(selected_value == 'rented'){
               $('#houserent').show();
               $(\".field-socialappraisal-house_rent_amount\").show();
        }else{
               $(\"#socialappraisal-house_rent_amount\").val('');
               $('#houserent').hide();
               $(\".field-socialappraisal-house_rent_amount\").hide();
        }
    });
    $(\".field-socialappraisal-amount\").hide();
    $(\".field-socialappraisal-date_of_maturity\").hide();
    $(\"#socialappraisal-monthly_savings\").change(function(){
        var selected_value =  $(\"#socialappraisal-monthly_savings\").val();
        if(selected_value == 'committee'){
               $('#amount').show();
               $('#maturity').show();
               $(\".field-socialappraisal-amount\").show();
               $(\".field-socialappraisal-date_of_maturity\").show();
        }
        else if(selected_value == 'bank_saving'){
            $(\"#socialappraisal-date_of_maturity\").val('');
            $(\".field-socialappraisal-date_of_maturity\").hide();
            $('#amount').show();
            $(\".field-socialappraisal-amount\").show();
        }
        else if(selected_value == 'at_home'){
            $(\"#socialappraisal-date_of_maturity\").val('');
            $(\".field-socialappraisal-date_of_maturity\").hide();
            $('#amount').show();
            $(\".field-socialappraisal-amount\").show();
        }
        else{
               $('#amount').hide();
               $('#maturity').hide();
               $(\"#socialappraisal-amount\").val('');
               $(\"#socialappraisal-date_of_maturity\").val('');
               $(\".field-socialappraisal-amount\").hide();
               $(\".field-socialappraisal-date_of_maturity\").hide();
        }
    });
    
     $(\".field-socialappraisal-loan_amount\").hide();
    $(\"#socialappraisal-other_loan\").change(function(){
        var selected_value =  $(\"#socialappraisal-other_loan\").val();
        if(selected_value == '1'){
               $('#loanamount').show();
               $(\".field-socialappraisal-loan_amount\").show();
        }else{
               $(\"#socialappraisal-loan_amount\").val('');
               $('#loanamount').hide();
               $(\".field-socialappraisal-loan_amount\").hide();
        }
    });
});

";
$js.='
var form = document.getElementById("form");
document.getElementById("btn-create").addEventListener("click", function (event) {
var educational_expenses = $.isNumeric($("#socialappraisal-educational_expenses").val()) ?  $("#socialappraisal-educational_expenses").val(): 0;
var medical_expenses = $.isNumeric($("#socialappraisal-medical_expenses").val()) ?  $("#socialappraisal-medical_expenses").val(): 0;
var kitchen_expenses = $.isNumeric($("#socialappraisal-kitchen_expenses").val()) ?  $("#socialappraisal-kitchen_expenses").val(): 0;
var utility_bills = $.isNumeric($("#socialappraisal-utility_bills").val()) ?  $("#socialappraisal-utility_bills").val(): 0;
var other_expenses = $.isNumeric($("#socialappraisal-other_expenses").val()) ?  $("#socialappraisal-other_expenses").val(): 0;
var expenses=(parseInt(educational_expenses) + parseInt(medical_expenses) + parseInt(kitchen_expenses) + parseInt(utility_bills) + parseInt(other_expenses));

var house_rent_amount = $.isNumeric($("#socialappraisal-house_rent_amount").val()) ?  $("#socialappraisal-house_rent_amount").val(): 0;
var loan_amount = $.isNumeric($("#socialappraisal-loan_amount").val()) ?  $("#socialappraisal-loan_amount").val(): 0;
var others=(parseInt(house_rent_amount) + parseInt(loan_amount));

var saving = $.isNumeric($("#socialappraisal-amount").val()) ?  $("#socialappraisal-amount").val(): 0;

var job_income = $.isNumeric($("#socialappraisal-job_income").val()) ?  $("#socialappraisal-job_income").val(): 0;
var business_income = $.isNumeric($("#socialappraisal-business_income").val()) ?  $("#socialappraisal-business_income").val(): 0;
var house_rent_income = $.isNumeric($("#socialappraisal-house_rent_income").val()) ?  $("#socialappraisal-house_rent_income").val(): 0;
var other_income = $.isNumeric($("#socialappraisal-other_income").val()) ?  $("#socialappraisal-other_income").val(): 0;
var income=(parseInt(job_income) + parseInt(business_income) + parseInt(house_rent_income) + parseInt(other_income));
if(parseInt(income)!=(parseInt(expenses)+parseInt(others)+parseInt(saving))){
alert("(Expenses + Savings + Others) Not Equal to "+ income);
  event.preventDefault();
}
});



';
$this->registerJs($js);

?>

<div class="social-appraisal-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <div class="row">
        <div class="col-sm-12">
            <?php
            $url = \yii\helpers\Url::to(['/social-appraisal/search-application']);
            if (!empty($model->application_id)) {
                $application = \common\models\Applications::findOne($model->application_id);
                $cityDesc = '<strong>Application No</strong>: ' . $application->application_no . ' <strong>Member Name</strong>: ' . $application->member->full_name;
            } else {
                $cityDesc = '';
            }
            ?>
            <?=
            $form->field($model, "application_id")->widget(\kartik\select2\Select2::classname(), [
                'initValueText' => $cityDesc, // set the initial display text
                'options' => ['placeholder' => 'Search for a Application No  / Member CNIC...', 'class' => 'file'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'language' => [
                        'errorLoading' => new \yii\web\JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(city) { return city.text; }'),
                    'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                ],
            ])->label('Select Application');
            ?>
        </div>
        <!--<div class="col-sm-3">
            <? /*= $form->field($model, 'application_id')->textInput() */ ?>
        </div>-->
    </div>
    <h3 class="m-t-lg with-border">Property Information</h3>
    <div class="row">

        <div class="col-sm-3">
            <?= $form->field($model, 'house_ownership')->dropDownList(\common\components\Helpers\ListHelper::getLists('house_ownership'), ['prompt' => 'Select House Ownership', 'class' => 'form-control form-control-sm']) ?>
        </div>
        <?= $form->field($model, 'poverty_index')->hiddenInput(['value'=>0])->label(false)?>
        <!--<div class="col-sm-3">
            <?/*= $form->field($model, 'poverty_index')->textInput(['placeholder' => "Enter overty Index"]) */?>
        </div>-->
        <div class="col-sm-3">
            <?= $form->field($model, 'land_size')->textInput(['placeholder' => "Enter Land Size"]) ?>
        </div>
        <div class="col-sm-3" id="houserent" style="display:none">
            <?= $form->field($model, 'house_rent_amount')->textInput(['placeholder' => "Enter House Rent",'maxlength' => true]) ?>
        </div>
    </div>
    <h3 class="m-t-lg with-border">Family Information</h3>
    <div class="row">
        <div class="col-sm-3" style="display: none">
           <!-- --><?/*= $form->field($model, 'total_family_members')->textInput(['placeholder' => "Enter Total Family Members"]) */?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'ladies')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_earning_hands'),['prompt' => "Enter Total Ladies",'class' => 'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'gents')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_earning_hands'),['prompt' => "Enter Total Gents",'class' => 'form-control form-control-sm']) ?>
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
            <?= $form->field($model, 'business_income')->textInput(['maxlength' => true,'placeholder' => "Enter Business Income"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'job_income')->textInput(['maxlength' => true,'placeholder' => "Enter Job Income"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'house_rent_income')->textInput(['maxlength' => true,'placeholder' => "Enter House Rent Income"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'other_income')->textInput(['maxlength' => true,'placeholder' => "Enter Other Income"]) ?>
        </div>
        <!--<div class="col-sm-3">
            <?/*= $form->field($model, 'total_household_income')->textInput(['maxlength' => true,'placeholder' => "Enter Total Household"]) */?>
        </div>-->
    </div>
    <h3 class="m-t-lg with-border">Expenses Information</h3>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'educational_expenses')->textInput(['maxlength' => true,'placeholder' => "Enter Educational Expenses"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'medical_expenses')->textInput(['maxlength' => true,'placeholder' => "Enter Medical Expenses"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'kitchen_expenses')->textInput(['maxlength' => true,'placeholder' => "Enter Kitchen Expenses"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'utility_bills')->textInput(['maxlength' => true,'placeholder' => "Enter Utility Bills Expenses"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'other_expenses')->textInput(['maxlength' => true,'placeholder' => "Enter Other Expenses"]) ?>
        </div>
        <div class="col-sm-3" style="display: none">
            <?/*= $form->field($model, 'total_expenses')->textInput(['maxlength' => true,'placeholder' => "Enter Total Expenses"]) */?>
        </div>
    </div>
    <h3 class="m-t-lg with-border">Saving Types</h3>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'monthly_savings')->dropDownList(\common\components\Helpers\ListHelper::getLists('monthly_savings'), ['prompt' => 'Select Monthly Savings', 'class' => 'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3" id="amount" style="display:none">
            <?= $form->field($model, 'amount')->textInput(['maxlength' => true,'placeholder' => "Enter Amount"]) ?>
        </div>
        <div class="col-sm-3" id="maturity" style="display:none">
            <?= $form->field($model, 'date_of_maturity')->widget(\kartik\date\DatePicker::className(), [
            'name' => 'report_date',
            // 'value' => date('Y-M'),
            'options' => ['placeholder' => 'Maturity Date'/*,'value'=>date('Y-m')*/],
            //'options' => ['class'=>'form-control', 'placeholder' => 'Deposite date','format' => 'yyyy-mm',],
            'type' => \kartik\date\DatePicker::TYPE_INPUT,

            'pluginOptions' => [
            'format' => 'yyyy-mm-d',
            ]]);?>
            <!--<?/*= $form->field($model, 'date_of_maturity')->textInput(['maxlength' => true]) */?>-->
        </div>
    </div>
    <h3 class="m-t-lg with-border">Other Loan Information</h3>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'other_loan')->dropDownList(\common\components\Helpers\ListHelper::getLists('other_loan'), ['prompt' => 'Select Other Loan', 'class' => 'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3" id="loanamount" style="display:none">
            <?= $form->field($model, 'loan_amount')->textInput(['maxlength' => true,'placeholder' => "Enter Loan Amount"]) ?>
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
            <?= $form->field($model, 'description')->textarea(['placeholder' => "Enter Description"])?>
        </div>
    </div>
    <div class="row">
        <?= $form->field($model, 'latitude')->hiddenInput(['value' => '0'])->label(false) ?>
        <?= $form->field($model, 'longitude')->hiddenInput(['value' => '0'])->label(false) ?>
        <?= $form->field($model, 'status')->hiddenInput(['value' => 'approved'])->label(false) ?>

    </div>
    <!--  <?php /*$form = ActiveForm::begin(); */ ?>

    <? /*= $form->field($model, 'application_id')->textInput() */ ?>

    <? /*= $form->field($model, 'poverty_index')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'house_ownership')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'land_size')->textInput() */ ?>

    <? /*= $form->field($model, 'total_family_members')->textInput() */ ?>

    <? /*= $form->field($model, 'no_of_earning_hands')->textInput() */ ?>

    <? /*= $form->field($model, 'ladies')->textInput() */ ?>

    <? /*= $form->field($model, 'gents')->textInput() */ ?>

    <? /*= $form->field($model, 'source_of_income')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'utility_bills')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'educational_expenses')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'medical_expenses')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'kitchen_expenses')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'monthly_savings')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'amount')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'other_expenses')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'total_expenses')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'economic_dealings')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'social_behaviour')->textInput(['maxlength' => true]) */ ?>

    <? /*= $form->field($model, 'latitude')->textInput() */ ?>

    <? /*= $form->field($model, 'longitude')->textInput() */ ?>

    <? /*= $form->field($model, 'status')->textInput() */ ?>

    <? /*= $form->field($model, 'approved_by')->textInput() */ ?>

    <? /*= $form->field($model, 'approved_on')->textInput() */ ?>

    <? /*= $form->field($model, 'assigned_to')->textInput() */ ?>

    <? /*= $form->field($model, 'created_by')->textInput() */ ?>

    <? /*= $form->field($model, 'updated_by')->textInput() */ ?>

    <? /*= $form->field($model, 'created_at')->textInput() */ ?>

    <? /*= $form->field($model, 'updated_at')->textInput() */ ?>

   <? /*= $form->field($model, 'deleted')->textInput() */ ?> -->


    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'btn-create']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
