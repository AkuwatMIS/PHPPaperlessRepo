<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Products */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="products-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($array['model_productwithactivity'], 'activity_ids')->widget(\kartik\select2\Select2::classname(), [
        'data' => $array['activities'],
        'options' => ['placeholder' => 'Select Activities', 'multiple' => true,],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,

        ],
    ])->label("Activities"); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inst_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'min')->textInput() ?>

    <?= $form->field($model, 'max')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>
<!--
    <?/*= $form->field($model, 'assigned_to')->textInput() */?>

    <?/*= $form->field($model, 'created_by')->textInput() */?>-->
<!--
    <?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>

  -->
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
