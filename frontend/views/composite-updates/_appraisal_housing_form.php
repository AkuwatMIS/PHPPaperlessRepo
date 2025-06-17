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

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Update Housing Appraisal</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="appraisals-housing-form">

            <?php $form = \yii\widgets\ActiveForm::begin(['action' => 'update-housing-appraisal?id=' . $model->application_id]); ?>
            <div class="row">
                <div class="col-sm-3">
                    <?= $form->field($model, 'property_type')->dropDownList(\common\components\Helpers\ListHelper::getLists('property_type')) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'ownership')->dropDownList(\common\components\Helpers\ListHelper::getLists('ownership')) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'land_area')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_rooms')) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'residential_area')->textInput() ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'purchase_price')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'current_price')->textInput(['maxlength' => true]) ?>
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
                <div class="col-sm-12">
                    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>
                </div>
            </div>

            <?php if (!Yii::$app->request->isAjax) { ?>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            <?php } ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

