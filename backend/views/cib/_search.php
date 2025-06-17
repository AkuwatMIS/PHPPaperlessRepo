<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \yii\helpers\Url;
use \kartik\depdrop\DepDrop;
use \kartik\daterange\DateRangePicker;
use \common\components\Helpers\ListHelper;

/* @var $this yii\web\View */
/* @var $model common\models\search\ApplicationsCibSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="applications-cib-search" id="cib-data">

    <?php $form = ActiveForm::begin([
        'action' => ['index-search'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'application_id') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'type')->dropDownList(['0' => 'Json Response', '1' => 'Pdf File'], ['prompt' => 'Select Response Type'])->label('Response Type'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'receipt_no') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'status')->dropdownList(['0' => 'pending', '1' => 'success', '3' => 'error', '9' => 'noAction'], ['prompt' => 'Select'])->label('Status'); ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'region_id')->dropDownList(\yii\helpers\ArrayHelper::map($regions, 'id', 'name'), ['prompt' => 'Select Region'])->label('Region'); ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['applicationscibsearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['applicationscibsearch-region_id'],
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
                    'depends' => ['applicationscibsearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['applicationscibsearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'cib_type_id')->dropDownList(['0' => 'Tasdeeq', '1' => 'Tasdeeq', '2' => 'datacheck'], ['prompt' => 'Service Provider'])->label('Service Provider'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'project_id')->dropdownList(\yii\helpers\ArrayHelper::map($projects, 'id', 'name'), ['prompt' => 'Select'])->label('Project'); ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'updated_at')->widget(DateRangePicker::classname(), [
                'convertFormat' => true,
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Updated Date'],
                'pluginOptions' => [
                    'locale' => [
                        'format' => 'Y-m-d',
                    ]
                ]
            ])->label("Updated Date");
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
