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
        'action' => ['composite-donation-index'],
        'method' => 'get',
    ]); ?>
    <div class="row">

        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'sanction_no')->textInput(['placeholder'=>'Sanction No'])->label('Sanction No');
            ?>
        </div>
        <div class="col-sm-2">
            <?php
            echo $form->field($model, 'receipt_no')->textInput(['placeholder'=>'Receipt No'])->label('Receipt No');
            ?>
        </div>
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
