<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AwpTargetVsAchievement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-target-vs-achievement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'region_id')->textInput() ?>

    <?= $form->field($model, 'area_id')->textInput() ?>

    <?= $form->field($model, 'branch_id')->textInput() ?>

    <?= $form->field($model, 'project_id')->textInput() ?>

    <?= $form->field($model, 'month')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'target_loans')->textInput() ?>

    <?= $form->field($model, 'target_amount')->textInput() ?>

    <?= $form->field($model, 'achieved_loans')->textInput() ?>

    <?= $form->field($model, 'achieved_amount')->textInput() ?>

    <?= $form->field($model, 'loans_dif')->textInput() ?>

    <?= $form->field($model, 'amount_dif')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
