<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Referrals */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="referrals-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <div class="col-md-6">
        <?= $form->field($model, 'type')->dropDownList(\common\components\Helpers\ListHelper::getLists('referral_type'),['prompt'=>'Select Type'])->label('Type') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'contact_no')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'status')->dropDownList(array('1'=>'Active','0'=>'Inactive'),['prompt'=>'Select Status'])->label('Status') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'deleted')->dropDownList(array('0'=>'No','1'=>'Yes'),['prompt'=>'Select Deleted'])->label('Deleted') ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    </div>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
