<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
//use yii\jui\DatePicker;

use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container-fluid">

    <?php $form = ActiveForm::begin([
        'action' => ['duelist'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <!--<div class="col-sm-2">
            <?php
/*            echo $form->field($model, 'region_id')->dropDownList($regions, ['class' => 'form-control input-sm', 'prompt' => 'All Regions'])->label('Region');
            */?>
        </div>

        <div class="col-sm-2">
            <?php
/*            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'options' => ['class' => 'form-control input-sm'],
                'pluginOptions' => [
                    'depends' => ['duelistsearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['duelistsearch-region_id'],
                    'placeholder' => 'All Areas',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            */?>
        </div>-->

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
            $value = !empty($model->team_id) ? $model->team_id : null;
            echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['duelistsearch-branch_id'],
                    'initialize' => true,
                    'initDepends' => ['duelistsearch-branch_id'],
                    'placeholder' => 'Select Team',
                    'url' => Url::to(['/structure/fetch-team-by-branch'])
                ],
                'data' => $value ? [$model->team_id => $value] : []
            ])->label('Team');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            /*            echo $form->field($model, 'project_id')->widget(Select2::classname(), [
                            'data' => array_merge(["" => ""], $projects),
                            'options' => ['placeholder' => 'Select Project'],
                            'size' => Select2::SMALL,
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ])->label("Project");*/
            echo $form->field($model, 'project_id')->dropDownList($projects, ['class' => 'form-control input-sm', 'prompt' => 'All Projects'])->label('Projects');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            //  echo '<label>Report Date</label>';
            //      echo DatePicker::widget([
            echo $form->field($model, 'report_date')->widget(DatePicker::className(), [
                'name' => 'report_date',
                // 'value' => date('Y-M'),
                'options' => ['placeholder' => 'Report Date'/*,'value'=>date('Y-m')*/],
                //'options' => ['class'=>'form-control', 'placeholder' => 'Deposite date','format' => 'yyyy-mm',],
                'type' => \kartik\date\DatePicker::TYPE_INPUT,

                'pluginOptions' => [
                    'format' => 'yyyy-mm',
                ]]);
            ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'sanction_no')->label("Sanction No")->textInput(['placeholder' => 'Sanction No', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'name')->label("Member Name")->textInput(['placeholder' => 'Mmeber Name', 'class' => 'form-control input-sm']) ?>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'loan_amount')->label("Loan Amount")->textInput(['placeholder' => 'Loan Amount', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'tranch_amount')->label("Tranch Amount")->textInput(['placeholder' => 'Tranch Amount', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'tranch_no')->label("Tranch No")->textInput(['placeholder' => 'Tranch No', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'team_name')->widget(Select2::classname(), [
                'data' => array_merge(["" => ""], $teams),
                'options' => ['placeholder' => 'Select Team'],
                'size' => Select2::SMALL,
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'grpno')->label("Group No")->textInput(['placeholder' => 'Group No', 'class' => 'form-control input-sm']) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'address')->label("Address")->textInput(['placeholder' => 'Address', 'class' => 'form-control input-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'mobile')->label("Mobile")->textInput(['placeholder' => 'Mobile', 'class' => 'form-control input-sm']) ?>
        </div>


    </div>

    <div class="row pull-right">
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<!--<a href="#demo" class="btn btn-primary" data-toggle="collapse"><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>-->