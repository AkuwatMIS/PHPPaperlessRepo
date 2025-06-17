<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DynamicReports */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dynamic-reports-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'report_defination_id')->dropDownList($reports_list)->label('Report') ?>

    <?= $form->field($model, 'filters')->textInput(['maxlength' => true]) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
