<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Schedules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="schedules-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'due_date')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class' => 'form-control', 'placeholder' => 'Report Date']
    ]) ?>

    <?= $form->field($model, 'schdl_amnt')->textInput(['maxlength' => true])->label('Schedule Amount') ?>
    <?= $form->field($model, 'charges_schdl_amount')->textInput(['maxlength' => true])->label('Charges Schedule Amount') ?>
    <?= $form->field($model, 'loan_id')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'due_amnt')->textInput(['maxlength' => true])->label('Due Amount') ?>
    <?= $form->field($model, 'charges_due_amount')->textInput(['maxlength' => true])->label('Charges Due Amount') ?>


	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>

</div>
