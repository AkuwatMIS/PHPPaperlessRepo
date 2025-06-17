<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FilesAccounts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="files-accounts-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'file')->fileInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'bank_file')->fileInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'project_id')->dropDownList([''=>'Select Project','132'=>'Apni Chhat Apna Ghar','0'=>'Other'])->label('Project'); ?>
        </div>
    </div>

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
