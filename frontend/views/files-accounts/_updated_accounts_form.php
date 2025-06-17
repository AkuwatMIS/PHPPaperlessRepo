<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FilesAccounts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="files-accounts-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    $form = ActiveForm::begin();
    $permissions = Yii::$app->session->get('permissions');
    $status=[''=>'Select Status'];
    $permissions = Yii::$app->session->get('permissions');

    if(in_array('frontend_reviewfilesaccounts', $permissions) && $model->status=='0'){
        $status['2']='Review';
    }

    if(in_array('frontend_approvefilesaccounts', $permissions) && $model->status=='2'){
        $status['0']='Pending';
        $status['3']='Approved';
    }
    ?>

    <?php if (!$model->isNewRecord) { ?>
        <?= $form->field($model, 'status')->dropDownList($status) ?>
    <?php } ?>


    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
