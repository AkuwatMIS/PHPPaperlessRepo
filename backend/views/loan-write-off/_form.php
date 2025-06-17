<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LoanWriteOff */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loan-write-off-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'loan_id')->textInput() ?>

    <?= $form->field($model, 'recovery_id')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'cheque_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'voucher_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bank_name')->dropDownList(\common\components\Helpers\ListHelper::getLists('bank_accounts')/*['hbl'=>'HBL','mcb'=>'MCB','national_bank'=>'National Bank']*/,['prompt' => 'Select Bank Name', 'class' => 'form-control form-control-sm'])->label('Bank Name'); ?>

    <?= $form->field($model, 'bank_account_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList([0=>'Recovery',1=>'Funeral Charges'],['prompt'=>'Select Type']) ?>

    <?= $form->field($model, 'reason')->dropDownList(['disable'=>'Permanently Disable','death'=>'Death'],['prompt'=>'Select Reason']) ?>

    <?= $form->field($model, 'deposit_slip_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'borrower_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'borrower_cnic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'who_will_work')->dropDownList(\common\components\Helpers\ApplicationHelper::getWhoWillWork(), ['prompt' => 'Select Relation with borrower', 'class' => 'form-control form-control-sm'])->label('Who Will Work')?>

    <?= $form->field($model, 'other_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_cnic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, "write_off_date")->widget(\yii\jui\DatePicker::className(), [
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Write Off Date']
    ]); ?>

    <?= $form->field($model, 'status')->dropDownList(['0'=>'Pending','1'=>'Approved','2'=>'Reject'],['prompt'=>'Select Status']) ?>



  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
