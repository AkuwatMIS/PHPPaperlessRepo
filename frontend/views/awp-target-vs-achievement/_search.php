<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AwpTargetVsAchievementSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-target-vs-achievement-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'region_id') ?>

    <?= $form->field($model, 'area_id') ?>

    <?= $form->field($model, 'branch_id') ?>

    <?= $form->field($model, 'project_id') ?>

    <?php // echo $form->field($model, 'month') ?>

    <?php // echo $form->field($model, 'target_loans') ?>

    <?php // echo $form->field($model, 'target_amount') ?>

    <?php // echo $form->field($model, 'achieved_loans') ?>

    <?php // echo $form->field($model, 'achieved_amount') ?>

    <?php // echo $form->field($model, 'loans_dif') ?>

    <?php // echo $form->field($model, 'amount_dif') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
