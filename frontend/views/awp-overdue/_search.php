<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AwpOverdueSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-overdue-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'branch_id') ?>

    <?= $form->field($model, 'area_id') ?>

    <?= $form->field($model, 'region_id') ?>

    <?= $form->field($model, 'month') ?>

    <?php // echo $form->field($model, 'date_of_opening') ?>

    <?php // echo $form->field($model, 'overdue_numbers') ?>

    <?php // echo $form->field($model, 'overdue_amount') ?>

    <?php // echo $form->field($model, 'awp_active_loans') ?>

    <?php // echo $form->field($model, 'awp_olp') ?>

    <?php // echo $form->field($model, 'active_loans') ?>

    <?php // echo $form->field($model, 'olp') ?>

    <?php // echo $form->field($model, 'diff_active_loans') ?>

    <?php // echo $form->field($model, 'diff_olp') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
