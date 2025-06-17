<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\models\DynamicReports */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid">

    <div class="box-typical box-typical-padding">
        <div class="dynamic-reports-form">

            <?php $form = ActiveForm::begin(['method'=>'post']); ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'report_defination_id')->dropDownList($reports_list,['required'=>true])->label('Report') ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    echo $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'All Regions'])->label('Region');
                    ?>
                </div>

                <div class="col-sm-6">
                    <?php
                    /*print_r($model->loan->area->name);
                    die();*/
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

                <div class="col-sm-6">
                    <?=
                    $value = !empty($model->branch_id) ? $model->branch_id : null;
                    echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                        'pluginOptions' => [
                            'depends' => ['arcaccountreportdetailssearch-area_id'],
                            //'initialize' => true,
                            //'initDepends'=>['progressreportdetailssearch-area_id'],
                            'placeholder' => 'All Branches',
                            'url' => Url::to(['/structure/fetch-branch-by-area'])
                        ],
                        'data' => $value ? [$model->branch_id => $value] : []
                    ])->label('Branch');
                    ?>
                </div>
                <div class="col-sm-6">
                    <?php
                    echo $form->field($model, 'project_ids')->widget(\kartik\select2\Select2::classname(), [
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
                <div class="col-sm-3">
                    <?php
                    echo $form->field($model, 'from_date')->dropDownList(common\components\Helpers\AccountsReportHelper::getMonths(), ['prompt' => '','required'=>true])->label("Month From");
                    ?>
                </div>
                <div class="col-sm-3">
                    <?php
                    echo $form->field($model, 'to_date')->dropDownList(common\components\Helpers\AccountsReportHelper::getMonths(), ['prompt' => '','required'=>true])->label("Month To");
                    ?>
                </div>
                <?php if (!Yii::$app->request->isAjax) { ?>
                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                <?php } ?>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
