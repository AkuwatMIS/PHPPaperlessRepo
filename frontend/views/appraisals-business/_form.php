<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsBusiness */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="appraisals-business-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-12">
            <?php
            $url = \yii\helpers\Url::to(['/social-appraisal/search-application']);
            if (!empty($model->application_id)) {
                $application = \common\models\Applications::findOne($model->application_id);
                $cityDesc = '<strong>Application No</strong>: ' . $application->application_no . ' <strong>Member Name</strong>: ' . $application->member->full_name;
            } else {
                $cityDesc = '';
            }
            ?>
            <?=
            $form->field($model, "application_id")->widget(\kartik\select2\Select2::classname(), [
                'initValueText' => $cityDesc, // set the initial display text
                'options' => ['placeholder' => 'Search for a Application No  / Member CNIC...', 'class' => 'file'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'language' => [
                        'errorLoading' => new \yii\web\JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new \yii\web\JsExpression('function(city) { return city.text; }'),
                    'templateSelection' => new \yii\web\JsExpression('function (city) { return city.text; }'),
                    'disabled' => true
                ],
            ])->label('Application');
            ?>
        </div>
    </div>

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

<?php
$script = <<< JS

$(document).ready(function(){        
  var tagInputEle = $('#appraisalsbusiness-fixed_business_assets');
  tagInputEle.tagsinput();
});

JS;
$this->registerJs($script);
?>
