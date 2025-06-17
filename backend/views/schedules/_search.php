<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Schedules */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid collapse border-0" id="demo">
    <div class="schedules-form">

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>
        <div class="form-group">
        <?= $form->field($model, 'loan_id')->textInput(['maxlength' => true,'placeholder'=>'Loan ID',]) ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'sanction_no')->textInput(['maxlength' => true,'placeholder'=>'Loan ID',]) ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>

