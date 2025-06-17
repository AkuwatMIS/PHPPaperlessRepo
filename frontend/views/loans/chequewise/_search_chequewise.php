<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin([
        'action' => ['chequewisereport'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <!--<div class="col-sm-2">
            <?php
           /*echo $form->field($model, 'region_id')->dropDownList($regions, ['class' => 'form-control input-sm', 'prompt' => 'All Regions'])->label('Region');
            */?>
        </div>

        <div class="col-sm-2">
            <?php
          /*$value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['loanssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['loanssearch-region_id'],
                    'placeholder' => 'All Areas',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            */?>
        </div>-->

        <div class="col-sm-2">
            <?php
          /*$value = !empty($model->branch_id) ? $model->branch_id : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['loanssearch-area_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'All Branches',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            */
            echo $form->field($model, 'branch_id')->dropDownList($branches, ['class' => 'form-control input-sm', 'prompt' => 'All Branches','required'=>'required'])->label('Branch');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'date_disbursed')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Disbursement Date'],
                'pluginOptions'=>[
                    'startDate'      => date("y-m-d"),
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ])->label("Disbursement Date");
            ?>

        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'cheque_no')->label("Cheque No")->textInput(['placeholder' => 'Cheque No', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'sanction_no')->label("Sanction No")->textInput(['placeholder' => 'Sanction No', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'loan_amount')->label("Approved Amount")->textInput(['placeholder' => 'Approved Amount', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'inst_months')->label("Inst Months")->textInput(['placeholder' => 'Inst Months', 'class' => 'form-control input-sm']) ?>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'loan_amount')->label("Loan Amount")->textInput(['placeholder' => 'Loan Amount', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'tranch_no')->label("Tranch No")->textInput(['placeholder' => 'Tranch No', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'tranch_amount')->label("Tranch Amount")->textInput(['placeholder' => 'Tranch Amount', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'group_no')->label("Group No")->textInput(['placeholder' => 'Group No', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'member_name')->label("Name")->textInput(['placeholder' => 'Name', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'member_cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->textInput(['maxlength' => true, 'placeholder' => 'CNIC', 'class' => 'form-control form-control-sm']) ?>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'member_parentage')->label("Parentage")->textInput(['placeholder' => 'Parentage', 'class' => 'form-control input-sm']) ?>
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
            echo $form->field($model, 'inst_type')->widget(Select2::classname(), [
                'data' => array_merge(["" => ""],  \common\components\Helpers\ApplicationHelper::getInstallmentsTypes()),
                'options' => ['placeholder' => 'Select Inst. Type'],
                'size' => Select2::SMALL,
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>


    <div class="row pull-right">
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<!--<a href="#demo" class="btn btn-primary" data-toggle="collapse"><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>-->