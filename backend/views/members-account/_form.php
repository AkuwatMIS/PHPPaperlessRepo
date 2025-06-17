<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MembersAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="members-account-form">

    <?php $form = ActiveForm::begin(); ?>

    <!--<?/*= $form->field($model, 'member_id')->textInput() */?>-->

        <?php
        $value = !empty($model->account_type) ? $model->account_type : null;
        echo $form->field($model, 'account_type')->dropDownList([
            'bank_accounts' => 'Bank',
            'coc_accounts' => 'COC',
            'cheque_accounts' => 'Cheque'
        ], ['prompt' => 'Select Account Type'])->label('Account Type');?>


        <?php
        $value = !empty($model->bank_name) ? $model->bank_name : null;
        echo $form->field($model, 'bank_name')->widget(\kartik\depdrop\DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['membersaccount-account_type'],
                'initialize' => true,
                'initDepends' => ['membersaccount-account_type'],
                'placeholder' => 'Select Bank Name',
                'url' => \yii\helpers\Url::to(['/structure/fetch-bank-by-type'])
            ],
            'data' => $value ? [$model->bank_name => $value] : []
        ])->label('Bank Name');
        ?>

   <!--  $form->field($model, 'bank_name')->textInput(['maxlength' => true]) -->

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'account_no')->textInput(['maxlength' => true]) ?>

    <!--<?/*= $form->field($model, 'is_current')->textInput() */?>

    <?/*= $form->field($model, 'assigned_to')->textInput() */?>

    <?/*= $form->field($model, 'created_by')->textInput() */?>

    <?/*= $form->field($model, 'updated_by')->textInput() */?>

    <?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>

    <?/*= $form->field($model, 'status')->textInput() */?>

    <?/*= $form->field($model, 'verified_at')->textInput() */?>

    <?/*= $form->field($model, 'verified_by')->textInput() */?>

    <?/*= $form->field($model, 'deleted')->textInput() */?>-->

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
