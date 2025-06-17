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
        'action' => ['summary'],
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
       <!-- <div class="col-sm-3">
            <?php /*= $form->field($model, 'created_at')->widget(\yii\jui\DatePicker::className(), [
                // if you are using bootstrap, the following line will set the correct style of the input field
                'options' => ['class' => 'form-control'],
                // ... you can configure more DatePicker properties here
            ])->label('Application Created Date') */ ?>
        </div>-->
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'created_at')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Application Creation Date'],
                'pluginOptions'=>[
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ])->label("Application Creation Date Range");
            ?>
        </div>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
