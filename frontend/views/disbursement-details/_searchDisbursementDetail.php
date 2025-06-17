<?php

use app\models\Branches;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\LoansSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid collapse border-0" id="demo">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);
    ?>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'bank_name')->dropdownList($bank_names,['prompt'=>'Select Bank Name'])->label('Bank Name'); ?>
        </div>
       <!-- <div class="col-sm-3">
            <?/*= $form->field($model, 'project_id')->dropdownList($projects_name,['prompt'=>'Select Project'])->label('Project'); */?>
        </div>-->
        <div class="col-sm-3">
            <?php
            $payment_mthods = ArrayHelper::map(\common\models\PaymentMethods::find()->select(["id",'CONCAT(name, "(",type,")") as name'])->asArray()->all(), 'id', 'name');
            echo $form->field($model, 'payment_method_id')->dropDownList($payment_mthods, ['prompt' => 'Select Payment Method'])->label('Payment Method');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            //$regions = ArrayHelper::map(Regions::find()->asArray()->all(), 'id', 'name');
            echo $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['disbursementdetailssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['disbursementdetailssearch-region_id'],
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
                    'depends' => ['disbursementdetailssearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['disbursementdetailssearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
       <!-- <div class="col-sm-3">
            <?/*= $form->field($model, 'branch_id')->dropdownList($branches_names,['prompt'=>'Select Branch'])->label('Branch'); */?>
        </div>-->
        <div class="col-sm-3">
            <?php
            $projects = ArrayHelper::map(\common\models\Projects::find()->asArray()->all(), 'id', 'name');
            echo $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project');
            ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'status')->dropdownList(\common\components\Helpers\ListHelper::getDisbursementDetailStatus(),['prompt'=>'Select','required'=>true])->label('Status'); ?>
        </div>
        <div class="col-sm-3">
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
        </div>

    <div class="row pull-right">
        <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        </div></div>
    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
