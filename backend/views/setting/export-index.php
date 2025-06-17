<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
//use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Paperless Admin Portal';
?>


<div class="container">
    <div class="jumbotron">
        <h2 style="margin-top: 5%">Export Active Loans</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <p>Export Active Loans</p>
            </div>
            <div class="panel-body">

                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="col-md-6">
                    <label for="exampleInputEmail1">Filter</label>
                </div>
                <div class="form-group col-md-12">
                    <div class="col-md-3">
                        <?php
                        echo $form->field($model, 'report_date')->widget(DatePicker::className(), [
                            'name' => 'report_date',
                            'options' => ['placeholder' => 'Report Date'],
                            'type' => \kartik\date\DatePicker::TYPE_INPUT,

                            'pluginOptions' => [
                                'format' => 'yyyy-mm',
                            ]]);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <label for="regions"> Select region list</label>
                        <input type="file" class="form-control" id="regions" name="regions" aria-describedby="regionHelp" value="select regions">
                    </div>
                    <div class="col-md-3">
                        <label for="areas"> Select Area List</label>
                        <input type="file" class="form-control" id="areas" name="areas" aria-describedby="areaHelp" value="select areas">
                    </div>
                    <div class="col-md-3">
                        <label for="branches"> Select Branches</label>
                        <input type="file" class="form-control" id="branches" name="branches" aria-describedby="branchesHelp" value="select branches">
                    </div>
                </div>

                <div class="row pull-right">
                    <div class=" col-sm-12 form-group">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
