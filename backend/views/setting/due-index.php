<?php
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */

$this->title = 'Paperless Admin Portal';
?>

<div class="container">
    <div class="jumbotron">
        <h2 style="margin-top: 12%">Data Managements</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
               <p>Due Loans</p>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label for="exampleInputEmail1">receipt date</label>
                            <input type="date" class="form-control" id="date" name="receipt">
                        </div>
                        <div class="col-md-4">
                            <label for="exampleInputEmail1">due date</label>
                            <input type="date" class="form-control" id="date" name="due_date">
                        </div>
                        <div class="col-md-4">
                            <label for="exampleInputEmail1">disbursed date</label>
                            <input type="date" class="form-control" id="date" name="disb_date">
                        </div>

                        <div class="col-md-6">
                            <label for="exampleInputEmail1">branches</label>
                            <input type="file" class="form-control" id="branches" name="branches" aria-describedby="emailHelp" placeholder="select sanction no">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="/setting/due-cron-job" class="btn btn-info">Run Cron Job</a>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
