<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AwpBranchSustainability */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-branch-sustainability-form">

    <?php $form = ActiveForm::begin(); ?>



    <?= $form->field($model, 'region_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Regions::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'area_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Areas::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'branch_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Branches::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'branch_code')->textInput(['required'=>true]) ?>

    <?= $form->field($model, 'month')->textInput(['required'=>true]) ?>

    <?= $form->field($model, 'amount_disbursed')->textInput(['required'=>true]) ?>

    <?= $form->field($model, 'percentage')->textInput(['required'=>true]) ?>

    <?= $form->field($model, 'income')->textInput(['required'=>true]) ?>

    <?= $form->field($model, 'actual_expense')->textInput(['required'=>true]) ?>

    <?= $form->field($model, 'surplus_deficit')->textInput(['required'=>true]) ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
