<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsAgriculture */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="appraisals-agriculture-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'water_analysis')->dropDownList([0=>'No',1=>'Yes']) ?>

    <?= $form->field($model, 'soil_analysis')->dropDownList([0=>'No',1=>'Yes']) ?>

    <?= $form->field($model, 'laser_level')->dropDownList([0=>'No',1=>'Yes']) ?>

    <?= $form->field($model, 'irrigation_source')->dropDownList(\common\components\Helpers\ListHelper::getLists('irrigation_source')) ?>

    <!--<?/*= $form->field($model, 'other_source')->textInput(['maxlength' => true]) */?>-->

    <?= $form->field($model, 'crop_year')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'crop_production')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'resources')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'expenses')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'available_resources')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'required_resources')->textInput(['maxlength' => true]) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
