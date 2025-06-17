<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VerifiedAccounts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="verify-accounts-form">

    <?php $form = ActiveForm::begin(['action'=>'verify-account?id='.$model->id]); ?>
            <?= $form->field($model, 'status')->dropdownList(\common\components\Helpers\ListHelper::getVerification(),[/*'prompt'=>'Select'*/])->label('Verify'); ?>


    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>
    
</div>
