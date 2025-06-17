<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Appraisals */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/appraisals.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = "";
$this->registerJs($js);
?>
    <style>
        .select2-container--krajee-bs3 .select2-results__option--highlighted[aria-selected] {
            background-color: #337ab7 !important;
            color: #fff;
        }
    </style>
<?php if (Yii::$app->session->hasFlash('success')) { ?>
    <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
        <h4><i class="icon fa fa-check"></i> Saved!</h4>
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php }
if (Yii::$app->session->hasFlash('error')) { ?>
    <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
        <h4><i class="icon fa fa-remove"></i> Error!</h4>
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php } ?>

    <div class="applications-form">

        <?php $form = ActiveForm::begin(['id' => 'form-update-appraisal'
        ]); ?>
        <?= $form->errorSummary($model) ?>
        <div class="row">
            <div class="col-sm-12">
                <?php
                $url = \yii\helpers\Url::to(['/composite-updates/search-application-nic']);
                if (!empty($model->id)) {
                    $application = \common\models\Applications::findOne($model->id);
                    $cityDesc = '<strong>Application No</strong>: ' . $application->application_no . ' <strong>Member Name</strong>: ' . $application->member->full_name;
                } else {
                    $cityDesc = '';
                }
                ?>

                <?= $form->field($model, "id")->widget(\kartik\select2\Select2::classname(), [
                    'initValueText' => $cityDesc, // set the initial display text
                    'options' => ['placeholder' => 'Search for a Application with CNIC No', 'class' => 'file'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'language' => [
                            'errorLoading' => new \yii\web\JsExpression("function () { return 'Waiting for results...'; }"),
                        ],
                        'ajax' => [
                            'url' => $url,
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(city) { return city.text; }'),
                        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                    ],
                ])->label('Select Application');

                ?>
                <?php
                $value = !empty($model->appraisal_id) ? $model->appraisal_id : null;
                echo $form->field($model, 'appraisal_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['applications-id'],
                        'initialize' => true,
                        'initDepends' => ['applications-id'],
                        'placeholder' => 'Select Appraisal',
                        'url' => \yii\helpers\Url::to(['/structure/fetch-appraisals-all'])
                    ],
                    'data' => $value ? [$model->appraisal_id => $value] : []
                ])->label('Appraisal');
                ?>
            </div>
        </div>

        <?php if (!Yii::$app->request->isAjax) { ?>
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary', 'id' => 'btn-update']) ?>
            </div>
        <?php } ?>

        <?php ActiveForm::end(); ?>
    </div>

<?php
$script = <<< JS

JS;
$this->registerJs($script);
?>