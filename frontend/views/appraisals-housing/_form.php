<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsHousing */
/* @var $form yii\widgets\ActiveForm */

$js = "
$(document).ready(function(){
     var selected_value =  $(\"#appraisalshousing-property_type\").val();
        if(selected_value == 'house'){
               $('.field-appraisalshousing-duration_type').show();
               $('.field-appraisalshousing-living_duration').show();
               $('.field-appraisalshousing-no_of_rooms').show();
               $('.field-appraisalshousing-no_of_kitchens').show();
               $('.field-appraisalshousing-no_of_toilets').show();
               
              $('#appraisalshousing-duration_type').attr('required', 'required');
              $('#appraisalshousing-living_duration').attr('required', 'required');
              $('#appraisalshousing-no_of_rooms').attr('required', 'required');
              $('#appraisalshousing-no_of_kitchens').attr('required', 'required');
              $('#appraisalshousing-no_of_toilets').attr('required', 'required');
             
        }else{
               $('.field-appraisalshousing-duration_type').hide();
               $('.field-appraisalshousing-living_duration').hide();
               $('.field-appraisalshousing-no_of_rooms').hide();
               $('.field-appraisalshousing-no_of_kitchens').hide();
               $('.field-appraisalshousing-no_of_toilets').hide();
               
               $('#appraisalshousing-duration_type').removeAttr('required');
               $('#appraisalshousing-living_duration').removeAttr('required');
               $('#appraisalshousing-no_of_rooms').removeAttr('required');
               $('#appraisalshousing-no_of_kitchens').removeAttr('required');
               $('#appraisalshousing-no_of_toilets').removeAttr('required');
              

        }  
        
     $('#appraisalshousing-property_type').change(function(){
        var selected_value = $('#appraisalshousing-property_type').val();
        if(selected_value == 'house'){
               $('.field-appraisalshousing-duration_type').show();
               $('.field-appraisalshousing-living_duration').show();
               $('.field-appraisalshousing-no_of_rooms').show();
               $('.field-appraisalshousing-no_of_kitchens').show();
               $('.field-appraisalshousing-no_of_toilets').show();
               
               $('#appraisalshousing-duration_type').attr('required', 'required');
               $('#appraisalshousing-living_duration').attr('required', 'required');
               $('#appraisalshousing-no_of_rooms').attr('required', 'required');
               $('#appraisalshousing-no_of_kitchens').attr('required', 'required');
               $('#appraisalshousing-no_of_toilets').attr('required', 'required');
        }else{
               $('.field-appraisalshousing-duration_type').hide();
               $('.field-appraisalshousing-living_duration').hide();
               $('.field-appraisalshousing-no_of_rooms').hide();
               $('.field-appraisalshousing-no_of_kitchens').hide();
               $('.field-appraisalshousing-no_of_toilets').hide();
               
               $('#appraisalshousing-duration_type').removeAttr('required');
               $('#appraisalshousing-living_duration').removeAttr('required');
               $('#appraisalshousing-no_of_rooms').removeAttr('required');
               $('#appraisalshousing-no_of_kitchens').removeAttr('required');
               $('#appraisalshousing-no_of_toilets').removeAttr('required');
        }  
    }); 
});

";


$this->registerJs($js);

?>

<div class="appraisals-housing-form">

    <?php $form = ActiveForm::begin(); ?>

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
                        'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new \yii\web\JsExpression('function(city) { return city.text; }'),
                    'templateSelection' => new \yii\web\JsExpression('function (city) { return city.text; }'),
                    'disabled' => true
                ],
            ])->label('Application');
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'property_type')->dropDownList(\common\components\Helpers\ListHelper::getLists('property_type')) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'ownership')->dropDownList(\common\components\Helpers\ListHelper::getLists('ownership')) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'land_area')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_rooms')) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'residential_area')->textInput() ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'duration_type')->dropDownList(\common\components\Helpers\ListHelper::getLists('duration_type')) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'living_duration')->textInput() ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'no_of_rooms')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_rooms')) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'no_of_kitchens')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_kitchens')) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'no_of_toilets')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_toilets')) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'purchase_price')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'current_price')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>
        </div>
    </div>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>

