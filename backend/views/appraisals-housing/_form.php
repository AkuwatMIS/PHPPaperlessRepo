<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsHousing */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="appraisals-housing-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'application_id')->textInput() ?>

    <?= $form->field($model, 'property_type')->dropDownList(\common\components\Helpers\ListHelper::getLists('property_type')) ?>

    <?= $form->field($model, 'ownership')->textInput(\common\components\Helpers\ListHelper::getLists('ownership')) ?>

    <?= $form->field($model, 'land_area')->textInput() ?>

    <?= $form->field($model, 'residential_area')->textInput() ?>

    <?= $form->field($model, 'living_duration')->textInput() ?>

    <?= $form->field($model, 'duration_type')->dropDownList(\common\components\Helpers\ListHelper::getLists('duration_type')) ?>

    <?= $form->field($model, 'no_of_rooms')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_rooms')) ?>

    <?= $form->field($model, 'no_of_kitchens')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_kitchens')) ?>

    <?= $form->field($model, 'no_of_toilets')->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_toilets')) ?>

    <?= $form->field($model, 'purchase_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'current_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
