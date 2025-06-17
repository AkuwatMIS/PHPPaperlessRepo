<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RandomMembers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="random-members-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cnic')->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '99999-9999999-9',
    ])->textInput(['maxlength' => true, 'placeholder' => 'CNIC', 'class' => 'form-control form-control-sm']) ?>

    <?= $form->field($model, 'province_id')->dropDownList($array['provinces'],['prompt'=>'Select Province'])->label('Province') ?>

    <?= $form->field($model, 'city_id')->dropDownList($array['cities'],['prompt'=>'Select City'])->label('City') ?>
  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
