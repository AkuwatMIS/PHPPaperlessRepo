<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
//use yii\jui\DatePicker;

use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\search\LoanWriteOffSearch  */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid collapse border-0" id="demo">

    <?php $form = ActiveForm::begin([
        'action' => ['write-off-export'],
        'method' => 'POST',
    ]); ?>

    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region'); ?>
        </div>

        <div class="col-sm-2">
            <?php
            $value = !empty($model->area_id) ? $model->area->name : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['loanwriteoffsearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['loanwriteoffsearch-region_id'],
                    'placeholder' => 'Select Area',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>

        <div class="col-sm-2">
            <?php
            $value = !empty($model->branch_id) ? $model->branch->name : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['loanwriteoffsearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'project_id')->dropdownList($projects,['prompt'=>'Select Project'])->label('Project'); ?>
        </div>

        <div class="col-sm-2">

            <?= $form->field($model, 'type')->dropDownList([0=>'Recovery',1=>'Funeral Charges'],['required'=>true,'prompt'=>'Select Type']) ?>
        </div>

        <div class="col-sm-2">

            <?= $form->field($model, 'reason')->dropDownList(['disable'=>'Permanently Disable','death'=>'Death'],['prompt'=>'Select Reason']) ?>
        </div>

        <div class="col-sm-2">

            <?= $form->field($model, 'bank_name')->textInput(['prompt'=>'Select Bank Name']) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'bank_account_no')->textInput(['prompt'=>'Select Account No']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'status')->dropDownList([0=>'Pending',1=>'Approved',2=>'Rejected'],['prompt'=>'Select Reason']) ?>
        </div>

        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'from_date')->widget(DatePicker::classname(), [
                'convertFormat'=>true,
                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                'pickerIcon' => '<i class="fa fa-calendar-alt text-primary"></i>',
                'removeIcon' => '',
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'From Date'],
                'pluginOptions'=>[
                    'startView'=>'month',
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ]);
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'to_date')->widget(DatePicker::classname(), [
                'convertFormat'=>true,
                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                'pickerIcon' => '<i class="fa fa-calendar-alt text-primary"></i>',
                'removeIcon' => '',
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'To Date'],
                'pluginOptions'=>[
                    'startView'=>'month',
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ]);
            ?>
        </div>

    </div>
    <div class="row pull-right">
        <div class="form-group">
            <?= Html::submitButton('Export', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse"><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>