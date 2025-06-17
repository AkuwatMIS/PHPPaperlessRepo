<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsBusiness */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Update Business Appraisal</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="appraisals-business-form">

            <?php $form = \yii\widgets\ActiveForm::begin(['action' => 'update-business-appraisal?id=' . $model->application_id]); ?>

            <?= $form->field($model, 'place_of_business')->dropDownList(\common\components\Helpers\ListHelper::getPlaceOfBusiness(), ['prompt' => 'Select Place Of Business', 'class' => 'form-control form-control-sm']) ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'fixed_business_assets')->textInput()->label('Fixed Business Asset'); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'fixed_business_assets_amount')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'running_capital')->textInput()->label('Running Capital'); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'running_capital_amount')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'business_expenses')->textInput()->label('Business Expenses'); ?>
                </div>
                <div class="col-sm-6">

                    <?= $form->field($model, 'business_expenses_amount')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'new_required_assets')->textInput()->label('New Required Assets'); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'new_required_assets_amount')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0]) ?>
                </div>
            </div>


            <?php if (!Yii::$app->request->isAjax) { ?>
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            <?php } ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>


<?php
$script = <<< JS

$(document).ready(function(){        
  var tagInputEle = $('#appraisalsbusiness-fixed_business_assets');
  tagInputEle.tagsinput();
});

JS;
$this->registerJs($script);
?>
