<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model common\models\Appraisals */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/appraisals.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = "
window.setTimeout(function() {
    $(\".alert\").fadeTo(3000, 0).slideUp(3000, function(){
        $(this).remove(); 
    });
}, 500);
";
$this->registerJs($js);
?>


<!--<div class="appraisals-form">

    <?php /*$form = ActiveForm::begin(); */?>

    <?/*= $form->field($model, 'name')->textInput(['maxlength' => true]) */?>

    <?/*= $form->field($model, 'appraisal_table')->textInput(['maxlength' => true]) */?>

    <?/*= $form->field($model, 'status')->textInput() */?>

  
	<?php /*if (!Yii::$app->request->isAjax){ */?>
	  	<div class="form-group">
	        <?/*= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) */?>
	    </div>
	<?php /*} */?>

    <?php /*ActiveForm::end(); */?>
    
</div>-->

<?php if (Yii::$app->session->hasFlash('success')) { ?>
    <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
        <h4><i class="icon fa fa-check"></i> Saved!</h4>
        <?= Yii::$app->session->getFlash('success')[0] ?>
    </div>
<?php }
if (Yii::$app->session->hasFlash('error')) { ?>
    <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
        <h4><i class="icon fa fa-remove"></i> Error!</h4>
        <?= Yii::$app->session->getFlash('error')[0] ?>
    </div>
<?php } ?>
<div class="applications-form">

    <?php $form = ActiveForm::begin([
    ]); ?>
    <?= $form->errorSummary($model) ?>
    <div class="row">
        <div class="col-sm-12">
            <?php
            $url = \yii\helpers\Url::to(['/applications/search-application']);
            if (!empty($model->id)) {
                $application = \common\models\Applications::findOne($model->id);
                $cityDesc = '<strong>Application No</strong>: ' . $application->application_no . ' <strong>Member Name</strong>: ' . $application->member->full_name;
            } else {
                $cityDesc = '';
            }
            ?>

            <?= $form->field($model, "id")->widget(\kartik\select2\Select2::classname(), [
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
                        'url' => \yii\helpers\Url::to(['/structure/fetch-appraisals-by-project'])
                    ],
                    'data' => $value ? [$model->appraisal_id => $value] : []
                ])->label('Appraisal');
                ?>
        </div>
    </div>
    <div id="total" class="label label-success pull-right" style="display: none;">
        <div class="row">
            <div class="col-sm-4" style="margin-left: 20px">
                Total Income:<b id="total-income">0</b>
            </div>
            <div class="col-sm-4" style="margin-left: 50px">
                Total Expenses:<b id="total-expenses">0</b>
            </div>
        </div>
    </div>
    <br><br>
    <header class="section-header" id="project-header">


    </header>
    <div class="row" id="project-details">

    </div>
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton(/*$model->isNewRecord ? */'Create'/* : 'Update',*/, ['class' => /*$model->isNewRecord ?*/ 'btn btn-success'/* : 'btn btn-primary'*/,'id'=>'btn-create']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>
</div>