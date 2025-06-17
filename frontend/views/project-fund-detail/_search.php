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
/* @var $model common\models\search\LoansSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]);

    ?>
    <div class="row">
    <div class="row collapse border-0" id="demo">

        <div class="col-sm-3">
            <?= $form->field($model, 'disbursement_source')->dropdownList($bank_names, ['prompt' => 'Select Disbursement Source'])->label('Disbursement Source'); ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project');
            ?>
        </div>
        <div class="col-sm-3">
        <?= $form->field($model, 'allocation_date')->widget(\yii\jui\DatePicker::className(), [
            // if you are using bootstrap, the following line will set the correct style of the input field
            'options' => ['class' => 'form-control'],
            // ... you can configure more DatePicker properties here
        ])->label('Batch Creation Date') ?>
        </div>
        <div class="col-sm-3">
        <?= $form->field($model, 'received_at')->widget(\yii\jui\DatePicker::className(), [
            // if you are using bootstrap, the following line will set the correct style of the input field
            'options' => ['class' => 'form-control'],
            // ... you can configure more DatePicker properties here
        ])->label('Receive Date') ?>
        </div>
        <div class="row pull-right">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary search_button','id'=>'seacrch']) ?>
            </div>
        </div>
    </div>
    </div>
    <?php ActiveForm::end(); ?>


</div>

<a href="#demo" class="btn btn-primary" data-toggle="collapse"><span
            class="glyphicon glyphicon-search"></span> Advance Filter </a>
<br><br><br><br>