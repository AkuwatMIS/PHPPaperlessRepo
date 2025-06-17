<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use \yii\jui\DatePicker;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\StructureHelper;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
$js = "
$(document).ready(function(){
 $(\".range_no\").hide();
 $(\".date_yes\").hide();
$(\"#kamyabpakistansearch-nadra_verisys_status\").change(function(){
        var selected_value =  $(\"#kamyabpakistansearch-nadra_verisys_status\").val();
        if(selected_value === 'no'){
        
            $(\".range_no\").show();
            $(\".date_yes\").hide();
        } else if(selected_value === 'yes'){

            $(\".range_no\").hide();
            $(\".date_yes\").show();
        }
    });
});
";
$this->registerJs($js);
?>
<style>
   .kv-date-picker .input-group-addon{
       all: unset;
   }
</style>
<div class="container-fluid collapse border-0" id="demo">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">

        <div class="col-sm-3">
            <?= $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region'); ?>
        </div>

        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ?  $model->area_id  : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['kamyabpakistansearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['kamyabpakistansearch-region_id'],
                    'placeholder' => 'Select Area',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            $value = !empty($model->branch_id) ? $model->branch_id : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['kamyabpakistansearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['kamyabpakistansearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->label("CNIC")->textInput(['placeholder'=>'CNIC', 'class'=>'form-control form-control-sm']) ?>
        </div>

        <div class="col-sm-3 range_no">
            <?php
            echo $form->field($model, 'application_date')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Creation Date Range'],
                'pluginOptions'=>[
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ])->label("Nadra Upload Date");
            ?>
        </div>
        <div class="col-sm-3 date_yes">
            <?= $form->field($model, 'created_at')->widget(\kartik\date\DatePicker::className(), [
                // if you are using bootstrap, the following line will set the correct style of the input field
                'options' => ['class' => 'form-control','placeholder' => 'Creation Date'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy'
                ]
                // ... you can configure more DatePicker properties here
            ])->label('Nadra Upload Date') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'nadra_verisys_status')->dropDownList(['yes' => 'YES','no' => 'NO'], ['prompt' => 'Select Verisys'])->label('NADRA Verisys'); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
