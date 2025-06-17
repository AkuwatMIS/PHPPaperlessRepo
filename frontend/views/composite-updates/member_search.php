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
                    <h4>Member Search</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="member-update">
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
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <?php $form = ActiveForm::begin(); ?>
                    <?php
                    $url = \yii\helpers\Url::to(['/applications/search-member']);
                    if (!empty($model->member_id)) {
                        $member = \common\models\Members::findOne($model->member_id);
                        $cityDesc = '<strong>Name</strong>: ' . $member->full_name . ' <strong>CNIC</strong>: ' . $member->cnic;
                    } else {
                        $cityDesc = '';
                    }
                    ?>

                    <?= $form->field($model, "member_id")->widget(Select2::classname(), [
                        'initValueText' => $cityDesc, // set the initial display text
                        'options' => ['placeholder' => 'Search Member CNIC(XXXXX-XXXXXXX-X)', 'class' => 'file'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 15,
                            'maximumInputLength' => 15,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => $url,
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(city) { return city.text; }'),
                            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                            'disabled' => empty($model->member_id) ? false : true,
                        ],
                    ])->label('Select Member');

                    ?>
                    <?php if (!Yii::$app->request->isAjax) { ?>
                        <?= Html::submitButton('Search', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'member-submit']) ?>
                    <?php } ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
