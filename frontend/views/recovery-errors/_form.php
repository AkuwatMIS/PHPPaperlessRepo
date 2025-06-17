<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\models\RecoveryErrors */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="recovery-errors-form">

    <?php $form = ActiveForm::begin(); ?>

   <?= $form->field($model, 'sanction_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'recv_date')->widget(DatePicker::className(),[
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class' => 'form-control picker', 'placeholder' => 'Recv Date']
    ]) ?>

    <?= $form->field($model, 'credit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'balance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'error_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comments')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(['0' => 'Open', '2' => 'Resolved'], ['prompt' => '']) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
