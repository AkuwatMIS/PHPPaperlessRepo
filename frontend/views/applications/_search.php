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
                    'initialize' => true,
                    'initDepends' => ['applicationssearch-area_id'],
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
                    'depends' => ['applicationssearch-branch_id'],
                    'initialize' => true,
                    'initDepends' => ['applicationssearch-branch_id'],
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
                    'depends' => ['applicationssearch-team_id'],
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
            <?= $form->field($model, 'application_no')->label("Application No")->textInput(['placeholder'=>'Application No', 'class'=>'form-control form-control-sm']) ?>
        </div>

        <!--<div class="col-sm-3">
            <?/*= $form->field($model, 'req_amount')->label("Requested Amount")->textInput(['placeholder'=>'Requested Amount', 'class'=>'form-control form-control-sm']) */?>
        </div>-->
        <div class="col-sm-3">
            <?php
            echo   \kartik\field\FieldRange::widget([
                'name1'=>'ApplicationsSearch[loan_amnt_frm]',
                'name2'=>'ApplicationsSearch[loan_amnt_to]',
                'value1'=>isset($model->loan_amnt_frm)?$model->loan_amnt_frm:'',
                'value2'=>isset($model->loan_amnt_to)?$model->loan_amnt_to:'',
                'label'=>'Req Amount',
                // 'form' => $form,
                // 'model' => $model,
                //  'label' => 'Enter amount range',
                // 'attribute1' => 1,
                //'attribute2' => 1000,
                'type' => \kartik\field\FieldRange::INPUT_TEXT,
            ]);

            ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'project_id')->dropDownList($projects, [ 'prompt' => 'Select Project'])->label("Project") ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'status')->dropDownList(common\components\Helpers\ApplicationHelper::getAppStatus(), [ 'class'=>'form-control form-control-sm','prompt' => 'Select Status'])->label("Status") ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'full_name')->label("Full Name")->textInput(['placeholder'=>'Full Name', 'class'=>'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->label("CNIC")->textInput(['placeholder'=>'CNIC', 'class'=>'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'application_date')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Application Date'],
                'pluginOptions'=>[
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ]
                ]
            ])->label("Application Date");
            ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
