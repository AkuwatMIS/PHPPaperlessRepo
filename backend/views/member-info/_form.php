<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MemberInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="member-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'member_id')->textInput() ?>
    <?= $form->field($model, 'cnic_issue_date')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'CNIC Issue Date']
    ])  ?>
    <?= $form->field($model, 'cnic_expiry_date')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'CNIC Expiry Date']
    ])  ?>


    <?= $form->field($model, 'mother_name')->textInput(['maxlength' => true]) ?>



  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
