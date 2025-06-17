<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LoansDisbursement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loans-disbursement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'loan_id')->textInput() ?>

    <?= $form->field($model, 'tranche_id')->textInput() ?>

    <?= $form->field($model, 'payment_method_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\PaymentMethods::find()->select(["id",'CONCAT(name, "(",type,")") as name'])->all(),'id','name')) ?>

    <!--<?/*= $form->field($model, 'created_by')->textInput() */?>

    <?/*= $form->field($model, 'created_at')->textInput() */?>

   <?/*= $form->field($model, 'updated_at')->textInput() */?> -->

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
