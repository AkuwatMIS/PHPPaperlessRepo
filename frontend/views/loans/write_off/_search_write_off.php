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
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid collapse border-0" id="demo">

    <?php $form = ActiveForm::begin([
        'action' => ['writeoff'],
        'method' => 'get',
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
                    'depends' => ['loanssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['loanssearch-region_id'],
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
                    'depends' => ['loanssearch-area_id'],
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
        <?= $form->field($model, 'sanction_no')->textInput()->label('Sanction No') ?>
        </div>

    </div>
    <div class="row">

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
            <?= $form->field($model, 'activity_id')->dropdownList($activities,['prompt'=>'Select Activity'])->label('Purpose'); ?>
        </div>

    </div>

    <div class="row pull-right">
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse"><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>