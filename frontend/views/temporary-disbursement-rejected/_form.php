<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DisbursementRejected */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="disbursement-rejected-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype'=>'multipart/form-data']]); ?>
    <div class="col-md-12">
        <div class="col-md-12">
            <?= $form->field($model, 'reject_reason')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'file')->fileInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'tranche_no')->hiddenInput(['value' => $tranche_no])->label(false) ?>
            <?= $form->field($model, 'disbursement_detail_id')->hiddenInput(['value' => $disbursement_detail_id])->label(false) ?>
        </div>
    </div>

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>

</div>
