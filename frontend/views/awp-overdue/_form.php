<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AwpOverdue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-overdue-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'branch_id')->textInput() ?>

    <?= $form->field($model, 'month')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_of_opening')->textInput() ?>

    <?= $form->field($model, 'overdue_numbers')->textInput() ?>

    <?= $form->field($model, 'overdue_amount')->textInput() ?>

    <?= $form->field($model, 'awp_active_loans')->textInput() ?>

    <?= $form->field($model, 'awp_olp')->textInput() ?>

    <?= $form->field($model, 'active_loans')->textInput() ?>

    <?= $form->field($model, 'olp')->textInput() ?>

    <?= $form->field($model, 'diff_active_loans')->textInput() ?>

    <?= $form->field($model, 'diff_olp')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
