<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AwpRecoveryPercentage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-recovery-percentage-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'month')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'branch_id')->textInput() ?>

    <?= $form->field($model, 'area_id')->textInput() ?>

    <?= $form->field($model, 'region_id')->textInput() ?>

    <?= $form->field($model, 'branch_code')->textInput() ?>

    <?= $form->field($model, 'recovery_count')->textInput() ?>

    <?= $form->field($model, 'recovery_one_to_ten')->textInput() ?>

    <?= $form->field($model, 'recovery_eleven_to_twenty')->textInput() ?>

    <?= $form->field($model, 'recovery_twentyone_to_thirty')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
