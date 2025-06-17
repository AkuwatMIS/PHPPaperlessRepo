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
/* @var $form yii\widgets\ActiveForm */
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-sm-12">
        <div class="col-sm-12">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <?php if (Yii::$app->session->hasFlash('success')): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-check"></i>Saved!</h4>
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                <?php endif; ?>
                <?php if (Yii::$app->session->hasFlash('error')): ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-check"></i>Saved!</h4>
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-2"></div>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2>Import PMT Data</h2>

                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                    <div class="form-group col-md-12">
                        <div class="col-md-5">
                            <label for="exampleInputEmail1">Cnic File</label>
                            <input type="file" class="form-control" id="cnic_file" name="cnic_file"
                                   aria-describedby="emailHelp" placeholder="select members data">
                        </div>
                        <div class="col-md-8" style="display: none">
                            <?php
                            echo $form->field($model, 'created_at')->widget(DateRangePicker::classname(), [

                            ]);
                            ?>
                        </div>
                    </div>

                    <div class="row pull-right">
                        <div class=" col-sm-12 form-group">
                            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary pull-right']) ?>

                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
