<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AwpLoansUmSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-loans-um-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'region_id') ?>

    <?= $form->field($model, 'area_id') ?>

    <?= $form->field($model, 'branch_id') ?>

    <?= $form->field($model, 'active_loans') ?>

    <?php // echo $form->field($model, 'no_of_um') ?>

    <?php // echo $form->field($model, 'active_loans_per_um') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
