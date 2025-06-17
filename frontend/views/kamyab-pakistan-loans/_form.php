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
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'member_cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->label("CNIC")->textInput(['placeholder'=>'CNIC', 'class'=>'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'sanction_no')->label("Sanction No")->textInput(['placeholder'=>'Sanction No', 'class'=>'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'group_no')->label("Group No")->textInput(['placeholder'=>'Group No', 'class'=>'form-control form-control-sm']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
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
        <div class="col-sm-4">
            <?php
            echo $form->field($model, 'status')->dropDownList($status, ['prompt' => 'Select Status'])->label('Status');
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            echo $form->field($model, 'is_sync')->dropDownList($is_sync, ['prompt' => 'Select Sync'])->label('Is Sync');
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
