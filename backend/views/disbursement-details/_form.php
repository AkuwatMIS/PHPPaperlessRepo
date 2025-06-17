<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DisbursementDetails */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="disbursement-details-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tranche_id')->textInput() ?>

    <?= $form->field($model, 'bank_name')->dropDownList(\common\components\Helpers\MemberHelper::getBankAccountsAll()) ?>

    <?= $form->field($model, 'account_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transferred_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'disbursement_id')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([0,1,2,3,4,5,6]) ?>

    <!--<?/*= $form->field($model, 'response_code')->textInput() */?>-->

    <?= $form->field($model, 'response_description')->textarea(['rows' => 6]) ?>
  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
