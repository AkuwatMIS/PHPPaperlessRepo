<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AwpLoanManagementCost */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-loan-management-cost-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'branch_id')->textInput() ?>

    <?= $form->field($model, 'area_id')->textInput() ?>

    <?= $form->field($model, 'region_id')->textInput() ?>

    <?= $form->field($model, 'date_of_opening')->textInput() ?>

    <?= $form->field($model, 'opening_active_loans')->textInput() ?>

    <?= $form->field($model, 'closing_active_loans')->textInput() ?>

    <?= $form->field($model, 'average')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'lmc')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
