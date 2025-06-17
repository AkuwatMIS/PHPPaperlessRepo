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
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-2">
                <?php
               echo $form->field($model, 'branch_id')->widget(Select2::classname(), [
                   'data' => $branches,
                   'options' => ['placeholder' => 'Select Branch'],
                   'size' => Select2::SMALL,
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label(false);
                ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'Due_Date')->widget(DatePicker::className(), [
                'name' => 'Due_Date',
                'options' => ['placeholder' => 'Report Date'],
                'type' => \kartik\date\DatePicker::TYPE_INPUT,

                'pluginOptions' => [
                    'format' => 'yyyy-mm',
                ]])->label(false);
            ?>
        </div>
        <div class="col-sm-2">
            <?= Html::submitButton('Search Takaf Due', ['class' => 'btn btn-primary pull-left']) ?>
        </div>
    </div>



    <?php ActiveForm::end(); ?>

</div>
