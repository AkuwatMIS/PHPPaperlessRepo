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
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'branch_id')->widget(Select2::classname(), [
                'data' => $branches,
                'options' => ['placeholder' => 'Select Branch'],
                'size' => Select2::SMALL,
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            $value = !empty($model->team_id) ? $model->team->name : null;
            echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['donationssearch-branch_id'],
                    'initialize' => true,
                    'initDepends' => ['donationssearch-branch_id'],
                    'placeholder' => 'Select Team',
                    'url' => Url::to(['/structure/fetch-team-by-branch'])
                ],
                'data' => $value ? [$model->team_id => $value] : []
            ])->label('Team');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            $value = !empty($model->field_id) ? $model->field->name : null;
            echo $form->field($model, 'field_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['donationssearch-team_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'Select Field',
                    'url' => Url::to(['/structure/fetch-field-by-team'])
                ],
                'data' => $value ? [$model->field_id => $value] : []
            ])->label('Field');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'sanction_no')->textInput(['placeholder'=>'Sanction No'])->label('Sanction No');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'receive_date')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Recovery Date'],
                'pluginOptions'=>[
                    'startDate'      => date("y-m-d"),
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ])->label("Receive Date");
            ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'member_cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->textInput(['maxlength' => true, 'placeholder' => 'CNIC', 'class' => 'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'receipt_no')->textInput(['placeholder'=>'Receipt No'])->label('Receipt No');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'amount')->textInput(['placeholder'=>'Amount'])->label('Mdp Amount');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project');
            ?>
        </div>
        <div class="col-sm-2">

        </div>
    </div>
    <div class="row pull-right">
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
