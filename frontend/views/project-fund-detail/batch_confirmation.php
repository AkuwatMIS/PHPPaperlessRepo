<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectFundDetail */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="project-fund-detail-view">
    <div class="col-md-6">
        <?php $form = \yii\widgets\ActiveForm::begin([ 'action' => ['update-batch'], 'method' => 'post','id'=>'approve-form']); ?>
        <?= $form->field($model, 'batch_no')->hiddenInput()->label( false) ?>
            <div class="form-group text-right">
                <?= Html::submitButton('Approved', ['class' => 'btn btn-success']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="col-md-6">
        <?php $form = \yii\widgets\ActiveForm::begin(['id'=>'reject-form','action' => ['reject-batch']]); ?>
        <?= $form->field($model, 'batch_no')->hiddenInput()->label( false) ?>
            <div class="form-group text-left">
                <?= Html::submitButton('Reject', ['class' => 'btn btn-danger']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
