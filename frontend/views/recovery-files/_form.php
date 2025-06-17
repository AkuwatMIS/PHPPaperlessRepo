<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\models\RecoveryFiles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="recovery-files-form">

    <?php 
        $form = ActiveForm::begin(); 
        $permissions = Yii::$app->session->get('permissions');

        $status=['0'=>'Approval Pending'];

        if(in_array('frontend_approverecoveryfiles', $permissions)){
            $status['1']='Approve';
        }

        if(in_array('frontend_executerecoveryfiles', $permissions) && !$model->isNewRecord && $model->status!='0'){
            $status['2']='Execute';
        }

        if($model->status=='3'){
            $status['3']='In-Process';
        }

        if($model->status=='4'){
            $status=['4' => 'Completed'];
        }

    ?>

    <?= $form->field($model, 'source')->dropDownList(['cih' => 'Cih','branch' => 'Branch', 'bi' => 'Bank Islami', 'hbl' => 'HBL', 'hble' => 'HBLE', 'mcb' => 'MCB', 'nbp' => 'NBP', 'ep' => 'EP', 'akb' => 'Askari Bank','trb' => 'Terabytes','abl'=>'ABL','bop'=>'BOP', 'omni' => 'OMNI', 'WROFF'=>'Write-Off '], ['prompt' => ''])->label('Bank Name') ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'file_date')->widget(DatePicker::className(),[
        'dateFormat' => 'yyyy-MM-dd',
        'options' => ['class' => 'form-control picker', 'placeholder' => 'File Date']
    ]) ?>

    <?php if(!$model->isNewRecord)
    { ?>
        <?= $form->field($model, 'status')->dropDownList($status) ?>
    <?php } ?>

    <?php if($model->isNewRecord)
    { ?>
        <?= $form->field($model, 'file')->fileInput(['maxlength' => true]) ?>
    <?php } ?>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
