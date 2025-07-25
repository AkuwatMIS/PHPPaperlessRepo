<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
//$limit=($dataProvider->count);

/*$this->title = 'Home';*/
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="row">
    <div class="col-sm-12">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p>Export Nadra Verysis Completed</p>
                </div>
                <div class="panel-body">
                    <?php $form = \kartik\form\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                    <div class="form-group">
                        <label for="exampleInputEmail1">CNIC</label>
                        <input type="file" class="form-control" id="cnic" name="cnic" aria-describedby="emailHelp" placeholder="select sanction no">
                    </div>
                    <br>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    <?php \kartik\form\ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div>
</div>


