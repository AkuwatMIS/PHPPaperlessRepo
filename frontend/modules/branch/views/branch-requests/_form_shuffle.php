<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="branch-requests-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <div class="row">
        <div class="col-sm-12">
           <?php $branches = ArrayHelper::map(\common\models\Branches::find()->asArray()->all(), 'id', 'name');?>

            <!--<?/*= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Branch Name') */?>-->
            <?= $form->field($model, 'branch_id')->widget(Select2::classname(), [
                'data' => $branches,
                'options' => ['placeholder' => 'Select Branch'],
                'size' => Select2::SMALL,
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Branch');?>
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
            <?= $form->field($model, 'action')->hiddenInput(['value'=>'branch_shuffle'])->label(false) ?>
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
