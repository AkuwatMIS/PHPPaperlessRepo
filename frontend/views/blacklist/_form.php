<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Blacklist */
/* @var $form yii\widgets\ActiveForm */
$js="
$(document).ready(function(){
$('#blacklist-cnic').change(function(){
var cnic=$('#blacklist-cnic').val();
$.ajax({

        type: \"POST\",
        url: '/blacklist/cnic-check?cnic='+cnic,
        success: function(data){
            var obj = $.parseJSON(data);
            if(obj.status_type=='success'){
            
             $('.exist').text('Member Exists against this CNIC'); 
             $('#blacklist-member_id').val(obj.data.id);
             $('#blacklist-name').val(obj.data.name);
             $('.alert-success').show();
             $(\".alert-success\").delay(2500).slideUp(1000);
             
                       
            }else{
            $('.blacklist').text('Member Not Exists against this CNIC'); 
            $('#blacklist-member_id').val('');
             $('#blacklist-name').val('');
             $('.alert-danger').show();
             $(\".alert-danger\").delay(2500).slideUp(1000);
                  
                      
            }
          
        }
    });
    });
});
";
$this->registerJs($js);
?>

<div class="blacklist-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'member_id')->hiddenInput()->label(false) ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->textInput(['maxlength' => true, 'placeholder' => 'CNIC', 'class' => 'form-control form-control-sm']) ?>
            <div class="alert alert-success alert-dismissable" style="display:none">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                <h4><i class="icon fa fa-check exist"></i></h4>
            </div>
            <div class="alert alert-danger alert-dismissable " style="display:none">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                <h4><i class="icon fa fa-ban blacklist"></i></h4>
            </div>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'cnic_invalid')->textInput(['maxlength' => true, 'placeholder' => 'Invalid CNIC', 'class' => 'form-control form-control-sm'])->label('Invalid Cnic') ?>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder'=>'Enter Name','class' => 'form-control form-control-sm'])->label('Name') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'parentage')->textInput(['maxlength' => true,'placeholder'=>'Enter Name','class' => 'form-control form-control-sm'])->label('Parentage') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'type')->dropDownList(["soft"=>"Soft","hard"=>"Hard"],['prompt'=>'Select Type','class' => 'form-control form-control-sm'])->label('Type') ?>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'reason')->dropDownList(["NACTA Defaulter"=>"NACTA Defaulter","NAB Defaulter"=>"NAB Defaulter","UNSCR"=>"UNSCR"],['prompt'=>'Select Institute Name','class' => 'form-control form-control-sm'])->label('Institute Name') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'province')->dropDownList(["Punjab"=>"Punjab","Sindh"=>"Sindh","KP"=>"KP","Balochistan"=>"Balochistan","KP(Ex-FATA)"=>"KP (Ex-FATA)","Gilgit_Baltistan"=>"Gilgit Baltistan","ajk"=>"Azad Jammu & Kashmir","Islamabad"=>"Islamabad Capital Territory"])->label('Province') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'location')->textInput(['maxlength' => true,'placeholder'=>'Enter Location','class' => 'form-control form-control-sm']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 6,'placeholder'=>'Enter Description']) ?>
        </div>
    </div>











    <!--<?/*= $form->field($model, 'created_by')->textInput() */?>

    <?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>

    <?/*= $form->field($model, 'deleted')->textInput() */?>-->

    <br>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>
    <?php ActiveForm::end(); ?>
    
</div>
