<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<br>
<br>
<div class="page-center">
    <div class="page-center-in">
        <div class="container-fluid">
            <?php $form = ActiveForm::begin([
                'id' => 'reset-password-form',
                'options' => [
                    'class' => 'sign-box'
                ]
            ]); ?>
            <div class="sign-avatar">
                <?php echo Html::img('@web/images/akhuwat-logo.png', ['alt' => Yii::$app->name]); ?>
            </div>
            <header class="sign-title"><?= Html::encode($this->title) ?></header>
            <p>Please enter your new password.</p>
            <?= $form->field($model, 'password')->passwordInput(['autofocus' => true, 'placeholder'=>'Enter New Password'])->label(false) ?>
            <?= Html::submitButton('Save', ['class' => 'btn btn-rounded']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div><!--.page-center-->