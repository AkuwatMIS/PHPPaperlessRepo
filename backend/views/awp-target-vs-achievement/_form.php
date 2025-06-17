<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AwpTargetVsAchievement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-target-vs-achievement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'region_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Regions::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'area_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Areas::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'branch_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Branches::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'project_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Projects::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'month')->textInput(['maxlength' => true,'required'=>'required']) ?>

    <?= $form->field($model, 'target_loans')->textInput(['required'=>'required']) ?>

    <?= $form->field($model, 'target_amount')->textInput(['required'=>'required']) ?>

    <?= $form->field($model, 'achieved_loans')->textInput(['required'=>'required']) ?>

    <?= $form->field($model, 'achieved_amount')->textInput(['required'=>'required']) ?>

    <?= $form->field($model, 'loans_dif')->textInput(['required'=>'required']) ?>

    <?= $form->field($model, 'amount_dif')->textInput(['required'=>'required']) ?>


  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
