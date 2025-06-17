<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */

$js = '
$(document).ready(function(){
    
    $("#crop-type").hide();
    if($("#loanssearch-project_ids").val()=="3"){
            $("#crop-type").show();
       }else{
            $("#crop-type").hide();
       }
    $("#loanssearch-project_ids").change(function(){
       var selected_project =  $("#loanssearch-project_ids").val();
       if(selected_project=="3"){
            $("#crop-type").show();
       }else{
            $("#crop-type").hide();
       }
    });
});
';

$this->registerJs($js);

?>

<div class="container-fluid border-0">

    <?php $form = ActiveForm::begin([
        'action' => ['disbursement-summary'],
        'method' => 'post',
    ]); ?>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, "date_disbursed")->widget(\yii\jui\DatePicker::className(),[
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Disbursement Date', 'readonly' => 'readonly'],
                'clientOptions'=>[
                    'minDate' => date('Y-m-01'),
                    'maxDate' => date('Y-m-d'),
                ]
            ])->label('Disbursement Date');  ?>
           <!-- <?php
/*            echo $form->field($model, 'date_disbursed')->widget(DateRangePicker::classname(), [
                'convertFormat' => true,
                'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Disbursement Date'],
                'pluginOptions' => [
                    'startDate' => date("y-m-d"),
                    'locale' => [
                        'format' => 'Y-m-d',
                    ]
                ]
            ])->label("Disbursement Date");
            */?>-->
        </div>

        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'All Regions'])->label('Region');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            /*print_r($model->loan->area->name);
            die();*/
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['loanssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['loanssearch-region_id'],
                    'placeholder' => 'All Areas',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            $value = !empty($model->branch_id) ? $model->branch_id: null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['loanssearch-area_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'All Branches',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>




        <div class="col-sm-4">
                <?php
                echo $form->field($model, 'project_ids')->widget(Select2::classname(), [
                    'data' => $projects,
                    'options' => ['placeholder' => 'Select Project'],
                    //'size' => Select2::SMALL,
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear' => true
                    ],
                ])->label("Project");
                ?>

            <?php
/*
            $value = !empty($model->project_ids) ? $model->project_ids : null;
            echo $form->field($model, "project_ids")->widget(\kartik\depdrop\DepDrop::classname(), [
                'type'=>\kartik\depdrop\DepDrop::TYPE_SELECT2,
                'options' => [
                    'class' => 'form-control update-trigger field-selection',
                    'data-field_index' => '1',
                    'multiple' => true,
                    'theme' => Select2::THEME_BOOTSTRAP,
                ],
                'pluginOptions' => [
                    //'multiple' => true,
                    //'allowClear' => true,
                    //'initialize' => true,
                    'depends' => ['loanssearch-branch_id'],
                    'initDepends' => ['loanssearch-branch_id'],
                    'placeholder' => 'Select Projects',
                    'url' => \yii\helpers\Url::to(['/structure/branchprojectsbyid'])
                ],
                'data' => $value
            ])->label('Projects');
            */?>
            <?php
            /*echo $form->field($model, 'project_ids')->widget(Select2::classname(), [
                'data' => $projects,
                'options' => ['placeholder' => 'Select Project'],
                'pluginOptions' => [
                    'multiple' => true,
                    'allowClear' => true
                ],
            ])->label("Project");
            */?>
        </div>

        <div class="col-sm-6" id="crop-type">
            <?php
            echo $form->field($model, 'crop_type')->dropDownList($crop_types, ['prompt' => 'Crop Type'])->label('Crop Type');
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3 pull-left">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>