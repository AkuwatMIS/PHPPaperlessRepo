<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\BusinessAppraisal */
/* @var $form yii\widgets\ActiveForm */
$js = '
            

$(document).ready(function(){
$("#newbusiness").show();
 function clearInput(element){
element.value="";
}
        type = $(\'input[name="BusinessAppraisal[business]"]:checked\', \'#businessappraisal-business\').val();

            if(type == \'old\'){
               //$("#oldbusiness").show();
               //$("#newbusiness").hide();
               // $("#businessappraisal-ba_fixed_buiness_assets").prop(\'required\',true);
               $("#businessappraisal-ba_fixed_buiness_assets_total").prop(\'required\',true);
               $("#businessappraisal-ba_running_capital_total").prop(\'required\',true);
               $("#businessappraisal-ba_business_expenses_total").prop(\'required\',true);

            }else if(type == \'new\'){
               //$("#oldbusiness").hide();
               //$("#newbusiness").show();
               //$("#businessappraisal-ba_fixed_buiness_assets").prop(\'required\',true);
               $("#businessappraisal-ba_new_required_assets_total").prop(\'required\',true);
            }
        $(\'#businessappraisal-business\').on(\'change\', function() {
            type = $(\'input[name="BusinessAppraisal[business]"]:checked\', \'#businessappraisal-business\').val();
           
            if(type == \'old\'){
              //$(\'#businessappraisal-ba_new_required_assets\').html($(\'#businessappraisal-ba_new_required_assets\').html().replace(\'selected\',\'\'));
              //$("#oldbusiness").show();
              //$("#newbusiness").hide();
              $("#businessappraisal-ba_fixed_buiness_assets_total").prop(\'required\',true);
              $("#businessappraisal-ba_running_capital_total").prop(\'required\',true);
              $("#businessappraisal-ba_business_expenses_total").prop(\'required\',true);

            }
            else if(type == \'new\'){
              //$(\'#businessappraisal-ba_fixed_buiness_assets\').html($(\'#businessappraisal-ba_fixed_buiness_assets\').html().replace(\'selected\',\'\'));
              //$(\'#businessappraisal-ba_running_capital\').html($(\'#businessappraisal-ba_running_capital\').html().replace(\'selected\',\'\'));
              //$(\'#businessappraisal-ba_business_expenses\').html($(\'#businessappraisal-ba_business_expenses\').html().replace(\'selected\',\'\'));
              //$("#oldbusiness").hide();
              //$("#newbusiness").show();
              $("#businessappraisal-ba_new_required_assets_total").prop(\'required\',true);

            }
        });
        });
';
$js .= "
$('#businessappraisal-application_id').change(function(){
var application_id=$('#businessappraisal-application_id').val();

$.ajax({
        type: \"POST\",
        url: '/business-appraisal/activity?id='+application_id,
        success: function(data){
            var obj = $.parseJSON(data);
           //$(\"#activity\").show();
           //$(\"#activity\").text('Activity Name:'+obj.activity);
                      
           $(\"#business_type\").show();
            $(\"#businessappraisal-business_type\").prop(\"disabled\", true);
            $(\"#businessappraisal-business_type\").prop(\"value\", obj.activity);



           $(\"#businessappraisal-ba_fixed_buiness_assets\").html('');
           $(\"#businessappraisal-ba_fixed_buiness_assets\").append(obj.ba_fixed_business_assets);

           $(\"#businessappraisal-ba_running_capital\").html('');
           $(\"#businessappraisal-ba_running_capital\").append(obj.ba_running_capital);
           
           $(\"#businessappraisal-ba_business_expenses\").html('');
           $(\"#businessappraisal-ba_business_expenses\").append(obj.ba_business_expense);
           
           $(\"#businessappraisal-ba_new_required_assets\").html('');
           $(\"#businessappraisal-ba_new_required_assets\").append(obj.ba_new_required_assets);
        }
    });
    });
";
$this->registerJs($js);
?>

<?php
///  var selected_value =  $('#businessappraisal-application_id').val();
//$('.activity').show();
//var a = \"".common\models\Applications::find()->where(['id'=>'1'])->one()->application_no."\";?>
<div class="business-appraisal-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model); ?>
    <?= $form->errorSummary($ba_details); ?>
    <div class="row">
        <div class="col-sm-12">
            <?php
            $url = \yii\helpers\Url::to(['/business-appraisal/search-application']);
            if (!empty($model->application_id)) {
                $application = \common\models\Applications::findOne($model->application_id);
                $cityDesc = '<strong>Application No</strong>: ' . $application->application_no . ' <strong>Name</strong>: ' . $application->member->full_name . ' <strong>CNIC</strong>: ' . $application->member->cnic;
            } else {
                $cityDesc = '';
            }
            ?>

            <?= $form->field($model, "application_id")->widget(Select2::classname(), [


                'initValueText' => $cityDesc, // set the initial display text
                'options' => ['placeholder' => 'Search for a Application / Member CNIC ...', 'class' => 'file'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
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
                ],
                'disabled'=>true,
            ])->label('Select Application');

            ?>
        </div>
        <!--<h4>
            <div id="activity" style="display: none ;margin-left: 15px;">
            </div>
        </h4>-->
    </div>
    <div class="row" id="business_type" style="display: none">
        <div class="col-sm-3">
            <?= $form->field($model, 'business_type')->textInput() ?>
        </div>
    </div>
    <h3 class="m-t-lg with-border">Basic Information</h3>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'place_of_business')->dropDownList(\common\components\Helpers\ListHelper::getPlaceOfBusiness(), ['prompt' => 'Select Place Of Business', 'class' => 'form-control form-control-sm']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <?php $model->isNewRecord == 1 ? $model->business = 'new' : $model->business; ?>
            <?= $form->field($model, 'business')->radioList(['new' => 'New', 'old' => 'Old']); ?>
        </div>
    </div>
    <div class="row" id="oldbusiness"> <!--style="display: none"-->
        <div class="col-sm-3">
            <?= $form->field($model, 'ba_fixed_buiness_assets')->widget(Select2::classname(), [
                'data' =>$fixed_business_assets_dropdown,
                'language' => 'english',
                'options' => ['multiple' => true, 'placeholder' => 'Select Fixed Business Assets ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Fixed Business Asset'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'ba_fixed_buiness_assets_total')->textInput(['placeholder' => "Enter Fixed Business Asset Total",'type' => 'number'])->label('Fixed Business Asset Total') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'ba_running_capital')->widget(Select2::classname(), [
                'data' => $running_capital_dropdown,
                'language' => 'english',
                'options' => ['multiple' => true, 'placeholder' => 'Select Running Capital ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Running Capital'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'ba_running_capital_total')->textInput(['placeholder' => "Enter Running Capital Total",'type' => 'number'])->label('Running Capital Total') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'ba_business_expenses')->widget(Select2::classname(), [
                'data' => $new_required_dropdown,
                'language' => 'english',
                'options' => ['multiple' => true, 'placeholder' => 'Select Business Expenses ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Business Expense'); ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'ba_business_expenses_total')->textInput(['placeholder' => "Enter Business Expense Total",'type' => 'number'])->label('Business Expense Total') ?>
        </div>

        <!--<div id="newbusiness"> --><!--style="display: none"-->
        <div class="col-sm-3" id="newbusiness">
            <?= $form->field($model, 'ba_new_required_assets')->widget(Select2::classname(), [
                'data' => $business_expenses_dropdown,
                'language' => 'english',
                'options' => ['multiple' => true, 'placeholder' => 'Select New Required Assets ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('New Required Asset'); ?>
        </div>
        <div class="col-sm-3" id="newbusiness">
            <?= $form->field($model, 'ba_new_required_assets_total')->textInput(['placeholder' => "Enter New Required Asset Total",'type' => 'number'])->label('New Required Asset Total') ?>
        </div>
        <!--</div>-->
    </div>
    <h3 class="m-t-lg with-border">Beneficiary Income</h3>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($ba_details, 'business_income')->textInput(['placeholder' => "Enter Business Income"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($ba_details, 'job_income')->textInput(['placeholder' => "Enter Job Income"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($ba_details, 'house_rent_income')->textInput(['placeholder' => "Enter House Rent Income"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($ba_details, 'other_income')->textInput(['placeholder' => "Enter Other Income"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($ba_details, 'total_income')->textInput(['placeholder' => "Enter Total Income"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($ba_details, 'expected_increase_in_income')->textInput(['placeholder' => "Enter Expected Increase Income"]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($ba_details, 'description')->textarea(['placeholder' => "Enter Description"]) ?>
        </div>
        <?= $form->field($model, 'longitude')->hiddenInput(['value' => '0'])->label(false) ?>
        <?= $form->field($model, 'latitude')->hiddenInput(['value' => '0'])->label(false) ?>

    </div>
</div>

<?php if (!Yii::$app->request->isAjax) { ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php } ?>

<?php ActiveForm::end(); ?>

</div>
