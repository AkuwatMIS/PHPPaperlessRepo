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
        'action' => ['housing'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-2">
            <?php
            //$regions = ArrayHelper::map(Regions::find()->asArray()->all(), 'id', 'name');
            echo $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region');
            ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'project_id')->dropDownList($projects, ['class' => 'form-control input-sm', 'prompt' => 'All Projects'])->label('Projects'); ?>
        </div>
        <div class="col-sm-2">
            <?php
            $value = !empty($model->area_id) ? $model->area->name : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['recoveriessearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['recoveriessearch-region_id'],
                    'placeholder' => 'Select Area',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            $value = !empty($model->branch_id) ? $model->branch->name : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['recoveriessearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['recoveriessearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            $value = !empty($model->team_id) ? $model->team->name : null;
            echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['recoveriessearch-branch_id'],
                    'initialize' => true,
                    'initDepends' => ['recoveriessearch-branch_id'],
                    'placeholder' => 'Select Team',
                    'url' => Url::to(['/structure/fetch-team-by-branch'])
                ],
                'data' => $value ? [$model->team_id => $value] : []
            ])->label('Team');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            $value = !empty($model->field_id) ? $model->field->name : null;
            echo $form->field($model, 'field_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['recoveriessearch-team_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'Select Field',
                    'url' => Url::to(['/structure/fetch-field-by-team'])
                ],
                'data' => $value ? [$model->field_id => $value] : []
            ])->label('Field');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'sanction_no')->textInput(['placeholder'=>'Sanction No'])->label('Sanction No');
            ?>
        </div>
        <div class="col-sm-2">
        <?php
        echo $form->field($model, 'receive_date')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Recovery Date'],
            'readonly' => true,
            'pluginOptions'=>[
                'startDate'      => date("y-m-d"),
                'locale'=>[
                    'format'=>'Y-m-d',
                ],
             'minDate' => (string) date('Y-m-d', strtotime('- 11 month')),
             'maxDate' => (string) date('Y-m-t'),
            ]
        ])->label("Receive Date");
        ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'member_name')->textInput(['placeholder'=>'Member Name'])->label('Member Name');
            ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'member_cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->textInput(['maxlength' => true, 'placeholder' => 'CNIC', 'class' => 'form-control form-control-sm']) ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'receipt_no')->textInput(['placeholder'=>'Receipt No'])->label('Receipt No');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'amount')->textInput(['placeholder'=>'Amount'])->label('Recovery Amount');
            ?>
        </div>
        <!--<div class="col-sm-2">
            <?php
/*            echo $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project');
            */?>
        </div>-->
    </div>

    <div class="row pull-right">
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
