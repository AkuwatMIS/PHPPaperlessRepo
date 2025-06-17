<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Funds */
/* @var $projects common\models\Projects */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="funds-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Bank Name') ?>

    <?= $form->field($model, 'total_fund')->textInput()->label('Allocated Credit Line') ?>

    <?= $form->field($model, 'project_id')->dropDownList($projects, [ 'prompt' => 'Select Project'])->label("Project") ?>

    <?= $form->field($model, 'email')->textInput()?>


  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
