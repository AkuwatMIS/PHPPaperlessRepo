<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\StructureHelper;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid collapse border-0" id="demo">
    <?php $form = ActiveForm::begin([
        'action' => ['emergency-loans-city-wise'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'province_id')->dropDownList($provinces, ['prompt' => 'Select Province'])->label('Province'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'district_id')->dropDownList($districts, ['prompt' => 'Select District'])->label('District'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'city_id')->dropDownList($cities, ['prompt' => 'Select City'])->label('City'); ?>
        </div>

        <!--<div class="col-sm-3">
            <?php
/*            $value = !empty($model->area_id) ?  $model->area_id  : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['emergencyloanssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['emergencyloanssearch-region_id'],
                    'placeholder' => 'Select Area',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            */?>
        </div>

        <div class="col-sm-3">
            <?php
/*            $value = !empty($model->branch_id) ? $model->branch_id : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['emergencyloanssearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['emergencyloanssearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            */?>
        </div>-->
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'date_disbursed')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Disbursed Date'],
                'pluginOptions'=>[
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ])->label("Disbursed Date");
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'member_cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->label("CNIC")->textInput(['placeholder'=>'CNIC', 'class'=>'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'sanction_no')->label("Sanction No")->textInput(['placeholder'=>'Sanction No', 'class'=>'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'project_id')->dropdownList($projects_name,['prompt'=>'Select Project'])->label('Project'); ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'donated_date')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Donated Date'],
                'pluginOptions'=>[
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ])->label("Donated Date");
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'status')->dropdownList(
                [
                    0 => "Not Donated",
                    1 => "Donated",
                ],['prompt'=>'Select Status'])->label('Status'); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
        class="glyphicon glyphicon-search"></span> Advanced Search</a>
