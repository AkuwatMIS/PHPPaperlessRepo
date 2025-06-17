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
        //'action' => ['result-report'],
        'action' => ['dashboard'],
        'method' => 'get',
    ]);
    ?>
    <section>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'project_id')->dropDownList($result_projects, ['prompt' => 'Select Project'])->label('Project'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'region_id')->dropDownList($result_regions, ['prompt' => 'Select Region'])->label('Region'); ?>
        </div>

        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ;//? $model->area->id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['housingreportssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['housingreportssearch-region_id'],
                    'placeholder' => 'Select Area',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            $value = !empty($model->branch_id) ;//? $model->branch->id : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['housingreportssearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
    </div>

    <div class="row pull-right">
        <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        </div></div>
    <?php ActiveForm::end(); ?>
    </section>
</div>
