<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Awp */
$js = "$(document).ready(function(){

$('#awp-avg_loan_size').change(function(e) {
    
    var amount = $(this).val();
    
    if(amount > 0 && amount < 10000){
        alert(\" کی رقم 10,000 روپے سے کم نہیں ڈال سکتے۔ Average Loan Size\");
        $(this).val(0);
    }
    if(amount < 0){
        alert(' کی  رقم  منفی نہیں ڈال سکتے۔ Average Loan Size');
        $(this).val(0);
    }
    
    var idArray = [];
    var seldid=this.id;
     
    var selfidno=seldid.substring(4, 5);
    var selfidnonew=seldid.substring(4, 6);
    var selfidnocomp=seldid.substring(4, 8);
    if(amount >= 10000){
        var ids = selfidnocomp.split(\"-\");
        var row = ids[0];
        var column = ids[1];
        $('#awpprojectmapping-'+row+'-'+column+'-'+'no_of_loans').prop('readonly', false);
        //alert('#awpprojectmapping-'+row+'-'+column+'-'+no_of_loans);
        //alert(column);
    }else if (amount <= 0){
    var ids = selfidnocomp.split(\"-\");
        var row = ids[0];
        var column = ids[1];
        $('#awpprojectmapping-'+row+'-'+column+'-'+'no_of_loans').attr('readonly','readonly');
    }
   

var size=$('#awp-avg_loan_size').val();
var loans=$('#awp-no_of_loans').val();
var amount=(parseInt(size))*(parseInt(loans));


var awploans=$('#awp-no_of_loans').val()
var awpavg_lon=$('#awp-avg_loan_size').val();
var amount=awploans*awpavg_lon;

});

$('.avg_recovery').blur(function(e) {
    var idArray1 = [];
    var seldid1=this.id;
    var selfidno1=seldid1.substring(4, 8);
    var selfidnocomp=seldid1.substring(4, 8);
    var active_loans=$('#awp-'+selfidnocomp+'-active_loans').val();
    var avg_recovery=$(this).val();
    /*alert(active_loans);
    alert(avg_recovery);*/
    $('#awp-'+selfidnocomp+'-monthly_recovery').val((parseInt(active_loans))*(parseInt(avg_recovery)));
});
$('.no_of_loanss').blur(function(e) {
    var amount = $(this).val();
    if(amount > 1000){
        alert('  کی تعداد 1,000 سے ذیادہ نہیں ڈال سکتے۔ No. of Loans');
        $(this).val(0);
    }
    if(amount < 0){
        alert('  کی تعداد منفی نہیں ڈال سکتے۔ No. of Loans');
        $(this).val(0);
    }
});

});";
$js .= "$(document).ready(function(){
$('#awp-no_of_loans').change(function(e) {
var a=this.value;
 var b=$('#awp-avg_loan_size').val();
var c=a*b;
 $('#awp-disbursement_amount').val(c);
});
$('#awp-avg_recovery').change(function(e) {
var a=this.value;
 var b=$('#awp-active_loans').val();
var c=a*b;
 $('#awp-monthly_recovery').val(c);
});

});
";
$this->registerJs($js);
?>

<h3>
   <strong> Annual Work Plan update <?php  echo date(date('Y')).'-'. date('y', strtotime(date('Y').'+1 year')) ;?></strong>
</h3>
<h4><?= ($branch_id != 0) ?'<strong>Branch Name:</strong> '.\common\models\Branches::find()->where(['id'=>$branch_id])->one()->name : '' ?></h4>
<div class="awp-create">
    <?php $form = \yii\widgets\ActiveForm::begin(['id' => 'dynamic-form','options' => ['class' => 'form-horizontal', 'name' => 'single_form']]); ?>
    <input type="hidden" name="single_post" value="single_awp">
    <?= $form->field($model, "id")->hiddenInput(['class' => "form-control small-input ", 'readonly' => 'readonly'])->label(false) ?>
    <label>Opening OLP</label>
    <?= $form->field($model, "monthly_olp")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false); ?>
    <label>Opening Active Loans<?php echo $model->month ?></label>
    <?= $form->field($model, "active_loans")->textInput(['class' => "form-control small-input active_loans", 'readonly' => 'readonly'])->label(false);?>
    <label>Exp. Closed Loans</label>
    <?= $form->field($model, "monthly_closed_loans")->textInput(['class' => "form-control small-input monthly_closed_loansss",'required'=>'required'])->label(false);?>
    <label>Average Recovery</label>
    <?=$form->field($model, "avg_recovery")->textInput(['class' => "form-control small-input avg_recovery",'required'=>'required'])->label(false);?>
    <label>Expected Recovery</label>
    <?= $form->field($model, "monthly_recovery")->textInput(['class' => "form-control small-input ", 'readonly' => 'readonly'])->label(false); ?>
    <label>Avg Loan Size</label>
    <?= $form->field($model, "avg_loan_size")->textInput(['class' => "form-control small-input avg_loan_sizeee"])->label(false); ?>
    <label>No of loans</label>
    <?= $form->field($model, "no_of_loans")->textInput(['class' => "form-control small-input no_of_loanss"])->label(false);?>
    <label>Disb. Amount</label>
    <?= $form->field($model, "disbursement_amount")->textInput(['class' => 'form-control small-input amount_disbursed', 'readonly' => 'readonly'])->label(false) ?>
    <label>Funds Required</label>
    <?= $form->field($model, "funds_required")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false) ?>
   <div class="col-sm-3" style="margin-top:20px">
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php \yii\widgets\ActiveForm::end(); ?>
</div>
