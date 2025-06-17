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
        'action' => ['user-report'],
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
                    'depends' => ['userssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['userssearch-region_id'],
                    'placeholder' => 'All Areas',
                    'url' => Url::to(['/users/area'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            $value = !empty($model->branch_id) ? $model->branch_id : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['userssearch-area_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'All Branches',
                    'url' => Url::to(['/users/branch'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->team_id) ? $model->team_id : null;
            echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['userssearch-branch_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'All Teams',
                    'url' => Url::to(['/users/team'])
                ],
                'data' => $value ? [$model->team_id => $value] : []
            ])->label('Team');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->field_id) ? $model->field_id : null;
            echo $form->field($model, 'field_id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['userssearch-team_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'All Fields',
                    'url' => Url::to(['/users/field'])
                ],
                'data' => $value ? [$model->field_id => $value] : []
            ])->label('Field');
            ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'username')->label("Name")->textInput(['placeholder' => 'Name', 'class' => 'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'emp_code')->label("Emp Code")->textInput(['placeholder' => 'Emp Code', 'class' => 'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->label("CNIC")->textInput(['placeholder'=>'CNIC', 'class'=>'form-control form-control-sm']) ?>
        </div>
    <div class="col-sm-3">
        <?php
        echo $form->field($model, 'report_date')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Report Date'],
            'pluginOptions'=>[
                'startDate'      => date("y-m-d"),
                'locale'=>[
                    'format'=>'Y-m-d',
                ]
            ]
        ])->label("Report Date");
        ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'role')->label("Role")->textInput(['class' => 'form-control form-control-sm'])->dropDownList($roles,['prompt'=>'Select Role']) ?>
    </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'platform')->label("Platform")->textInput(['class' => 'form-control form-control-sm'])->dropDownList(["1"=>"Web","2"=>"Mobile"],['prompt'=>'Select Platform']) ?>
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