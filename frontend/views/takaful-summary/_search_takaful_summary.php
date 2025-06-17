<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="container-fluid border-0">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'post',
    ]); ?>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'All Regions'])->label('Region');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['arcaccountreportdetailssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['arcaccountreportdetailssearch-region_id'],
                    'placeholder' => 'All Areas',
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
                    'depends' => ['arcaccountreportdetailssearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['arcaccountreportdetailssearch-area_id'],
                    'placeholder' => 'All Branches',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'project_ids')->widget(Select2::classname(), [
                'data' => $projects,
                'options' => ['placeholder' => 'Select Project'],
                //'size' => Select2::SMALL,
                'pluginOptions' => [
                    'multiple' => true,
                    'allowClear' => true
                ],
            ])->label("Project");
            ?>
        </div>

        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'from_date')->dropDownList(common\components\Helpers\AccountsReportHelper::getMonths(),['prompt'=>''])->label("Month From");

            ?>
        </div>
        <div class="col-sm-2">
            <?php
              echo $form->field($model, 'to_date')->dropDownList(common\components\Helpers\AccountsReportHelper::getMonths(), ['prompt' => ''])->label("Month To");
            ?>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-3 pull-left">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
