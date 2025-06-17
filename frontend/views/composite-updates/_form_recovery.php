<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Recoveries */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="recoveries-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, "receive_date")->widget(\yii\jui\DatePicker::className(), [
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Receive Date']
    ])->label('Received Date'); ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true,'type' => 'number']) ?>
    <?= $form->field($model, 'charges_amount')->textInput(['maxlength' => true,'type' => 'number']) ?>

    <?= $form->field($model, 'receipt_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'credit_tax')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project');?>


    <?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
