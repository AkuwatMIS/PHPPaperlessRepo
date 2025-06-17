<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$model->type = 'email';
?>
<br>
<div class="page-center">
    <div class="page-center-in">
        <div class="container-fluid">
            <?php $form = ActiveForm::begin([
                'options' => [
                    'class' => 'sign-box'
                ]
            ]); ?>
            <div class="sign-avatar">
                <?php echo Html::img('@web/images/akhuwat-logo.png', ['alt'=>Yii::$app->name]); ?>
            </div>
            <header class="sign-title">Sign In</header>

            <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'form-control', 'placeholder'=>'Enter Email'])->label(false) ?>

            <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control', 'placeholder'=>'Enter Password'])->label(false) ?>
            <div class="form-group">
                <div class="checkbox float-left">
                    <?= $form->field($model, 'rememberMe')->checkbox(['id'=>'signed-in'])->label("Remember Me") ?>
                </div>
                <div class="float-right reset">
                    <?= Html::a('Reset Password', ['site/request-password-reset']) ?>
                </div>
            </div>

            <?= Html::submitButton('Login', ['class' => 'btn btn-rounded', 'name' => 'login-button']) ?>


            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div><!--.page-center-->