<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\DisbursementRejectedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="disbursement-rejected-search container-fluid collapse border-0" id="demo">

    <?php $form = ActiveForm::begin([
        'action' => ['reject-loan'],
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'disbursement_detail_id') ?>

    <?= $form->field($model, 'reject_reason') ?>

    <?= $form->field($model, 'deposit_slip_no') ?>

    <?= $form->field($model, 'deposit_date') ?>

    <?php // echo $form->field($model, 'deposit_bank') ?>

    <?php // echo $form->field($model, 'deposit_amount') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'is_verified') ?>

    <?php // echo $form->field($model, 'verified_by') ?>

    <?php // echo $form->field($model, 'verfied_at') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>