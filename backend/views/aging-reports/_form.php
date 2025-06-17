<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AgingReports */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aging-reports-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true])->dropDownList(array("due"=>"Due","overdue"=>"Overdue")) ?>
    <?= $form->field($model, "start_month")->widget(\yii\jui\DatePicker::className(),[
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Start Month']
    ])->label('Start Month'); ?>
    <!--<?/*= $form->field($model, 'start_month')->textInput() */?>

    <?/*= $form->field($model, 'one_month')->textInput() */?>

    <?/*= $form->field($model, 'next_three_months')->textInput() */?>

    <?/*= $form->field($model, 'next_six_months')->textInput() */?>

    <?/*= $form->field($model, 'next_one_year')->textInput() */?>

    <?/*= $form->field($model, 'next_two_year')->textInput() */?>

    <?/*= $form->field($model, 'next_three_year')->textInput() */?>

    <?/*= $form->field($model, 'next_five_year')->textInput() */?>

    <?/*= $form->field($model, 'total')->textInput() */?>

    <?/*= $form->field($model, 'status')->textInput() */?> -->
<!--
    <?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>

  -->
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
