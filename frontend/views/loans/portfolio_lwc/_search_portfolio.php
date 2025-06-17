<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<!--<div class="container-fluid collapse border-0" id="demo">-->
<div class="container-fluid  border-0">
    <?php $form = ActiveForm::begin([
        'action' => ['portfolio_lwc'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'region_id')->dropDownList($regions, ['class' => 'form-control input-sm', 'prompt' => 'All Regions'])->label('Region');
            ?>
        </div>

        <div class="col-sm-2">
            <?php
            /*print_r($model->loan->area->name);
            die();*/
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['portfoliosearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['portfoliosearch-region_id'],
                    'placeholder' => 'All Areas',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>

        <div class="col-sm-2">
            <?php
            $value = !empty($model->branch_id) ? $model->branch_id : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['portfoliosearch-area_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'All Branches',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            /* echo $form->field($model, 'project_id')->widget(Select2::classname(), [
                 'data' => array_merge(["" => ""], $projects),
                 'options' => ['placeholder' => 'Select Project'],
                 'size' => Select2::SMALL,
                 'pluginOptions' => [
                     'allowClear' => true,
                 ],
             ])->label("Project");*/
            echo $form->field($model, 'project_id')->dropDownList($projects, ['class' => 'form-control input-sm', 'prompt' => 'All Projects'])->label('Projects');

            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'report_date')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Recovery Date'],
                'pluginOptions'=>[
                    'startDate'      => date("y-m-d"),
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ],
                    'minDate' => (string) date('Y-m-d', strtotime('- 3 month')),
                    'maxDate' => (string) date('Y-m-d'),
                ]
            ])->label("Disbursement Date");
            ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'sanction_no')->label("Sanction No")->textInput(['placeholder' => 'Sanction No', 'class' => 'form-control input-sm']) ?>
        </div>


    </div>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'name')->label("Member Name")->textInput(['placeholder' => 'Member Name', 'class' => 'form-control input-sm']) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'parentage')->label("Member Parentage")->textInput(['placeholder' => 'Member Parentage', 'class' => 'form-control input-sm']) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'loan_amount')->label("Loan Amount")->textInput(['placeholder' => 'Loan Amount', 'class' => 'form-control input-sm']) ?>
        </div>

    </div>
    <div class="row pull-right">
        <div class="form-group">
            <?= Html::submitButton('Export', ['value'=>'export','name'=>'export','class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<!--<a href="#demo" class="btn btn-primary" data-toggle="collapse"><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>-->