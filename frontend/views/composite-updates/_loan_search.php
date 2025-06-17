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

<div class="container-fluid  border-0" id="demo">

    <?php $form = ActiveForm::begin([
        'action' => ['composite-loan-search'],
        'method' => 'get',
    ]); ?>
    <div class="row" style="margin-top: 1em!important;">
        <div class="col-sm-3">
            <?= $form->field($model, 'sanction_no')->label("Sanction No")->textInput(['placeholder' => 'Sanction No', 'class' => 'form-control form-control-sm'])->label(false) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'application_no')->label("Application No")->textInput(['placeholder' => 'Application No', 'class' => 'form-control form-control-sm'])->label(false) ?>
        </div>
        <div class="col-sm-1 form-group" style="text-align: left">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
