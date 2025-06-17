<?php

/* @var $this yii\web\View */

$this->title = 'Paperless Admin Portal';
?>

<div class="container">
    <div class="jumbotron">
        <h2>Add activity and mapping</h2>
    </div>
</div>

<div class="body-content" style="margin-left: 2%">

    <?php $form = \kartik\form\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="col-md-12">
                <h3>Upload Activities</h3>
                <div class="col-md-12">
                    <input type="file" name="activity_file" class="form-control"  style="margin-top: 10px">
                </div>
            </div>
            <div class="col-md-12" style="padding: 30px">
                <?= \yii\bootstrap\Html::submitButton($model->isNewRecord ? 'Submit' : 'Submit', ['class' =>'btn btn-primary form-control']) ?>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>

    <?php \kartik\form\ActiveForm::end(); ?>
</div>
