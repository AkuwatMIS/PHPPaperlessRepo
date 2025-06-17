<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
?>


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
<div class="row">
   <div class="col-sm-2">
    <?= $form->field($model, 'region_id')->dropDownList($regions, ['class' => 'form-control input-sm','prompt' => 'All Regions'])->label('Regions');?>
    </div>
    <div class="col-sm-2">
        <?php
        /*print_r($model->loan->area->name);
        die();*/
        $value = !empty($model->area_id)?$model->area_id:null;
        echo $form->field($model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
            'options' => ['class' => 'form-control input-sm'],
            'pluginOptions'=>[
                'depends'=>['awpsearch-region_id'],
                'initialize' => true,
                'initDepends'=>['awpsearch-region_id'],
                'placeholder'=>'All Areas',
                'url'=>\yii\helpers\Url::to(['/structure/fetch-area-by-region'])
            ],
            'data' => $value?[$model->area_id => $value]:[]
        ] )->label('Area');
        ?>
       <!-- <?/*= $form->field($model, 'area_id')->dropDownList($areas, ['class' => 'form-control','prompt' => 'All Areas'])->label('Areas');*/?>-->
    </div>
   <!-- <div class="col-sm-3">
        <?/*= $form->field($model, 'branch_id')->dropDownList($branches, ['class' => 'form-control','prompt' => 'All Branches'])->label('Branches');*/?>
    </div>-->

    <div class="col-sm-2">
        <?= $form->field($model, 'project_id')->dropDownList($projects, ['class' => 'form-control input-sm','prompt' => 'All Projects'])->label('Projects');?>
    </div>
    <div class="col-sm-2">
       <?= $form->field($model, 'month')->textInput(['placeholder'=>'Select Month', 'class'=>'form-control input-sm'])->dropDownList(common\components\Helpers\AwpHelper::getMonths()) ?>
       <!-- <?php
/*        echo $form->field($model, 'month')->widget(\yii\jui\DatePicker::className(), [
            'name' => 'month',
            'options' => ['placeholder' => 'Month','class'=>'form-control input-sm',
                'format' => 'yyyy-mm',
            ],
            'dateFormat'=>'yyyy-MM',
           //'options' => ['class'=>'form-control', 'placeholder' => 'Deposite date','format' => 'yyyy-mm',],

            //'type' => \kartik\date\DatePicker::TYPE_INPUT,
        ]);
        */?>-->
    </div>
    <div class="col-sm-2">
        <div class="form-group pull-right" style="margin-top: 20px">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary',]) ?>
        </div>
    </div>
</div>
    <?php ActiveForm::end(); ?>
