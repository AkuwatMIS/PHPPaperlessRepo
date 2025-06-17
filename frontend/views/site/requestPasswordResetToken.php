<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Request password reset';
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<br>
<br>
<div class="page-center">
    <div class="page-center-in">
        <div class="container-fluid">
            <?php $form = ActiveForm::begin([
                'id' => 'request-password-reset-form',
                'options' => [
                    'class' => 'sign-box'
                ]
            ]); ?>
            <div class="sign-avatar">
                <?php echo Html::img('@web/images/akhuwat-logo.png', ['alt' => Yii::$app->name]); ?>
            </div>
            <header class="sign-title"><?= Html::encode($this->title) ?></header>
            <p>Please enter your email address.</p>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder'=>'Enter Email Address'])->label(false) ?>
            <?= Html::submitButton('Send', ['class' => 'btn btn-rounded']) ?>


            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
