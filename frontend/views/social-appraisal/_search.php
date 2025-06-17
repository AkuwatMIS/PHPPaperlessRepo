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
                    'depends' => ['socialappraisalsearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['socialappraisalsearch-region_id'],
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
                    'depends' => ['socialappraisalsearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['socialappraisalsearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->team_id) ? $model->team_id : null;
            echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['socialappraisalsearch-branch_id'],
                    'initialize' => true,
                    'initDepends' => ['socialappraisalsearch-branch_id'],
                    'placeholder' => 'Select Team',
                    'url' => Url::to(['/structure/fetch-team-by-branch'])
                ],
                'data' => $value ? [$model->team_id => $value] : []
            ])->label('Team');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->field_id) ? $model->field_id : null;
            echo $form->field($model, 'field_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['socialappraisalsearch-team_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'Select Field',
                    'url' => Url::to(['/structure/fetch-field-by-team'])
                ],
                'data' => $value ? [$model->field_id => $value] : []
            ])->label('Field');
            ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'member_name')->label("Member Name")->textInput(['placeholder'=>'Member Name', 'class'=>'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'member_cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->label("Member CNIC")->textInput(['placeholder'=>'Member CNIC', 'class'=>'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'application_no')->label("Application No")->textInput(['placeholder'=>'Application No', 'class'=>'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'house_ownership')->dropDownList(\common\components\Helpers\ListHelper::getLists('house_ownership'), [ 'class'=>'form-control form-control-sm','prompt' => 'Select House Ownership'])->label("House Ownerdhip") ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'no_of_earning_hands')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_earning_hands'), [ 'class'=>'form-control form-control-sm','prompt' => 'Select No Of Earning Hands'])->label("No Of Earning Hands") ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'source_of_income')->dropDownList(\common\components\Helpers\ListHelper::getLists('source_of_income'), [ 'class'=>'form-control form-control-sm','prompt' => 'Select Source Of Income'])->label("Source Of Income") ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'monthly_savings')->dropDownList(\common\components\Helpers\ListHelper::getLists('monthly_savings'), [ 'class'=>'form-control form-control-sm','prompt' => 'Select Monthly Savings'])->label("Monthly Savings") ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'fatal_disease')->dropDownList(\common\components\Helpers\ListHelper::getLists('fatal_disease'), [ 'class'=>'form-control form-control-sm','prompt' => 'Select Fatal Disease'])->label("Fatal Disease") ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
