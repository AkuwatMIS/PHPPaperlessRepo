<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ApplicationActions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="application-actions-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parent_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'action')->dropDownList($action_list, ['prompt' => '']) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'pre_action')->textInput() ?>

    <?= $form->field($model, 'expiry_date')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'Expiry Date']
    ]) ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
