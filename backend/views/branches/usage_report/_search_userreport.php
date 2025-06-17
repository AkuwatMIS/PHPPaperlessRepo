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

<div class="container-fluid collapse border-0" id="demo">

    <?php $form = ActiveForm::begin([
        'action' => ['usage-report'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'region_id')->dropDownList($regions, ['class' => 'form-control input-sm', 'prompt' => 'All Regions'])->label('Region');
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['branchessearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['branchessearch-region_id'],
                    'placeholder' => 'All Areas',
                    'url' => Url::to(['/users/area'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            $value = !empty($model->id) ? $model->id : null;
            echo $form->field($model, 'id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['branchessearch-area_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'All Branches',
                    'url' => Url::to(['/users/branch'])
                ],
                'data' => $value ? [$model->id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'report_date')->widget(DateRangePicker::classname(), [
                'convertFormat' => true,
                'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Report Date'],
                'pluginOptions' => [
                    'startDate' => date("y-m-d"),
                    'locale' => [
                        'format' => 'Y-m-d',
                    ]
                ]
            ])->label("Report Date");
            ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'platform')->label("Platform")->textInput(['class' => 'form-control form-control-sm'])->dropDownList(["1" => "Web", "2" => "Mobile"], ['prompt' => 'Select Platform']) ?>
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