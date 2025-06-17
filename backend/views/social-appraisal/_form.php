<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SocialAppraisalCopy */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="social-appraisal-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    <?/*= $form->field($model, 'application_id')->textInput() */?>-->
    <?= $form->field($model, 'house_ownership')->textInput(['maxlength' => true])->dropDownList(\common\components\Helpers\ListHelper::getLists('house_ownership'), ['prompt' => 'Select House Ownership']) ?>

    <!--<?/*= $form->field($model, 'total_family_members')->textInput() */?>-->

    <?= $form->field($model, 'no_of_earning_hands')->textInput()->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_earning_hands'), ['prompt' => 'Select No Of Earning Hands']) ?>

    <?= $form->field($model, 'ladies')->textInput()->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_earning_hands'), ['prompt' => 'Select Ladies']) ?>

    <?= $form->field($model, 'gents')->textInput()->dropDownList(\common\components\Helpers\ListHelper::getLists('no_of_earning_hands'), ['prompt' => 'Select Gents']) ?>

    <?= $form->field($model, 'source_of_income')->textInput(['maxlength' => true])->dropDownList(\common\components\Helpers\ListHelper::getLists('source_of_income'), ['prompt' => 'Select Source Of Income']) ?>

    <?= $form->field($model, 'utility_bills')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'educational_expenses')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'medical_expenses')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kitchen_expenses')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_expenses')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'business_income')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'monthly_savings')->dropDownList(\common\components\Helpers\ListHelper::getLists('monthly_savings')) ?>
    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

   <!--<?/*= $form->field($model, 'total_expenses')->textInput(['maxlength' => true]) */?>-->

    <?= $form->field($model, 'social_behaviour')->textInput(['maxlength' => true])->dropDownList(\common\components\Helpers\ListHelper::getLists('social_behaviour'), ['prompt' => 'Select Social Behaviour']) ?>

    <?= $form->field($model, 'economic_dealings')->textInput(['maxlength' => true])->dropDownList(\common\components\Helpers\ListHelper::getLists('economic_dealings'), ['prompt' => 'Select Economic Dealings']) ?>

    <?= $form->field($model, 'income_before_corona')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'income_after_corona')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'expenses_in_corona')->textInput(['maxlength' => true]) ?>

    <!--<?/*= $form->field($model, 'latitude')->textInput() */?>

    <?/*= $form->field($model, 'longitude')->textInput() */?>-->

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
