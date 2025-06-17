<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FilesAccounts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="files-accounts-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file')->fileInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'type')->hiddenInput(['value' => 1]) ->label(false)?>

    <?php /*$form->field($model, 'file_path')->textInput(['maxlength' => true]) */?><!--

    <?php /*$form->field($model, 'status')->textInput() */?>

    <?php /*$form->field($model, 'total_records')->textInput() */?>

    <?php /*$form->field($model, 'updated_records')->textInput() */?>

    --><?php /*$form->field($model, 'error_description')->textarea(['rows' => 6]) */?>


  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
