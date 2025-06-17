<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Applications */
/* @var $form yii\widgets\ActiveForm */

$js = "

$(document).ready(function(){
//$(\".fee\").hide();
project_id = $(\"#applications-project_id\").val();
        if(project_id=='3' || project_id=='4' || project_id=='26'){
             $(\".fee\").hide();
             $('#applications-fee').removeAttr('required');
             $('#applications-fee').val('');
        }
        else{
            $(\".fee\").show();
            $('#applications-fee').attr('required', 'required');
            $('#applications-fee').attr('readonly', 'readonly');
            $('#applications-fee').val('200');
        }
        if(project_id){
            $.ajax({
                type: \"POST\",
                url: '/applications/validate-req-amount?id='+project_id,
                success: function(data){
                    var obj = $.parseJSON(data);
                  $('#applications-req_amount').attr('max',obj.limit);
    
                }
            });
        }
 $(\"#applications-project_id\").on('change', function() {
        project_id = $(\"#applications-project_id\").val();
        if(project_id=='3' || project_id=='4' || project_id=='26'){
             $(\".fee\").hide();
             $('#applications-fee').removeAttr('required');
             $('#applications-fee').val('');
        }
        else{
            $(\".fee\").show();
            $('#applications-fee').attr('required', 'required');
            $('#applications-fee').attr('readonly', 'readonly');
            $('#applications-fee').val('200');
        }
        
        $.ajax({
            type: \"POST\",
            url: '/applications/validate-req-amount?id='+project_id,
            success: function(data){
                var obj = $.parseJSON(data);
              $('#applications-req_amount').attr('max',obj.limit);

            }
        });
    });
        var selected_value =  $(\"#applications-who_will_work\").val();
        if(selected_value != \"self\" && selected_value!=''){
             
               $(\".field-applications-name_of_other\").show();
               $(\".field-applications-other_cnic\").show();
               $('#applications-name_of_other').attr('required', 'required');
               
        }else{
               $(\"#applications-name_of_other\").val('');
               $(\"#applications-other_cnic\").val('');
               $('#applications-name_of_other').removeAttr('required');
               $(\".field-applications-name_of_other\").hide();
               $(\".field-applications-other_cnic\").hide();
        }
 
    //$(\".field-applications-activity_id\").hide();
    $(\"#applications-product_id\").change(function(){
   
        var selected_value =  $(\"#applications-product_id\").val();
        if(selected_value == 1){
         $(\".activity\").show();
               //$(\".field-applications-activity_id\").show();
               $('#applications-activity_id').attr('required', 'required');

        }else{
               $('#applications-activity_id').removeAttr('required');
                        $(\".activity\").hide();
               //$(\".field-applications-activity_id\").hide();
        }
    });

   
    $(\"#applications-who_will_work\").change(function(){
        var selected_value =  $(\"#applications-who_will_work\").val();
        if(selected_value != \"self\" && selected_value!=''){
               $(\"#applications-name_of_other\").val('');
               $(\"#applications-other_cnic\").val(''); 
               $('#applications-name_of_other').attr('required', 'required');
               $(\".field-applications-name_of_other\").show();
               $(\".field-applications-other_cnic\").show();

        }else{
               $(\"#applications-name_of_other\").val('');
               $(\"#applications-other_cnic\").val('');
               $('#applications-name_of_other').removeAttr('required');
               $(\".field-applications-name_of_other\").hide();
               $(\".field-applications-other_cnic\").hide();
        }
    });
$(\"#applications-status\").change(function(){
        var selected_value =  $(\"#applications-status\").val();
        if(selected_value == \"rejected\"){
           $(\"#rejectreason\").show();
        }
    });
});
";
$this->registerJs($js);
?>

<div class="applications-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'member_id')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'fee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_id')->dropDownList($array['projects'],['prompt'=>'Select Project'])->label('Project') ?>

    <?php
    $value = !empty($model->product_id) ? $model->product->name : null;
    echo $form->field($model, 'product_id')->widget(DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['applications-project_id'],
            'initialize' => true,
            'initDepends' => ['applications-project_id'],
            'placeholder' => 'Select Product',
            'url' => Url::to(['/structure/fetch-product-by-project'])
        ],
        'data' => $value ? [$model->product_id => $value] : []
    ])->label('Product');
    ?>

    <?php
    $value = !empty($model->activity_id) ? $model->activity->name : null;
    echo $form->field($model, 'activity_id')->widget(DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['applications-product_id'],
            //'initialize' => true,
            //'initDepends'=>['progressreportdetailssearch-area_id'],
            'placeholder' => 'Select Activity',

            'url' => Url::to(['/structure/fetch-activity-by-product'])
        ],
        'data' => $value ? [$model->activity_id => $value] : []
    ])->label('Activity');
    ?>
        <?php
        if(!($model->isNewRecord) && ($model->project_id=='52' || $model->project_id=='77' || $model->project_id=='76' || $model->project_id=='61' || $model->project_id=='62' || $model->project_id=='64')) {
            $value = !empty($model->sub_activity) ? $model->sub_activity : null;
            echo $form->field($model, 'sub_activity', ['enableClientValidation' => false])->widget(DepDrop::classname(), [
                'type' => DepDrop::TYPE_SELECT2,
                'options' => [
                    'multiple' => true,
                    'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                ],
                'pluginOptions' => [
                    'depends' => ['applications-activity_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'Select Sub Activity',
                    'url' => Url::to(['/structure/fetch-sub-activity-by-activity'])
                ],
                'data' => $value
            ])->label('Sub Activity');
        }
        ?>
    <?= $form->field($model, 'region_id')->dropDownList(($array['regions']),['prompt'=>'Select Region'])->label('Region') ?>


        <?php
        $value = !empty($model->area_id) ? $model->area_id : null;
        echo $form->field($model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['applications-region_id'],
                'initialize' => true,
                'initDepends' => ['applications-region_id'],
                'placeholder' => 'Select Area',
                'url' => \yii\helpers\Url::to(['/structure/fetch-areas-by-region'])
            ],
            'data' => $value ? [$model->area_id => $value] : []
        ])->label('Area');
        ?>

        <?php
        $value = !empty($model->branch_id) ? $model->branch_id : null;
        echo $form->field($model, 'branch_id')->widget(\kartik\depdrop\DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['applications-area_id'],
                'initialize' => true,
                'initDepends' => ['applications-area_id'],
                'placeholder' => 'Select Branch',
                'url' => \yii\helpers\Url::to(['/structure/fetch-branches-by-area'])
            ],
            'data' => $value ? [$model->branch_id => $value] : []
        ])->label('Branch');
        ?>

    <?php
        $value = !empty($model->team_id) ? $model->team_id : null;
        echo $form->field($model, 'team_id')->widget(\kartik\depdrop\DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['applications-branch_id'],
                'initialize' => true,
                'initDepends' => ['applications-branch_id'],
                'placeholder' => 'Select Team',
                'url' => \yii\helpers\Url::to(['/structure/fetch-teams-by-branch'])
            ],
            'data' => $value ? [$model->team_id => $value] : []
        ])->label('Team');
        ?>
        <?php
        $value = !empty($model->field_id) ? $model->field_id : null;
        echo $form->field($model, 'field_id')->widget(\kartik\depdrop\DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['applications-team_id'],
                //'initialize' => true,
                //'initDepends'=>['progressreportdetailssearch-area_id'],
                'placeholder' => 'Select Field',
                'url' => \yii\helpers\Url::to(['/structure/fetch-fields-by-team'])
            ],
            'data' => $value ? [$model->field_id => $value] : []
        ])->label('Field');
        ?>
<!--    \common\components\Helpers\MemberHelper::getMemberStatus()-->
    <?= $form->field($model, 'bzns_cond')->dropDownList(['old'=>'Old','new'=>'New'], ['prompt' => 'Select Bzns Cond', 'class' => 'form-control form-control-sm'])->label('Business Cond') ?>

    <?= $form->field($model, 'who_will_work')->dropDownList(\common\components\Helpers\ApplicationHelper::getWhoWillWork(), ['prompt' => 'Select Who Will Work', 'class' => 'form-control form-control-sm'])->label('Who Will Work') ?>

    <?= $form->field($model, 'name_of_other')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_cnic')->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '99999-9999999-9',
    ])->textInput(['maxlength' => true, 'placeholder' => 'Other CNIC', 'class' => 'form-control'])  ?>

    <?= $form->field($model, 'req_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, "application_date")->widget(\yii\jui\DatePicker::className(),[
        'dateFormat' => 'yyyy-MM-dd',
        //'value' => $model->isNewRecord?date('Y-m-d'):$model->application_date,

        'options' => ['class' => 'form-control', 'placeholder' => 'Application Date',
            'readonly' => 'readonly',
            //'value'=>date('Y-m-d'),
        ],
        'clientOptions'=>[
            'changeMonth' => true,
            'changeYear' => true,
            //'value'=>date('Y-m-d')
        ]
    ])->label('Application Date');  ?>
    <?= $form->field($model, 'client_contribution')->textInput(['min'=>0,'type'=>'number','placeholder'=>'Enter Clent Contribution'])->label('Client Contribution') ?>
    <?= $form->field($model, 'recommended_amount')->textInput(['min'=>0,'type'=>'number','placeholder'=>'Enter Recommended Amount'])->label('Recommended Amount') ?>
    <?= $form->field($model, 'application_no')->textInput(['maxlength' => true,'type'=>'number','min'=>'0','placeholder'=>'Enter Application Number']) ?>
    <?= $form->field($model, 'group_id')->textInput(['maxlength' => true,'type'=>'number','placeholder'=>'Group Id']) ?>
    <?= $form->field($model, 'is_urban')->dropDownList(\common\components\Helpers\ApplicationHelper::getIsUrban(), ['prompt' => 'Select Is Urban', 'class' => 'form-control form-control-sm'])->label('Is Urban') ?>
    <?= $form->field($model, 'referral_id')->dropDownList(common\components\Helpers\ListHelper::getReferralsBackend(), [ 'class'=>'form-control','prompt' => ""]) ?>
    <?= $form->field($model, 'reject_reason')->textarea([ 'class'=>'form-control','placeholder' => 'Select Reject Reason'])->label("Reject Reason") ?>
    <?= $form->field($model, 'is_lock')->dropDownList([0=>'No',1=>'Yes'],['class'=>'form-control','prompt' => 'Select is lock']) ?>

    <?= $form->field($model, 'status')->dropDownList(\common\components\Helpers\ApplicationHelper::getAppStatus(),['prompt'=>'Select Application Status'])->label('Status') ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
