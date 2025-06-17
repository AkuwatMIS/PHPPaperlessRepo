<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ConfigRules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="config-rules-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'group')->dropDownList($array['config_groups']) ?>
<!--
    <?/*= $form->field($model, 'priority')->dropDownList($array['config_priority']) */?>

-->    <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_type')->dropDownList($array['config_parent_type']) ?>

    <?= $form->field($model, 'parent_id')->textInput() ?>

    <?= $form->field($model, 'project_id')->dropDownList($array['config_projects'],['prompt' => 'Select Project']) ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
