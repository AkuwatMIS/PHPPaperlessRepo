<?php

/* @var $this yii\web\View */

$this->title = 'Paperless Admin Portal';
?>

<div class="container">
    <div class="jumbotron">
        <h2>Account Update</h2>
    </div>
</div>

<div class="body-content" style="margin-left: 2%">

    <?php $form = \kartik\form\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-4">
            <h3>Single update</h3>
            <div class="col-md-12">
                <input type="text" name="member_name" placeholder="Member Name" class="form-control"  style="margin-top: 10px">
            </div>
            <div class="col-md-12">
                <input type="text" name="member_cnic" placeholder="Member Cnic" class="form-control"  style="margin-top: 10px">
            </div>
            <div class="col-md-12">
                <input type="text" name="member_account" placeholder="Member Account" class="form-control"  style="margin-top: 10px">
            </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <h3>bulk update</h3>
            <div class="col-md-12">
                <input type="file" name="accounts_file" class="form-control"  style="margin-top: 10px">
            </div>
        </div>
        <div class="col-md-12" style="padding: 30px">
            <?= \yii\bootstrap\Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' =>'btn btn-primary form-control']) ?>
        </div>
    </div>

    <?php \kartik\form\ActiveForm::end(); ?>
</div>
