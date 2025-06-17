<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsBusiness */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="business-appraisal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'place_of_business')->textInput(['maxlength' => true])->dropDownList(\common\components\Helpers\ListHelper::getPlaceOfBusiness(), ['prompt' => 'Select Place Of Business']) ?>


    <?= $form->field($model, 'fixed_business_assets_amount')->textInput() ?>
    <?= $form->field($model, 'running_capital_amount')->textInput() ?>
    <?= $form->field($model, 'business_expenses_amount')->textInput() ?>
    <?= $form->field($model, 'new_required_assets_amount')->textInput() ?>

   	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
