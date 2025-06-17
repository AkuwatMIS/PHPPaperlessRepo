<?php
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

$this->title = 'Paperless Admin Portal';
?>

<div class="container">
    <div class="jumbotron">
        <h2 style="margin-top: 12%">Please use following columns names to create new tranche</h2>
        <b>sanction no</b><br>
        <b>tranch no</b><br>
        <b>tranch amount</b>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
               <p>Add Tranche</p>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="form-group">
                    <label for="exampleInputEmail1">Sanctions</label>
                    <input type="file" class="form-control" id="sanctions" name="sanctions_file" aria-describedby="emailHelp" placeholder="select sanction no">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
