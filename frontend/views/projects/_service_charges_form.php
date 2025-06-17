<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectCharges */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-charges-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'received_amount')->textInput(['maxlength' => true])->label('Total Receivable') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'remaining_amount')->textInput(['maxlength' => true])->label('Total Received') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'pending_amount')->textInput(['maxlength' => true])->label('Total Pending') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, "received_date")->widget(\yii\jui\DatePicker::className(),[
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Last Receiving Date',
                    //'readonly' => 'readonly'

                ],
            ])->label('Last Receiving Date')  ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, "request_date")->widget(\yii\jui\DatePicker::className(),[
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Last Request Sent on',
                    //'readonly' => 'readonly'

                ],
            ])->label('Last Request Sent on')  ?>
        </div>
    </div>
    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
