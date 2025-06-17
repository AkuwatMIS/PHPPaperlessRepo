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
            echo $form->field($model, 'region_id')->dropDownList($regions_by_id, ['prompt' => 'Select Region'])->label('Region');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['groupssearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['groupssearch-region_id'],
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
                    'depends' => ['groupssearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['groupssearch-area_id'],
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
                    'depends' => ['groupssearch-branch_id'],
                    'initialize' => true,
                    'initDepends' => ['groupssearch-branch_id'],
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
                    'depends' => ['groupssearch-team_id'],
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
            <?= $form->field($model, 'grp_no')->label("Group No")->textInput(['placeholder'=>'Group No', 'class'=>'form-control form-control-sm']) ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'group_name')->label("Group Name")->textInput(['placeholder'=>'Group Name', 'class'=>'form-control form-control-sm']) ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'grp_type')->label("Group Type")->textInput(['placeholder'=>'Group Type', 'class'=>'form-control form-control-sm']) ?>
        </div>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
