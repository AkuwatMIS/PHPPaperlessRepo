<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AwpBranchSustainabilitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-branch-sustainability-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'branch_id') ?>

    <?= $form->field($model, 'branch_code') ?>

    <?= $form->field($model, 'region_id') ?>

    <?= $form->field($model, 'area_id') ?>

    <?php // echo $form->field($model, 'month') ?>

    <?php // echo $form->field($model, 'amount_disbursed') ?>

    <?php // echo $form->field($model, 'percentage') ?>

    <?php // echo $form->field($model, 'income') ?>

    <?php // echo $form->field($model, 'actual_expense') ?>

    <?php // echo $form->field($model, 'surplus_deficit') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
