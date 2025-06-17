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
<div class="container-fluid">

    <?php $form = ActiveForm::begin([
        'action' => ['visit-images'],
        'method' => 'get',
    ]);
    ?>
    <section>
    <div class="row">

        <div class="col-sm-3">
            <?= $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region'); ?>
        </div>

        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['applicationssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['applicationssearch-region_id'],
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
                    'depends' => ['applicationssearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'image_status')->dropDownList($images_status, ['prompt' => 'Select Image Status'])->label('Image Status'); ?>
        </div>
    </div>
        <div class="row">
            <div class="col-sm-3">
                <?= $form->field($model, 'disb_status')->dropDownList($disb_status, ['prompt' => 'Select Disbursed Status'])->label('Disbursed Status'); ?>
            </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'visit_count')->dropDownList($visitCount, ['prompt' => 'Select Visit Count'])->label('Visit Count'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'image_count')->dropDownList($visitCount, ['prompt' => 'Select Image Count'])->label('Image Count'); ?>
        </div>
    </div>

    <div class="row pull-right">
        <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        </div></div>
    <?php ActiveForm::end(); ?>
    </section>
</div>
