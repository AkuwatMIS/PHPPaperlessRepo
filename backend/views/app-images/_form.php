<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppImages */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-images-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'path')->fileInput(['maxlength' => true])->label('Upload Image') ?>

    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'type')->dropDownList(['splash' => 'Splash','banner'=>'Banner'],['prompt' => 'Select Image Type'])->label('Image Type') ?>
        </div>
    <?php // $form->field($model, 'path')->textInput(['maxlength' => true]) ?>
<div class="col-sm-4">
    <?= $form->field($model, 'sort_order')->textInput(['min'=>0,'max'=>100000,'type'=>'number','placeholder'=>'Sort Order']) ?>
</div>
        <div class="col-sm-4">
    <?= $form->field($model, 'target')->textInput(['placeholder' => 'Target' ]) ?>
        </div>
    <?php // $form->field($model, 'status')->textInput() ?>


	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>

</div>
