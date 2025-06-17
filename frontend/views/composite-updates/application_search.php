<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $members common\models\Members */

/* @var $form yii\widgets\ActiveForm */

use kartik\depdrop\DepDrop;
use yii\helpers\Url;

use common\components\Helpers\StructureHelper;
use yii\helpers\ArrayHelper;

use kartik\widgets\Select2;
use yii\web\JsExpression;

?>

<style>
    .select2-container--krajee-bs3 .select2-results__option--highlighted[aria-selected] {
        background-color: #337ab7!important;
        color: #fff;
    }
</style>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Application Search</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="application-update">
            <div class="row">
                <div class="col-md-12">
                    <?php if (Yii::$app->session->hasFlash('warning')): ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-check"></i>Failure!</h4>
                            <?= Yii::$app->session->getFlash('warning') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <?= $form->field($model, 'cnic')->textInput(['required'=>true , 'maxlength' => true, 'placeholder' => 'Enter Member CNIC with dashes']) ?>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <?= $form->field($model, 'application_no')->textInput(['required'=>true , 'maxlength' => true, 'type' => 'number', 'min' => '0', 'placeholder' => 'Enter Application Number']) ?>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 mt-3">
                        <?php if (!Yii::$app->request->isAjax) { ?>
                            <?= Html::submitButton('Search', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'member-submit']) ?>
                        <?php } ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <?php if(!empty($dataProvider)) {?>
                            <div class="table-responsive">
                                <?= \yii\grid\GridView::widget([
                                    'dataProvider' => $dataProvider,
                                    'columns' => require(__DIR__ . '/_application_columns.php'),
                                    'summary' => '',
                                    'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                                ]); ?>

                            </div>
                        <?php } else{ ?>
                            <div class="table-responsive">
                                <hr>
                                <h3>Search Applications through above filters!</h3>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
