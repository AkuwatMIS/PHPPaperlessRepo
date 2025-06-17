<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LoanTranchesActions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loan-tranches-actions-form">

    <?php $form = ActiveForm::begin(); ?>

    <!--<?/*= $form->field($model, 'id')->textInput() */?>-->

    <?= $form->field($model, 'parent_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'action')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'pre_action')->textInput() ?>

    <!--<?/*= $form->field($model, 'expiry_date')->textInput() */?>

    <?/*= $form->field($model, 'created_by')->textInput() */?>

    <?/*= $form->field($model, 'updated_by')->textInput() */?>

    <?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>-->

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
