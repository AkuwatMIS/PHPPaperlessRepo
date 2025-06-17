<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Awp */
/* @var $form yii\widgets\ActiveForm */
$js = "";
$js = "$(document).ready(function(){

if(parseInt($(\".no_of_loanss\").val()) == 0) {
    $(\".no_of_loanss\").attr('readonly','readonly');
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
    var selfidno=seldid.substring(18, 20);
    var selfidnocomp=seldid.substring(18, 22);
    
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
   
$('#awp-'+selfidno+'-avg_loan_size').val(total);
var size=$('#awp-'+selfidno+'-avg_loan_size').val();
var loans=$('#awp-'+selfidno+'-no_of_loans').val();
var amount=(parseInt(size))*(parseInt(loans));


var awploans=$('#awpprojectmapping-'+selfidnocomp+'-no_of_loans').val()
var awpavg_lon=$('#awpprojectmapping-'+selfidnocomp+'-avg_loan_size').val();
var amount=awploans*awpavg_lon;
$('#awpprojectmapping-'+selfidnocomp+'-disbursement_amount').val(amount);
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
    var selfidno1=seldid1.substring(18, 20);
    var selfidnocomp=seldid1.substring(18, 22);

   
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

var awploans=$('#awpprojectmapping-'+selfidnocomp+'-no_of_loans').val()
var awpavg_lon=$('#awpprojectmapping-'+selfidnocomp+'-avg_loan_size').val();
var amount=awploans*awpavg_lon;
$('#awpprojectmapping-'+selfidnocomp+'-disbursement_amount').val(amount);

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

    .project-single {
        margin-top: 10px;
        width: 130px;
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
        color: white;
    }
    .monthly_closed_loansss,.avg_recovery,.no_of_loanss,.no_of_loansss,.active_loans,.closed_loans{
        width: 60px;
    }
    .project {
        margin-top: -30px;
    }
</style>

<?php
$branch_ids = array(45, 46, 48, 50, 392, 393, 26, 51, 52, 284, 44, 745, 32, 742, 743, 744, 41, 42, 43, 12, 741, 660, 659, 661, 455, 439, 445, 446, 580, 390, 285, 161, 377, 270);
?>
<?php $model1 = $model; ?>


<div class="awp-form">
    <?= Html::beginForm([''], 'post', ['enctype' => 'multipart/form-data', 'id' => 'form']); ?>
    <div class="row">
        <div class="col-sm-3">
            <label>Branches</label>
            <?= Html::dropDownList('branch_id', $branch_id, $branches, ['id' => 'branch', 'class' => 'form-control', 'prompt' => 'Select Branch']) ?>
            <div class="help-block"></div>
        </div>
        <div class="col-sm-3" style="margin-top:23px">
            <?= Html::submitButton('Search Branch', ['class' => 'btn btn-primary']) ?>
        </div>
        <?= Html::endForm(); ?>
        <div class="col-sm-3 pull-right" style="margin-top:23px">
            <?php if ($branch_id != 0) {
                if ($model[0]->is_lock == 0) { ?>
                    <form id="form-lock" method="post">
                        <input type="hidden" name="Awp[branch_id]" value="<?php echo $branch_id ?>">
                        <button name="submit" class="btn btn-danger pull-right">Lock AWP</button>
                    </form>
                <?php }
            } ?>
        </div>

    </div>


    <?php if ($branch_id != 0) { ?>
        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
        <?php if ($model[0]->no_of_loans != 0) { ?>
            <div style="border:1px solid #d6e9c6;padding:10px;">
                <h2>Branch Summary</h2>

                <div class="row">
                    <div class="col-sm-3">
                        <table class="table table-bordered">
                            <tbody>
                            <?php if (isset($model[0])) { ?>
                                <tr>
                                    <th>Active Loans as
                                        on <?php echo date('t-F-Y-', strtotime($model[0]->month . ' -1 month')); ?></th>
                                    <td><?php echo number_format($model[0]->active_loans) ?></td>
                                </tr>
                                <tr>
                                    <th>OLP as
                                        on <?php echo date('t-F-Y-', strtotime($model[0]->month . ' -1 month')); ?></th>
                                    <td><?php echo number_format($model[0]->monthly_olp) ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-bordered">
                            <tbody>
                            <?php if (isset($model[0])) { ?>
                                <tr>
                                <tr>
                                    <th>Total Closed Loans</th>
                                    <?php
                                    $total_loans = 0;
                                    $disbursement_amount = 0;
                                    $closed_loans = 0;
                                    $total_recovery = 0;
                                    $total_funds = 0;


                                    foreach ($model as $m) {
                                        $total_loans += $m->no_of_loans;
                                        $disbursement_amount += $m->amount_disbursed;
                                        $closed_loans += $m->monthly_closed_loans;
                                        $total_recovery += $m->monthly_recovery;
                                        $total_funds = $disbursement_amount - $total_recovery;

                                    }
                                    ?>
                                    <td><?php echo number_format($closed_loans) ?></td>
                                </tr>
                                <th>Total Loans Disbursement</th>

                                <td><?php echo number_format($total_loans) ?></td>
                                </tr>
                                <tr>
                                    <th>Total Disbursement Amount</th>
                                    <td><?php echo number_format($disbursement_amount) ?></td>
                                </tr>

                                <tr>
                                    <th>Total Recovery</th>
                                    <td><?php echo number_format($total_recovery) ?></td>
                                </tr>
                                <tr>
                                    <th>Total Funds Required</th>
                                    <td><?php echo number_format($total_funds) ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-3">
                        <table class="table table-bordered">
                            <tbody>
                            <?php if (isset($model[0])) { ?>
                                <tr>
                                    <th>Active Loans as
                                        on <?php echo date('t-F-Y', strtotime($model[11]->month)); ?></th>
                                    <td><?php echo number_format(($model[11]->active_loans + $model[11]->no_of_loans) - $model[11]->monthly_closed_loans) ?></td>

                                </tr>
                                <tr>
                                    <th>OLP as
                                        on <?php echo date('t-F-Y', strtotime($model[11]->month )); ?></th>
                                    <td><?php echo number_format(($model[11]->monthly_olp + $model[11]->amount_disbursed) - $model[11]->monthly_recovery) ?></td>

                                </tr>

                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>


        <div class="table-responsive">
            <table class="table table-bordered">
                <?php if ($model[0]->is_lock == 0) { ?>
                    <thead>
                    <tr>
                        <th>Month</th>
                        <th>Projects</th>
                        <th>Opening OLP<?php /*echo $model[0]->month*/ ?></th>
                        <th>Opening Active Loans<?php /*echo $model[0]->month*/ ?></th>
                        <th>Exp. Closed Loans</th>
                        <th>Average Recovery</th>
                        <th>Expected Recovery</th>
                        <th class="input-fields">Avg Loan Size</th>
                        <th class="input-fields">No of loans</th>
                        <th>Disb. Amount</th>
                        <th>Funds Required</th>
                    </tr>
                    </thead>
                <?php } ?>
                <tbody>
                <?php
                /*                echo'<pre>';
                                print_r($model);
                                die();
                                */ ?>
                <?= Html::beginForm([''], 'post', ['enctype' => 'multipart/form-data', 'id' => 'form']); ?>
                <?php /*if (isset($model[0])) { */
                if ($model1[0]->status != 0 && $model[0]->is_lock == 0) {
                    $inc = 1;
                    $inc = sprintf("%02d", $inc);

                    foreach ($model as $model) {
                        ?>
                        <tr>

                            <?= $form->field($model, "[{$inc}]id")->hiddenInput()->label(false); ?>
                            <td><strong><?php echo date('F-Y', strtotime($model->month)) ?></strong></td>
                            <td>

                                <?php
                                $awp_projects = \common\models\AwpProjectMapping::find()->where(['awp_id' => $model->id])->all();
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                foreach ($awp_projects as $project) { ?>
                                    <?php if (\common\components\AwpHelper::getProject($project->project_id)['name'] == '123123') { ?>
                                        <div class="project-single"><?php echo \common\components\AwpHelper::getProject($project->project_id)['name'] ?>
                                            &nbsp;<input type="checkbox" class="isagri"
                                                         id=<?php echo $inc . '-' . $i ?>></div>
                                    <?php } else { ?>
                                        <div class="project-single"><?php echo \common\components\AwpHelper::getProject($project->project_id)['name'] ?></div>
                                    <?php }
                                    $i++;

                                    ?>

                                <?php } ?>
                                <div class="project-total"><b>Total</b></div>
                            </td>
                            <!--<td>
                        <?php
                            /*                        $awp_projects = \common\models\AwpProjectMapping::find()->where(['awp_id' => $model->id])->all();

                                                    foreach ($awp_projects as $project) { */
                            ?>
                            <div class="project-single"></div>
                           <?php /*if(\common\components\AwpHelper::getProject($project->project_id)['name']=='PM-IFL'){*/
                            ?>
                            <div class="project-single"><input type="checkbox" id="takaf" checked="checked"></div>
                                <?php /*}*/
                            ?>

                        <?php /*} */
                            ?>
                    </td>-->
                            <?= $form->field($model, "[{$inc}]month")->hiddenInput(['readonly' => 'readonly'])->label(false) ?>
                            <?= $form->field($model, "[{$inc}]monthly_closed_loans")->hiddenInput(['readonly' => 'readonly'])->label(false) ?>
                            <td>
                                <?php
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                foreach ($awp_projects as $project) {
                                    echo $form->field($project, "[{$inc}][$i]id")->hiddenInput(['class' => "form-control small-input"])->label(false);

                                    echo $form->field($project, "[{$inc}][$i]monthly_olp")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(/*\common\components\AwpHelper::getProject($project->project_id)['name']*/
                                        false);
                                    $i++;
                                } ?>
                                <br>
                                <?= $form->field($model, "[{$inc}]monthly_olp")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false) ?>
                            </td>
                            <!--<td><? /*= $form->field($model, "[{$inc}]monthly_olp")->textInput(['readonly' => 'readonly'])->label(false) */
                            ?></td>-->
                            <td>
                                <?php
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                foreach ($awp_projects as $project) {
                                    echo $form->field($project, "[{$inc}][$i]id")->hiddenInput(['class' => "form-control small-input"])->label(false);

                                    echo $form->field($project, "[{$inc}][$i]active_loans")->textInput(['class' => "form-control small-input active_loans", 'readonly' => 'readonly'])->label(/*\common\components\AwpHelper::getProject($project->project_id)['name']*/
                                        false);
                                    $i++;
                                } ?>
                                <br>
                                <?= $form->field($model, "[{$inc}]active_loans")->textInput(['class' => "form-control small-input active_loans", 'readonly' => 'readonly'])->label(false) ?>
                            </td>
                            <!--<td><? /*= $form->field($model, "[{$inc}]active_loans")->textInput(['readonly' => 'readonly'])->label(false) */
                            ?></td>-->
                            <td>
                                <?php
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                foreach ($awp_projects as $project) {

                                    echo $form->field($project, "[{$inc}][$i]id")->hiddenInput(['class' => "form-control small-input"])->label(false);
                                    if (\common\components\AwpHelper::getProjectcode($project->project_id)['code'] == 'Kissan' || (in_array($branch_id, $branch_ids) && \common\components\AwpHelper::getProjectcode($project->project_id)['code'] == 'PSIC')) {
                                        echo $form->field($project, "[{$inc}][$i]monthly_closed_loans")->textInput(['class' => "form-control small-input monthly_closed_loansss"])->label(false);

                                    } else {
                                        echo $form->field($project, "[{$inc}][$i]monthly_closed_loans")->textInput(['class' => "form-control small-input  monthly_closed_loansss", 'readonly' => 'readonly'])->label(false);

                                    }

                                    $i++;
                                } ?>
                                <br>
                                <?= $form->field($model, "[{$inc}]monthly_closed_loans")->textInput(['class' => "form-control small-input closed_loans monthly_closed_loans{$inc}", 'readonly' => 'readonly'])->label(false) ?>
                            </td>
                            <td>
                                <?php
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                foreach ($awp_projects as $project) {
                                    echo $form->field($project, "[{$inc}][$i]id")->hiddenInput(['class' => "form-control small-input"])->label(false);

                                    echo $form->field($project, "[{$inc}][$i]avg_recovery")->textInput(['class' => "form-control small-input avg_recovery"])->label(/*\common\components\AwpHelper::getProject($project->project_id)['name']*/
                                        false);
                                    $i++;
                                } ?>
                                <br>
                            </td>
                            <!--<td><? /*= $form->field($model, "[{$inc}]monthly_closed_loans")->textInput(['readonly' => 'readonly'])->label(false) */
                            ?></td>-->
                            <td>
                                <?php
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                foreach ($awp_projects as $project) {
                                    echo $form->field($project, "[{$inc}][$i]id")->hiddenInput(['class' => "form-control small-input"])->label(false);
                                    if (\common\components\AwpHelper::getProjectcode($project->project_id)['code'] == 'Kissan' || (in_array($branch_id, $branch_ids) && \common\components\AwpHelper::getProjectcode($project->project_id)['code'] == 'PSIC')) {
                                        echo $form->field($project, "[{$inc}][$i]monthly_recovery")->textInput(['class' => "form-control small-input monthly_recovery"])->label(false);

                                    } else {
                                        echo $form->field($project, "[{$inc}][$i]monthly_recovery")->textInput(['class' => "form-control small-input monthly_recovery", 'readonly' => 'readonly'])->label(false);

                                    }
                                    $i++;
                                } ?>
                                <br>
                                <?= $form->field($model, "[{$inc}]monthly_recovery")->textInput(['class' => "form-control small-input ", 'readonly' => 'readonly'])->label(false) ?>
                            </td>
                            <!--<td><? /*= $form->field($model, "[{$inc}]monthly_recovery")->textInput(['readonly' => 'readonly'])->label(false) */
                            ?></td>-->
                            <?= $form->field($model, "[{$inc}]branch_id")->hiddenInput(['readonly' => 'readonly'])->label(false) ?>

                            <td>
                                <?php
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                foreach ($awp_projects as $project) {
                                    echo $form->field($project, "[{$inc}][$i]id")->hiddenInput(['class' => "form-control small-input avg_loan_sizeeesss"])->label(false);

                                    echo $form->field($project, "[{$inc}][$i]avg_loan_size")->textInput(['class' => "form-control small-input avg_loan_sizeee"])->label(/*\common\components\AwpHelper::getProject($project->project_id)['name']*/
                                        false);
                                    $i++;
                                } ?>
                                <?= $form->field($model, "[{$inc}]avg_loan_size")->hiddenInput(['class' => 'form-control small-input', 'readonly' => 'readonly'])->label(false) ?>
                            </td>
                            <td>
                                <?php
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                $awp_projects = \common\models\AwpProjectMapping::find()->where(['awp_id' => $model->id])->all();


                                foreach ($awp_projects as $project) { ?>

                                    <?php echo $form->field($project, "[{$inc}][$i]id")->hiddenInput(['class' => "form-control small-input no_of_loansssss"])->label(false);

                                    echo $form->field($project, "[{$inc}][{$i}]no_of_loans")->textInput(['class' => "form-control small-input no_of_loanss"])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                                    $i++;
                                } ?>
                                <br>
                                <?= $form->field($model, "[{$inc}]no_of_loans")->textInput(['class' => 'form-control small-input no_of_loansss', 'readonly' => 'readonly'])->label(false) ?>
                            </td>
                            <td>
                                <?php
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                $awp_projects = \common\models\AwpProjectMapping::find()->where(['awp_id' => $model->id])->all();


                                foreach ($awp_projects as $project) { ?>

                                    <?php echo $form->field($project, "[{$inc}][$i]id")->hiddenInput(['class' => "form-control small-input"])->label(false);

                                    echo $form->field($project, "[{$inc}][{$i}]disbursement_amount")->textInput(['class' => "form-control small-input amount_disbursedd", 'readonly' => 'readonly'])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                                    $i++;
                                } ?>
                                <br>
                                <?= $form->field($model, "[{$inc}]amount_disbursed")->textInput(['class' => 'form-control small-input amount_disbursed', 'readonly' => 'readonly'])->label(false) ?>
                            </td>


                            <!--<td><? /*= $form->field($model, "[{$inc}]avg_loan_size")->label(false) */
                            ?></td>-->
                            <!--<td><? /*= $form->field($model, "[{$inc}]amount_disbursed")->textInput(['readonly' => 'readonly'])->label(false) */
                            ?></td>-->
                            <td>
                                <?php
                                $i = 1;
                                $inc = sprintf("%02d", $inc);
                                foreach ($awp_projects as $project) {
                                    echo $form->field($project, "[{$inc}][$i]id")->hiddenInput(['class' => "form-control small-input"])->label(false);

                                    echo $form->field($project, "[{$inc}][$i]funds_required")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(/*\common\components\AwpHelper::getProject($project->project_id)['name']*/
                                        false);
                                    $i++;
                                } ?>
                                <br>
                                <?= $form->field($model, "[{$inc}]funds_required")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false) ?>
                            </td>
                            <!--<td><? /*= $form->field($model, "[{$inc}]funds_required")->textInput(['readonly' => 'readonly'])->label(false) */
                            ?></td>-->
                        </tr>

                        <?php $inc++;
                        $inc = sprintf("%02d", $inc);

                    }
                } ?>

                <?php

                if ($model1[0]->status == 0 && $model[0]['is_lock'] == 0) {
                    $i = 1;
                    $i = sprintf("%02d", $i);
                    //foreach ($model1[0] as $m) { ?>
                    <tr>
                    <?= $form->field($model[0], "[{$i}]id")->hiddenInput()->label(false); ?>
                    <?= $form->field($model1[0], "[{$i}]month")->hiddenInput(['readonly' => 'readonly'])->label(false) ?>
                    <?= $form->field($model[0], "[{$i}]monthly_closed_loans")->hiddenInput(['readonly' => 'readonly'])->label(false) ?>

                    <td><?php echo date('M-Y', strtotime($model[0]->month)) ?></td>
                    <td>

                        <?php
                        $awp_projects = \common\models\AwpProjectMapping::find()->where(['awp_id' => $model[0]->id])->all();

                        $inc = 1;
                        $i = sprintf("%02d", $i);
                        foreach ($awp_projects as $project) { ?>
                            <?php if (\common\components\AwpHelper::getProject($project->project_id)['name'] == '123123') { ?>
                                <div class="project-single"><?php echo \common\components\AwpHelper::getProject($project->project_id)['name'] ?>
                                    &nbsp;<input type="checkbox" class="isagri" id=<?php echo $i . '-' . $inc ?>></div>
                            <?php } else { ?>
                                <div class="project-single"><?php echo \common\components\AwpHelper::getProject($project->project_id)['name'] ?></div>
                            <?php }
                            $inc++;

                            ?>

                        <?php } ?>

                        <div class="project-total"><b>Total</b></div>

                    </td>

                    <td>
                        <?php
                        $inc = 1;
                        foreach ($awp_projects as $project) {
                            echo $form->field($project, "[{$i}][{$inc}]id")->hiddenInput(['class' => "form-control small-input"])->label(false);

                            echo $form->field($project, "[$i][{$inc}]monthly_olp")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                            $inc++;

                        } ?>
                        <br>
                        <?= $form->field($model1[0], "[{$i}]monthly_olp")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false) ?>

                    </td>
                    <td>
                        <?php
                        $inc = 1;
                        foreach ($awp_projects as $project) {
                            echo $form->field($project, "[{$i}][{$inc}]id")->hiddenInput(['class' => "form-control small-input active_loans"])->label(false);

                            echo $form->field($project, "[$i][{$inc}]active_loans")->textInput(['class' => "form-control small-input active_loans", 'readonly' => 'readonly'])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                            $inc++;

                        } ?>
                        <br>
                        <?= $form->field($model1[0], "[{$i}]active_loans")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false) ?>

                    </td>
                    <!--<td><?/*= $form->field($model1[0], "[{$i}]active_loans")->textInput(['readonly' => 'readonly'])->label(false) */ ?></td>-->
                    <td>
                        <?php
                        $inc = 1;
                        foreach ($awp_projects as $project) {
                            echo $form->field($project, "[{$i}][{$inc}]id")->hiddenInput(['class' => "form-control small-input"])->label(false);
                            if (\common\components\AwpHelper::getProjectcode($project->project_id)['code'] == 'Kissan' || (in_array($branch_id, $branch_ids) && \common\components\AwpHelper::getProjectcode($project->project_id)['code'] == 'PSIC')) {

                                echo $form->field($project, "[$i][{$inc}]monthly_closed_loans")->textInput(['class' => "form-control small-input monthly_closed_loansss"])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                            } else {
                                echo $form->field($project, "[$i][{$inc}]monthly_closed_loans")->textInput(['class' => "form-control small-input monthly_closed_loansss", 'readonly' => 'readonly'])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);

                            }
                            $inc++;

                        } ?>
                        <br>
                        <?= $form->field($model1[0], "[{$i}]monthly_closed_loans")->textInput(['class' => "form-control small-input closed_loans monthly_closed_loans{$i}", 'readonly' => 'readonly'])->label(false) ?>

                    </td>
                    <td>
                        <?php
                        $inc = 1;
                        foreach ($awp_projects as $project) {
                            echo $form->field($project, "[{$i}][{$inc}]id")->hiddenInput(['class' => "form-control small-input"])->label(false);

                            echo $form->field($project, "[$i][{$inc}]avg_recovery")->textInput(['class' => "form-control small-input avg_recovery"])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                            $inc++;

                        } ?>
                        <br>
                    </td>
                    <!--<td><?/*= $form->field($model1[0], "[{$i}]monthly_closed_loans")->textInput(['readonly' => 'readonly'])->label(false) */ ?></td>-->
                    <td>
                        <?php
                        $inc = 1;
                        foreach ($awp_projects as $project) {
                            echo $form->field($project, "[{$i}][{$inc}]id")->hiddenInput(['class' => "form-control small-input"])->label(false);
                            if (\common\components\AwpHelper::getProjectcode($project->project_id)['code'] == 'Kissan' || (in_array($branch_id, $branch_ids) && \common\components\AwpHelper::getProjectcode($project->project_id)['code'] == 'PSIC')) {

                                echo $form->field($project, "[$i][{$inc}]monthly_recovery")->textInput(['class' => "form-control small-input monthly_recovery"])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                            } else {
                                echo $form->field($project, "[$i][{$inc}]monthly_recovery")->textInput(['class' => "form-control small-input monthly_recovery", 'readonly' => 'readonly'])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);

                            }
                            $inc++;

                        } ?>
                        <br>
                        <?= $form->field($model1[0], "[{$i}]monthly_recovery")->textInput(['class' => "form-control small-input monthly_recovery", 'readonly' => 'readonly'])->label(false) ?>

                    </td>
                    <!--<td><?/*= $form->field($model1[0], "[{$i}]monthly_recovery")->textInput(['readonly' => 'readonly'])->label(false) */ ?></td>-->
                    <?= $form->field($model1[0], "[{$i}]branch_id")->hiddenInput(['readonly' => 'readonly'])->label(false) ?>


                    <td>
                        <?php
                        $inc = 1;
                        foreach ($awp_projects as $project) {
                            echo $form->field($project, "[{$i}][{$inc}]id")->hiddenInput(['class' => "form-control small-input avg_loan_sizeeesss"])->label(false);

                            echo $form->field($project, "[$i][{$inc}]avg_loan_size")->textInput(['class' => "form-control small-input avg_loan_sizeee"])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                            $inc++;

                        } ?>
                        <br>
                        <?= $form->field($model[0], "[{$i}]avg_loan_size")->hiddenInput(['class' => 'form-control small-input', 'readonly' => 'readonly'])->label(false) ?>
                    </td>
                    <td>
                        <?php
                        $inc = 1;
                        $awp_projects = \common\models\AwpProjectMapping::find()->where(['awp_id' => $model[0]->id])->all();
                        foreach ($awp_projects as $project) {
                            echo $form->field($project, "[{$i}][{$inc}]id")->hiddenInput(['class' => "form-control small-input no_of_loansssss"])->label(false);
                            echo $form->field($project, "[{$i}][{$inc}]project_id")->hiddenInput()->label(false);

                            echo $form->field($project, "[{$i}][{$inc}]no_of_loans")->textInput(['class' => "form-control small-input no_of_loanss"])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                            $inc++;
                        } ?>
                        <br>
                        <?= $form->field($model1[0], "[{$i}]no_of_loans")->textInput(['class' => 'form-control small-input', 'readonly' => 'readonly'])->label(false) ?>
                    </td>


                    <td>
                        <?php
                        $inc = 1;

                        $awp_projects = \common\models\AwpProjectMapping::find()->where(['awp_id' => $model[0]->id])->all();


                        foreach ($awp_projects as $project) { ?>

                            <?php echo $form->field($project, "[$i][{$inc}]id")->hiddenInput(['class' => "form-control small-input"])->label(false);

                            echo $form->field($project, "[{$i}][{$inc}]disbursement_amount")->textInput(['class' => "form-control small-input amount_disbursedd", 'readonly' => 'readonly'])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                            $inc++;

                        } ?>
                        <br>
                        <?= $form->field($model[0], "[{$i}]amount_disbursed")->textInput(['class' => 'form-control small-input amount_disbursed', 'readonly' => 'readonly'])->label(false) ?>
                    </td>


                    <!--  <td><?/*= $form->field($model1[0], "[{$i}]avg_loan_size")->label(false) */ ?></td>-->
                    <td>
                        <?php
                        $inc = 1;
                        foreach ($awp_projects as $project) {
                            echo $form->field($project, "[{$i}][{$inc}]id")->hiddenInput(['class' => "form-control small-input"])->label(false);

                            echo $form->field($project, "[$i][{$inc}]funds_required")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false/*\common\components\AwpHelper::getProject($project->project_id)['name']*/);
                            $inc++;

                        } ?>
                        <br>
                        <?= $form->field($model1[0], "[{$i}]funds_required")->textInput(['class' => "form-control small-input", 'readonly' => 'readonly'])->label(false) ?>

                    </td>
                    <!--<td><?/*= $form->field($model[0], "[{$i}]funds_required")->textInput(['class' => 'form-control','readonly' => 'readonly'])->label(false) */ ?></td>-->

                    </tr><?php
                    $i++;
                    $i = sprintf("%02d", $i);
                    //}

                }
                ?>


                </tbody>
            </table>
        </div>
        <?php if ($model1[0]->is_lock == 0) { ?>
            <div class="form-group">
                <?= Html::submitButton($model1[0]->status == 0 ? 'Create Awp' : 'Save', $model1[0]->status == 0 ? ['class' => 'btn btn-success submit'] : ['class' => 'btn btn-primary pull-right']) ?>
            </div>
        <?php } ?>
        <?php ActiveForm::end(); ?>
    <?php } ?>
</div>







