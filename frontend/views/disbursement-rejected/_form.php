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

            <?= $form->field($model, 'deposit_slip_no')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'deposit_bank')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'file')->fileInput(['maxlength' => true]) ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, "deposit_date")->widget(\yii\jui\DatePicker::className(),[
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control', 'placeholder' => 'Deposit Date',
                ],
                'clientOptions'=>[
                    'changeMonth' => true,
                    'changeYear' => true,
                ]
            ])->label('Deposit Date');  ?>

            <?= $form->field($model, 'deposit_amount')->textInput() ?>
        </div>
    </div>
    <input id="disbursement_detail_id" name="disbursement_detail_id" value="<?=$disbursement_detail_id?>" type="hidden">
    <input id="project_id" name="project_id" value="<?=$project_id?>" type="hidden">

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>

</div>
