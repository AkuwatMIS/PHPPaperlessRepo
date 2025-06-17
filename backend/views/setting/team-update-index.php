<?php
use yii\widgets\ActiveForm;
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
               <p>Team Update</p>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="form-group">
                    <div class="col-md-4">
                        <label for="exampleInputEmail1">Sanctions No</label>
                        <input type="file" class="form-control" id="sanctions" name="sanctions" aria-describedby="emailHelp" placeholder="select sanction no">
                    </div>
                    <div class="col-md-4">
                        <label for="exampleInputTeam">Team</label>
                        <select id="exampleInputTeam" class="form-control form-select" aria-label="Default select example" name="team">
                            <option selected>Select Team</option>
                            <option value="Team1">Team 1</option>
                            <option value="Team2">Team 2</option>
                            <option value="Team3">Team 3</option>
                            <option value="Team4">Team 4</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="exampleInputTeam">Field</label>
                        <select id="exampleInputTeam" class="form-control form-select" aria-label="Default select example" name="field">
                            <option selected>Select Field</option>
                            <option value="Field1">Field 1</option>
                            <option value="Field2">Field 2</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
