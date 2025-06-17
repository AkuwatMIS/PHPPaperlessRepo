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

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>