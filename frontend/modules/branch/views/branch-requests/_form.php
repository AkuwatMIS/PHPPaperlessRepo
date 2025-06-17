<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="branch-requests-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Branch Name') ?>
        </div>
    </div>
    <h3>Organization Hierarchy</h3>
    <div class="row">
        <div class="col-sm-4">
            <?php
            $dataCD = ArrayHelper::map(\common\models\CreditDivisions::find()->asArray()->all(), 'id', 'name');
            echo $form->field($model, 'cr_division_id')->dropDownList($dataCD, ['prompt' => 'Select...'])->label('Credit Division');
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            $value = !empty($model->region_id) ? $model->region->name : null;
            echo $form->field($model, 'region_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['branchrequests-cr_division_id'],
//             'initialize' => true,
//             'initDepends'=>['branchrequests-cr_division_id'],
                    'placeholder' => 'Select...',
                    'url' => Url::to(['structure/fetch-regions-by-cr-division'])
                ],
                'data' => $value ? [$model->region_id => $value] : []
            ])->label('Region');
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            $value = !empty($model->area_id) ? $model->area->name : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['branchrequests-region_id'],
                    'initialize' => true,
                    'initDepends' => ['branchrequests-cr_division_id'],
                    'placeholder' => 'Select...',
                    'url' => Url::to(['structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>
    </div>
    <h3>Demographic Hierarchy</h3>
    <div class="row">
        <div class="col-sm-4">
            <?php
            $dataCtry = ArrayHelper::map(\common\models\Countries::find()->asArray()->all(), 'id', 'name');
            echo $form->field($model, 'country_id')->dropDownList($dataCtry, ['prompt' => 'Select...'])->label('Country');
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            $value = !empty($model->province_id) ? $model->province->name : null;
            echo $form->field($model, 'province_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['branchrequests-country_id'],
                    'placeholder' => 'Select...',
                    'url' => Url::to(['structure/fetch-provinces-by-country'])
                ],
                'data' => $value ? [$model->province_id => $value] : []
            ])->label('Province');
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            $value = !empty($model->city_id) ? $model->city->name : null;
            echo $form->field($model, 'city_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['branchrequests-province_id'],
                    'placeholder' => 'Select...',
                    'url' => Url::to(['structure/fetch-cities-by-province'])
                ],
                'data' => $value ? [$model->city_id => $value] : []
            ])->label('City');
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?php
            $value = !empty($model->division_id) ? $model->division->name : null;
            echo $form->field($model, 'division_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['branchrequests-province_id'],
                    'placeholder' => 'Select...',
                    'url' => Url::to(['structure/fetch-divisions-by-province'])
                ],
                'data' => $value ? [$model->division_id => $value] : []
            ])->label('Division');
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            $value = !empty($model->district_id) ? $model->district->name : null;
            echo $form->field($model, 'district_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['branchrequests-division_id'],
                    'placeholder' => 'Select...',
                    'url' => Url::to(['structure/fetch-by-division'])
                ],
                'data' => $value ? [$model->district_id => $value] : []
            ])->label('District');
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            $value = !empty($model->tehsil_id) ? $model->tehsil->name : null;
            echo $form->field($model, 'tehsil_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['branchrequests-district_id'],
                    'initialize' => true,
                    'initDepends' => ['branchrequests-country_id'],
                    'placeholder' => 'Select...',
                    'url' => Url::to(['structure/fetch-tehsils-by-district'])
                ],
                'data' => $value ? [$model->tehsil_id => $value] : []
            ])->label('Tehsil');
            ?>
        </div>
    </div>
    <h3>Other Info</h3>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'type')->dropDownList(['branch' => 'Branch', 'unit' => 'Unit'], ['prompt' => 'Select...']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'uc')->textInput(['maxlength' => true])->label('UCs') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'village')->textInput(['maxlength' => true])->label('Village/Town') ?>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-4">
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'latitude')->textInput(['value'=>0.0]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'longitude')->textInput(['value'=>0.0]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'opening_date')->widget(\yii\jui\DatePicker::className(), [
                'options' => ['class' => 'form-control'],
            ]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'effective_date')->widget(\yii\jui\DatePicker::className(), [
                'options' => ['class' => 'form-control'],
            ]); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'description')->textarea() ?>
        </div>
    </div>

    <?php
    //For hidden fields errors
    if ($model->hasErrors() && !empty($model->getErrors())) {
        ?>
        <div class="form-group has-error">
            <?php
            $errors = implode(" ", array_map(function ($arr) {
                return implode(" ", $arr);
            }, $model->getErrors()));
            echo "<div class='help-block'><h4>More Errors</h4></div>";
            echo "<div class='help-block'>" . $errors . "</div>";
            ?>
        </div>
        <?php
    };
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
