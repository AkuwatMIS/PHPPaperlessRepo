<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProgressReports */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="progress-reports-form">

    <?php $form = ActiveForm::begin(); ?>



        <?= $form->field($model, "report_date")->widget(\yii\jui\DatePicker::className(),[
            'dateFormat' => 'yyyy-MM-dd',
            'options' => ['class' => 'form-control', 'placeholder' => 'Report Date']
        ])->label(false); ?>

    <?= $form->field($model, 'code')->dropDownList(['all'=>'All reports','recv'=>'Recovery Summary','disb'=>'Disbursement Summary','app_disb'=>'Application Disbursement Report','don'=>'Donation Summary']) ?>

    <?= $form->field($model, 'do_update')->dropDownList([0=>'Inactive',1=>'Active']) ?>

    <?= $form->field($model, 'is_awp')->dropDownList([0=>'Inactive',1=>'Active']) ?>



    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
