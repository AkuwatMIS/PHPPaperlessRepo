<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\UsersCopy */
/* @var $form yii\widgets\ActiveForm */
$js='$(document).ready(function () {
         
   var a=$("#designation").val();
       if (a == "LO"){
           $("#extra_fields").show();
           $("#multi_branches").hide();
           $(\'#branch_id\').removeAttr(\'disabled\');
           $(\'#team_id\').removeAttr(\'disabled\');
           $(\'#field_id\').removeAttr(\'disabled\');
          
       }
       else{
                $(\'#branch_id\').attr("disabled","disabled");
                $(\'#team_id\').attr("disabled","disabled");
                $(\'#field_id\').attr("disabled","disabled");
       }
        $("#designation").change(function(){
       
            var a=$(\'#designation\').val();
            if(a==\'LO\'){
                $(\'#extra_fields\').show();
                $(\'#multi_branches\').hide();
                $(\'#field_id\').attr("disabled","disabled");
                $(\'#branch_id\').removeAttr(\'disabled\');
                $(\'#team_id\').removeAttr(\'disabled\');
                $(\'#field_id\').removeAttr(\'disabled\');
            }
            else {
            
                $(\'#extra_fields\').hide();
                $(\'#multi_branches\').show();
                $(\'#branch_id\').val("");
                $(\'#branch_id\').attr("disabled","disabled");
                $(\'#team_id\').attr("disabled","disabled");
                $(\'#field_id\').attr("disabled","disabled");
                $(\'#team_id\').val("");
                $(\'#field_id\').val("");

            }
        });
    });';
$this->registerJs($js);
?>
<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>
    <h4>Sructural Mapping</h4>
    <?php $role = !empty($model->role->item_name) ? $model->role->item_name : null; ?>
    <label>Designation</label>
    <?= Html::dropDownList('designation',$role, $array['designations'],array('class'=>'form-control designation','initialize'=>false, 'id'=>'designation', 'prompt' => 'SELECT Designation','id'=>'designation')) ?>


    <?= $form->field($array['model_userwithproject'], 'project_ids')->widget(\kartik\select2\Select2::classname(), [
        'data' => $array['projects'],
        'options' => ['placeholder' => 'Select Project', 'multiple' => true,],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,

        ],
    ])->label("Projects"); ?>

    <?= $form->field($array['model_userwithregions'], 'region_ids')->widget(\kartik\select2\Select2::classname(), [
        'data' => $array['regions'],
        'options' => ['placeholder' => 'Select Region', 'multiple' => true,],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,

        ],
    ])->label("Regions"); ?>

    <?= $form->field($array['model_userwithareas'], 'area_ids')->widget(\kartik\select2\Select2::classname(), [
        'data' => $array['areas'],
        'id'=>'areas',
        'options' => ['placeholder' => 'Select Areas', 'multiple' => true,],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,

        ],
    ])->label("Areas"); ?>
    <div id="multi_branches">
    <?= $form->field($array['model_userwithbranches'], 'branch_ids')->widget(\kartik\select2\Select2::classname(), [
        'data' => $array['branches'],
        'options' => ['placeholder' => 'Select Branches', 'multiple' => true,],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,

        ],
    ])->label("Branches"); ?>
</div>
    <div style="display: none" id="extra_fields">
    <label>Branch</label>

        <?php $value = isset($model->branch->obj_id) ? $model->branch->obj_id : null; ?>

    <?= Html::dropDownList('branch_id',$value,$array['branches'],array('class'=>'form-control branch_id','initialize'=>true, 'id'=>'branch_id', 'prompt' => 'SELECT Branch')) ?>

    <label>Team</label>
       <?php $value = !empty($model->team->obj_id) ? $model->team->obj_id : null; ?>
        <?php echo \kartik\depdrop\DepDrop::widget([
            'name' => 'team_id',
            'data' => $value ? [$model->team->obj_id => $value] : [],
            'id'=>'team_id',
            'pluginOptions' => ['name'=>'team_id','id'=>'team_id',
                'initialize' => true,
                'depends'  => ['branch_id'],
                'placeholder' => 'Select Team',
                'url' => \yii\helpers\Url::to(['/users/team'])
            ]]); ?>

    <label>Field</label>
        <?php $value = !empty($model->field->obj_id) ? $model->field->obj_id : null; ?>
    <?php echo \kartik\depdrop\DepDrop::widget([
        'name' => 'field_id',
        'data' => $value ? [$model->field->obj_id => $value] : [],
        'id'=>'field_id',
        'pluginOptions' => ['name'=>'field_id','id'=>'field_id',
            'initialize' => true,
            'depends'  => ['team_id'],
            'placeholder' => 'Select Field',
            'url' => \yii\helpers\Url::to(['/users/field'])
        ]]); ?>

    </div>

    <h4>Other Fields</h4>
    <!--<?/*= $form->field($model, 'id')->textInput() */?>-->

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'father_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alternate_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
    <!--
    <?/*= $form->field($model, 'auth_key')->textInput(['maxlength' => true]) */?>

    <?/*= $form->field($model, 'password_hash')->textInput(['maxlength' => true]) */?>

    <?/*= $form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) */?>

    <?/*= $form->field($model, 'last_login_at')->textInput() */?>

    <?/*= $form->field($model, 'last_login_token')->textInput(['maxlength' => true]) */?>

-->
    <!--<?/*= $form->field($model, 'latitude')->textInput() */?>-->

   <!--<?/*= $form->field($model, 'address')->textInput(['maxlength' => true]) */?>-->

    <!--<?/*= $form->field($model, 'longitude')->textInput() */?>-->

    <!--<?/*= $form->field($model, 'image')->textInput(['maxlength' => true]) */?>-->

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>

    <!--<?/*= $form->field($model, 'joining_date')->textInput() */?>-->
    <?= $form->field($model, "joining_date")->widget(\yii\jui\DatePicker::className(),[
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Joining Date']
    ])->label('Joining Date');  ?>

    <?= $form->field($model, 'city_id')->textInput()->dropDownList($array['cities']) ?>

    <?= $form->field($model, 'cnic')->textInput(); ?>

    <?= $form->field($model, 'emp_code')->textInput(['maxlength' => true]) ?>

<!--   <?/*= $form->field($model, 'is_block')->textInput() */?> -->

    <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>

    <!--<?/*= $form->field($model, 'block_date')->textInput() */?>-->
    <?= $form->field($model, 'is_block')->dropDownList(array('0'=>'No','1'=>'Yes')) ?>

    <?= $form->field($model, 'do_reset_password')->dropDownList(array('0'=>'0','1'=>'1')) ?>

    <?= $form->field($model, 'do_complete_profile')->dropDownList(array('0'=>'0','1'=>'1')) ?>
    <!--<?/*= $form->field($model, 'team_name')->textInput(['maxlength' => true]) */?>-->

    <?= $form->field($model, 'status')->textInput() ?>

    <!--<?/*= $form->field($model, 'assigned_to')->textInput() */?>

    <?/*= $form->field($model, 'created_by')->textInput() */?>

    <?/*= $form->field($model, 'updated_by')->textInput() */?>-->
<!--
    <?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>

  -->
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
