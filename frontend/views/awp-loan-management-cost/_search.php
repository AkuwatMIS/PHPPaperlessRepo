<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AwpLoanManagementCostSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-loan-management-cost-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'branch_id') ?>

    <?= $form->field($model, 'area_id') ?>

    <?= $form->field($model, 'region_id') ?>

    <?= $form->field($model, 'date_of_opening') ?>

    <?php // echo $form->field($model, 'opening_active_loans') ?>

    <?php // echo $form->field($model, 'closing_active_loans') ?>

    <?php // echo $form->field($model, 'average') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'lmc') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
