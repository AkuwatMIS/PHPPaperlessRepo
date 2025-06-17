<?php
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

$this->title = 'Paperless Admin Portal';
?>

<div class="container">
    <div class="jumbotron">
        <h2 style="margin-top: 12%">Member Update</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
               <p>Member Update</p>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="form-group">
                    <label for="exampleInputEmail1">CNIC</label>
                    <input type="file" class="form-control" id="cnics" name="cnics" aria-describedby="emailHelp" placeholder="select sanction no">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
