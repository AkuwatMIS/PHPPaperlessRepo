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
            $value = !empty($model->area_id) ? $model->area->name : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['memberssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['memberssearch-region_id'],
                    'placeholder' => 'Select Area',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->branch_id) ? $model->branch->name : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['memberssearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['memberssearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->team_id) ? $model->team->name : null;
            echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['memberssearch-branch_id'],
                    'initialize' => true,
                    'initDepends' => ['memberssearch-branch_id'],
                    'placeholder' => 'Select Team',
                    'url' => Url::to(['/structure/fetch-team-by-branch'])
                ],
                'data' => $value ? [$model->team_id => $value] : []
            ])->label('Team');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->field_id) ? $model->field->name : null;
            echo $form->field($model, 'field_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['memberssearch-team_id'],
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
            <?= $form->field($model, 'full_name')->label("Full Name")->textInput(['placeholder'=>'Full Name', 'class'=>'form-control form-control-sm']) ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'parentage')->label("Parenatge")->textInput(['placeholder'=>'Parenatge', 'class'=>'form-control form-control-sm']) ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'parentage_type')->label("Parentage Type")->textInput(['placeholder'=>'Parentage Type', 'class'=>'form-control form-control-sm']) ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->label("CNIC")->textInput(['placeholder'=>'CNIC', 'class'=>'form-control form-control-sm']) ?>
        </div>

        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'dob')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Date of Birth'],
                'pluginOptions'=>[
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ])->label("Date of Birth");
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'religion')->widget(Select2::classname(), [
                'data' => array_merge(["" => ""], \common\components\Helpers\MemberHelper::getReligions()),
                'options' => ['placeholder' => 'Select Religion'],
                'size' => Select2::SMALL,
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'gender')->widget(Select2::classname(), [
                'data' => array_merge(["" => ""], \common\components\Helpers\MemberHelper::getGender()),
                'options' => ['placeholder' => 'Select Gender'],
                'size' => Select2::SMALL,
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'education')->widget(Select2::classname(), [
                'data' => array_merge(["" => ""],  \common\components\Helpers\MemberHelper::getEducation()),
                'options' => ['placeholder' => 'Select Education'],
                'size' => Select2::SMALL,
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'marital_status')->widget(Select2::classname(), [
                'data' => array_merge(["" => ""],  \common\components\Helpers\MemberHelper::getMaritalStatus()),
                'options' => ['placeholder' => 'Select Marital Status'],
                'size' => Select2::SMALL,
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'status')->dropDownList(\common\components\Helpers\MemberHelper::getMemberStatus(), [ 'class'=>'form-control form-control-sm','prompt' => 'Select Status'])->label("Status") ?>
        </div>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
