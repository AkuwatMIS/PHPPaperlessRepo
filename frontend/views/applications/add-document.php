<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AuditExecutionFiles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container-fluid">
    <div class="box-typical box-typical-padding">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_id')->hiddenInput(['id'=>'upload','value'=>$application->id])->label(false) ?>
    <?= $form->field($model, 'parent_type')->hiddenInput(['value'=>'applications'])->label(false) ?>
    <?= $form->field($model, 'image_type')->dropDownList($documents,['prompt'=>'Select Type'])->label('Type') ?>
        <br>
    <?= $form->field($model, 'image_data')->fileInput([/*'style' => 'margin-left:30%'*/])->label(false) ?>
        <br>

        <?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Upload' : 'Upload', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
</div>
