<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Awp */
/* @var $form yii\widgets\ActiveForm */
$js = "";
$js = "$(document).ready(function(){
if(parseInt($(\".no_of_loanss\").val()) == 0) {
   // $(\".no_of_loanss\").attr('readonly','readonly');
}
$('.avg_loan_sizeee').blur(function(e) {
    
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
        //alert('#awpprojectmapping-'+row+'-'+column+'-'+'no_of_loans');
        $('#awpprojectmapping-'+row+'-'+column+'-'+'no_of_loans').attr('readonly','readonly');
    }
    
    $('.avg_loan_sizeee').each(function () {
        var a=this.id;
        var b=a.substring(18, 20);
        
        if(selfidno == b){
       idArray.push(this.id);}
    });
   
    var total=0;
    jQuery.each(idArray, function(index,item) {
      var it=item;
       var be=$('#'+it).val();
        if( $.isNumeric(be))
       total=total+parseInt(be);
});
//$('#awp-'+selfidno+'-avg_loan_size').val(total);
var size=$('#awp-'+selfidno+'-avg_loan_size').val();
var loans=$('#awp-'+selfidno+'-no_of_loans').val();
var amount=(parseInt(size))*(parseInt(loans));


var awploans=$('#awp-'+selfidnocomp+'-no_of_loans').val()
var awpavg_lon=$('#awp-'+selfidnocomp+'-avg_loan_size').val();
var amount=awploans*awpavg_lon;

$('#awp-'+selfidnocomp+'-disbursement_amount').val(amount);
 var idArray1 = [];
            var total1=0;

    $('.amount_disbursedd').each(function () {
        var a=this.id;
        var b=a.substring(18, 20);
        
        if(selfidno == b){
       idArray1.push(this.id);}
    });
    jQuery.each(idArray1, function(index,item) {
      var it=item;
       var be=$('#'+it).val();
    
        if( $.isNumeric(be))
       total1=total1+parseInt(be); 
         //alert(total1);
    });
    
$('#awp-'+selfidno+'-amount_disbursed').val(total1);

///
$('.amount_disbursed').each(function () {
        var a=this.id;
        var b=a.substring(4, 6);
        if(selfidnonew == b){
          idArray1.push(this.id);}
    });
    jQuery.each(idArray1, function(index,item) {
      var it=item;
      var be=$('#'+it).val();
      if( $.isNumeric(be))
       total1=total1+parseInt(be); 
         //alert(total1);
    });
    $('#disbursement-amount-total-'+selfidnonew).text('');
    $('#disbursement-amount-total-'+selfidnonew).append(total1);
///





});
$('.avg_recovery').blur(function(e) {
    var idArray1 = [];
    var seldid1=this.id;
    var selfidno1=seldid1.substring(18, 20);
    var selfidnocomp=seldid1.substring(18, 22);
    
    var active_loans=$('#awpprojectmapping-'+selfidnocomp+'-active_loans').val();
    var avg_recovery=$(this).val();
    /*alert(active_loans);
    alert(avg_recovery);*/
    $('#awpprojectmapping-'+selfidnocomp+'-monthly_recovery').val((parseInt(active_loans))*(parseInt(avg_recovery)));
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
    var idArray1 = [];
    var seldid1=this.id;
    var selfidno1=seldid1.substring(4, 6);
    var selfidnocomp=seldid1.substring(4, 8);
    var selfidnonew=seldid1.substring(4, 6);
   
    $('.no_of_loanss').each(function () {
        var a1=this.id;
        var b1=a1.substring(18, 20);
                //alert(a1)

        if(selfidno1 == b1){
        //alert(selfidno1+' '+b1)
       idArray1.push(this.id);}
    });
  // alert(idArray1);
    var total=0;
    jQuery.each(idArray1, function(index,item) {
      var it1=item;
       var be1=$('#'+it1).val();
        if( $.isNumeric(be1))
       total=total+parseInt(be1); 
    });
$('#awp-'+selfidno1+'-no_of_loans').val(total);
var size=$('#awp-'+selfidno1+'-avg_loan_size').val();
var loans=$('#awp-'+selfidno1+'-no_of_loans').val();
var amount=(parseInt(size))*(parseInt(loans));
//$('#awp-'+selfidno1+'-amount_disbursed').val(amount);

var awploans=$('#awp-'+selfidnocomp+'-no_of_loans').val()
var awpavg_lon=$('#awp-'+selfidnocomp+'-avg_loan_size').val();
var amount=awploans*awpavg_lon;

$('#awp-'+selfidnocomp+'-disbursement_amount').val(amount);

 var idArray1 = [];
            var total1=0;

    $('.amount_disbursedd').each(function () {
        var a=this.id;
        var b=a.substring(18, 20);
        //alert(this.id);
        if(selfidno1 == b){
       idArray1.push(this.id);}
    });

    jQuery.each(idArray1, function(index,item) {
      var it=item;
       var be=$('#'+it).val();
    
        if( $.isNumeric(be))
       total1=total1+parseInt(be); 
         //alert(total1);
    });
    $('#awp-'+selfidno1+'-amount_disbursed').val(total1);
    
///
$('.amount_disbursed').each(function () {
        var a=this.id;
        var b=a.substring(4, 6);
        if(selfidnonew == b){
          idArray1.push(this.id);}
    });
    jQuery.each(idArray1, function(index,item) {
      var it=item;
      var be=$('#'+it).val();
      if( $.isNumeric(be))
       total1=total1+parseInt(be); 
         //alert(total1);
    });
    $('#disbursement-amount-total-'+selfidnonew).text('');
    $('#disbursement-amount-total-'+selfidnonew).append(total1);
///
    
    
});




$('.monthly_recovery').blur(function(e) {

    var idArray3 = [];
    var seldid3=this.id;
    var selfidno3=seldid3.substring(18, 20);
    var selfidnocomp3=seldid3.substring(18, 22);
    //alert(selfidno3);

   
    $('.monthly_recovery').each(function () {
        var a3=this.id;
        var b3=a3.substring(18, 20);
                //alert(a1)

        if(selfidno3 == b3){
        //alert(selfidno3+' '+b3)
       idArray3.push(this.id);}
    });
  // alert(idArray3);
    var total=0;
    jQuery.each(idArray3, function(index,item) {
      var it3=item;
       var be3=$('#'+it3).val();
        if( $.isNumeric(be3))
       total=total+parseInt(be3); 
    });
    //alert(total);
   // alert(selfidno3);
    $('#awp-'+selfidno3+'-monthly_recovery').val(total);

});

$('.monthly_closed_loansss').blur(function(e) {

    var idArray3 = [];
    var seldid3=this.id;
    var selfidno3=seldid3.substring(18, 20);
    var selfidnocomp3=seldid3.substring(18, 22);
   
    $('.monthly_closed_loansss').each(function () {
        var a3=this.id;
        var b3=a3.substring(18, 20);
                //alert(a1)

        if(selfidno3 == b3){
        //alert(selfidno3+' '+b3)
       idArray3.push(this.id);}
    });
  
    var total=0;
    $.each(idArray3, function(index,item) {
      var it3=item;
       var be3=$('#'+it3).val();
        if( $.isNumeric(be3))
       total=total+parseInt(be3); 
    });
    //alert('monthly_closed_loans['+selfidno3+']');
    //alert(total);
    //alert(selfidno3);
   // alert($('#awp-'+selfidno3+'-monthly_closed_loans').val());
        ($('.monthly_closed_loans'+selfidno3).val(total));


});


});";
$js .= "$(document).ready(function(){
$('.avg_loan_size').blur(function(e) {
var a=this.value;
 var b=$('#no_of_loans').val();
var c=a*b;
 $('#disbursement_amount').html(c);
});

});
";
$js .= '$(document).ready(function(){
              $("#form-lock").submit( function(e){
                        //alert(\'a\');
                            var awp = $(this);
                           // alert(awp);
                            //alert(TicketChat);
                            $.ajax({
                            url    :\'/awp/update-islock\',
                            type   : \'POST\',
                            data   : awp.serialize(),
                            success: function (response)
                            {
                              //alert(response);
                              location.reload(true);

                            },
                            error  : function ()
                            {
                               alert("not saved")
                            }
                            });
                            return false;
                        });
});';
$js .= '$(".isagri").change(function() {
//alert(this.id);
    if($(this).not(\':checked\')){
     $(\'#awpprojectmapping-\'+this.id+\'-monthly_closed_loans\').prop( "readonly", true );
        $(\'#awpprojectmapping-\'+this.id+\'-monthly_recovery\').prop( "readonly", true );
       
    }
    if($(this).is(\':checked\')){
       
 $(\'#awpprojectmapping-\'+this.id+\'-monthly_closed_loans\').prop( "readonly", false );
     $(\'#awpprojectmapping-\'+this.id+\'-monthly_recovery\').prop( "readonly", false );
 
 }
});';

$this->registerJs($js);

/*echo'<pre>';
print_r($model) ;
die();*/
?>
<style>
    .project-total {
        margin-top: 30px;
        width: 130px;
    }

    .project-total-not-editable {
        margin-top: 20px;
        width: 130px;
    }

    .project-single {
        margin-top: 20px;
        width: 280px;
        /*margin-top: 18px;*/
        /*width: 130px;*/
    }
</style>

<style>
    .small-input {
        height: 22px;
        width: 90px;
        margin-top: -3px;
    }

    .input-fields {
        background-color: #0f6742;
        color: black;
    }

    .monthly_closed_loansss, .avg_recovery, .no_of_loanss, .no_of_loansss, .active_loans, .closed_loans {
        width: 60px;
    }

    .project {
        margin-top: -30px;
    }
</style>

<?php
$branch_ids = array(8,21,23,32,33,34,35,36,37,39,41,42,43,152,266,267,348,358,360,361,406,412,413,422,539,607,608,609,689,690,691,692,693);
?>

<div class="awp-form">
    <?= Html::beginForm([''], 'post', ['enctype' => 'multipart/form-data', 'id' => 'form']); ?>
    <div class="row">
        <div class="col-sm-3">
            <label>Branches</label>
            <?= Html::dropDownList('branch_id', $branch_id, $branches, ['id' => 'branch', 'class' => 'form-control', 'prompt' => 'Select Branch']) ?>
            <div class="help-block"></div>
        </div>
        <div class="col-sm-3" style="margin-top:20px">
            <?= Html::submitButton('Search Branch', ['class' => 'btn btn-primary']) ?>
        </div>
        <?= Html::endForm(); ?>
    </div>

    <?php if ($branch_id != 0) { ?>
        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Month</th>
                    <th>Projects</th>
                    <!--<th>Opening OLP<?php /*echo $model[0]->month*/ ?></th>-->
                    <!--<th>Opening Active Loans<?php /*echo $model[0]->month*/ ?></th>-->
                    <!--<th>Exp. Closed Loans</th>-->
                    <!--<th>Average Recovery</th>-->
                    <th>Expected Recovery</th>
                    <!--style="background-color: #46c35f"-->
                    <!--style="background-color: #00a8ff" -->
                    <th style="background-color: #00a8ff;color:white" class="input-fields">Avg Loan Size</th>
                    <th style="background-color: #00a8ff;color:white" class="input-fields">No of loans</th>
                    <th>Disb. Amount</th>
                    <th>Funds Required</th>
                </tr>
                </thead>
                <tbody>
                <?= Html::beginForm([''], 'post', ['enctype' => 'multipart/form-data', 'id' => 'form']); ?>
                <?php
                $inc = 1;
                $inc = sprintf("%02d", $inc);
                foreach ($model as $_model) {
                    if ($_model->is_lock == 0) {
                        $awps = \common\models\Awp::find()->where(['month' => $_model->month, 'branch_id' => $_model->branch_id])->all(); ?>
                        <tr>
                            <td><strong><?php echo date('F-Y', strtotime($_model->month)) ?></strong></td>
                            <?php $i = 1;
                            foreach ($awps as $awp) { ?>
                                <?= $form->field($awp, "[{$inc}][{$i}]id")->hiddenInput(['class' => "form-control small-input ", 'readonly' => 'readonly'])->label(false) ?>
                            <?php $i++; } ?>
                            <td>
                                <?php $i = 1 ?>
                                <?php
                                foreach ($awps as $awp) { ?>
                                    <div class="project-single"><?php echo \common\components\Helpers\AwpHelper::getProject($awp->project_id)['name'] ?></div>
                                <?php $i++; } ?>
                            </td>
                            <td>
                                <?php  $i =1;?>
                                <?php foreach ($awps as $awp) { ?>

                                    <br>

                                   <?php if (\common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'Kissan' || (in_array($branch_id, $branch_ids) && \common\components\Helpers\AwpHelper::getProjectcode($awp->project_id)['code'] == 'PSIC')) {
                                   // echo $form->field($awp, "[{$inc}][$i]monthly_recovery")->textInput(['class' => "form-control small-input monthly_recovery"])->label(false);
                                    echo $form->field($awp, "[{$inc}][{$i}]monthly_recovery")->textInput(['class' => "form-control small-input "])->label(false) ;

                                    } else {
                                    //echo $form->field($awp, "[{$inc}][$i]monthly_recovery")->textInput(['class' => "form-control small-input monthly_recovery", 'readonly' => 'readonly'])->label(false);
                                    echo $form->field($awp, "[{$inc}][{$i}]monthly_recovery")->textInput(['class' => "form-control small-input ", 'readonly' => 'readonly'])->label(false);

                                    }
                                    ?>

                                <?php $i++; } ?>
                            </td>
                            <td>
                                <?php  $i =1; ?>
                                <?php foreach ($awps as $awp) { ?>
                                    <br>

                                    <?= $form->field($awp, "[{$inc}][{$i}]avg_loan_size")->textInput(['class' => "form-control small-input avg_loan_sizeee"])->label(/*\common\components\AwpHelper::getProject($project->project_id)['name']*/
                                        false); ?>
                                    <?php $i++;
                                } ?>

                            </td>
                            <td>
                                <?php  $i =1;?>
                                <?php foreach ($awps as $awp) { ?>

                                    <br>
                                    <?= $form->field($awp, "[{$inc}][{$i}]no_of_loans")->textInput(['class' => "form-control small-input no_of_loanss"])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);?>

                                <?php $i++; } ?>

                            </td>
                            <td>
                                <?php  $i=1;?>
                                <br>
                                <br>
                                <?php $total=0;?>
                                <?php foreach ($awps as $awp) { ?>

                                    <br>
                                    <?= $form->field($awp, "[{$inc}][{$i}]disbursement_amount")->textInput(['class' => 'form-control small-input amount_disbursed', 'readonly' => 'readonly'])->label(false) ?>
                                <?php $total=$total+$awp->disbursement_amount;?>
                                    <?php $i++; } ?>
                                <br>
                                <div id="disbursement-amount-total-<?php echo $inc?>" class="label label-primary"><?php echo $total?></div>

                            </td>
                            <td>
                                <?php $i =1;?>
                                <?php foreach ($awps as $awp) { ?>

                                    <br>
                                    <?= $form->field($awp, "[{$inc}][{$i}]funds_required")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false) ?>


                                <?php $i++; } ?>
                            </td>
                        </tr>
                        <?php

                    }
                    $inc++;
                    $inc = sprintf("%02d", $inc);} ?>

                </tbody>
            </table>
        </div>
            <br>
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary pull-right']) ?>
            </div>
            <?php ActiveForm::end(); ?>
    <?php } ?>
    <?php
    if ($branch_id != 0) {
        $flag = false;
        foreach ($model as $m) {
            if ($m->is_lock == 0) {
                $flag = true;
                break;
            }
        }
    }
    if ($branch_id != 0 && $flag==false) { ?>
        <div>No Result Found!</div> <?php } ?>
    <br>
    <br>
</div>








