<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LoanTranches */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loan-tranches-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'loan_id')->textInput(['maxlength' => true])->label('Loan ID') ?>
    <?= $form->field($model, 'fund_request_id')->textInput(['maxlength' => true])->label('Fund Request ID') ?>
    <?= $form->field($model, 'tranch_no')->dropDownList(['1'=>'1','2'=>'2'])->label('Tranch No') ?>
    <?= $form->field($model, 'tranch_amount')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'disbursement_id')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'date_disbursed')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'cheque_no')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'cheque_date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(['0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8']) ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>

</div>
