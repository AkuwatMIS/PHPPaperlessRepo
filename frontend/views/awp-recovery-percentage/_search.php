<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AwpRecoveryPercentageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-recovery-percentage-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'month') ?>

    <?= $form->field($model, 'branch_id') ?>

    <?= $form->field($model, 'area_id') ?>

    <?= $form->field($model, 'region_id') ?>

    <?php // echo $form->field($model, 'branch_code') ?>

    <?php // echo $form->field($model, 'recovery_count') ?>

    <?php // echo $form->field($model, 'recovery_one_to_ten') ?>

    <?php // echo $form->field($model, 'recovery_eleven_to_twenty') ?>

    <?php // echo $form->field($model, 'recovery_twentyone_to_thirty') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
