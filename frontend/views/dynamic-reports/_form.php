<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\models\DynamicReports */
/* @var $modelReferrals common\models\Referrals */
/* @var $form yii\widgets\ActiveForm */
$js = '';
$js .= "
$(document).ready(function(){
        $(\".field-dynamicreports-file\").hide();
        $(\".field-dynamicreports-referral_id\").hide();
          $(\".field-dynamicreports-notification\").hide();
        var selected_value =  $(\"#dynamicreports-report_defination_id\").val();
        if(selected_value == 11 || selected_value == 15 || selected_value == 16 || selected_value == 17 || selected_value == 18 || selected_value == 40 || selected_value == 44){
            $(\".field-dynamicreports-region_id\").hide();
            $(\".field-dynamicreports-area_id\").hide();
            $(\".field-dynamicreports-branch_id\").hide();
            $(\".field-dynamicreports-project_id\").hide();
            $(\".field-dynamicreports-report_date\").hide();
            $(\".field-dynamicreports-pmt_score\").hide();
            $(\".field-dynamicreports-file\").show();
        }
        else{
            $(\" .field-dynamicreports-region_id\").show();
            $(\".field-dynamicreports-area_id\").show();
            $(\".field-dynamicreports-branch_id\").show();
            $(\".field-dynamicreports-project_id\").show();
            $(\".field-dynamicreports-report_date\").show();
            $(\".field-dynamicreports-pmt_score\").hide();
            $(\".field-dynamicreports-file\").hide();
        }
        if(selected_value == 14)
        {
           $(\" .field-dynamicreports-notification\").show();
             $(\".project_div\").hide();
        }
    $(\"#dynamicreports-report_defination_id\").change(function(){
        var selected_value =  $(\"#dynamicreports-report_defination_id\").val();             
        if(selected_value == 11 || selected_value == 15 || selected_value == 16 || selected_value == 17 || selected_value == 18 || selected_value == 19 || selected_value == 40 || selected_value == 44){
            $(\".field-dynamicreports-region_id\").hide();
            $(\".field-dynamicreports-area_id\").hide();
            $(\".field-dynamicreports-branch_id\").hide();
            $(\".field-dynamicreports-project_id\").hide();
            $(\".field-dynamicreports-report_date\").hide();
            $(\".field-dynamicreports-file\").show();
            $(\".field-dynamicreports-pmt_score\").hide();
        }       
        else{
            $(\" .field-dynamicreports-region_id\").show();
            $(\".field-dynamicreports-area_id\").show();
            $(\".field-dynamicreports-branch_id\").show();
            $(\".field-dynamicreports-project_id\").show();
            $(\".field-dynamicreports-report_date\").show();
            $(\".field-dynamicreports-file\").hide();
            $(\".field-dynamicreports-pmt_score\").hide();
        }
        if(selected_value == 22 || selected_value == 32 || selected_value == 43){
        
        $(\".field-dynamicreports-region_id\").hide();
            $(\" .field-dynamicreports-area_id\").hide();
            $(\" .field-dynamicreports-branch_id\").hide();
            $(\" .field-dynamicreports-project_id\").hide();
            $(\" .field-dynamicreports-report_date\").show();
            $(\" .field-dynamicreports-file\").hide();
            $(\".field-dynamicreports-pmt_score\").hide();
        }
        
        if(selected_value == 45){
        
        $(\".field-dynamicreports-region_id\").hide();
            $(\" .field-dynamicreports-area_id\").hide();
            $(\" .field-dynamicreports-branch_id\").hide();
            $(\" .field-dynamicreports-project_id\").hide();
            $(\" .field-dynamicreports-report_date\").show();
            $(\" .field-dynamicreports-file\").hide();
            $(\".field-dynamicreports-pmt_score\").hide();
        }
        
        if(selected_value == 33){
        
        $(\".field-dynamicreports-region_id\").hide();
            $(\" .field-dynamicreports-area_id\").hide();
            $(\" .field-dynamicreports-branch_id\").hide();
            $(\" .field-dynamicreports-project_id\").hide();
            $(\" .field-dynamicreports-report_date\").hide();
            $(\" .field-dynamicreports-file\").hide();
            $(\".field-dynamicreports-pmt_score\").hide();
        }
        
        if(selected_value == 34 || selected_value == 35 || selected_value == 36 || selected_value == 37 || selected_value == 42){
        
        $(\".field-dynamicreports-region_id\").hide();
            $(\" .field-dynamicreports-area_id\").hide();
            $(\" .field-dynamicreports-branch_id\").hide();
            $(\" .field-dynamicreports-project_id\").hide();
            $(\" .field-dynamicreports-report_date\").show();
            $(\" .field-dynamicreports-file\").hide();
            $(\".field-dynamicreports-pmt_score\").show();
        }
        
        if(selected_value == 14)
        {
           $(\" .field-dynamicreports-notification\").show();
          
             $(\".project_div\").hide();
        }
        
        if(selected_value == 20){
              $(\".field-dynamicreports-referral_id\").show();
        }else{
              $(\".field-dynamicreports-referral_id\").hide();
        }
    });
});
";
$this->registerJs($js);
?>

<div class="dynamic-reports-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'report_defination_id')->dropDownList($reports_list)->label('Report') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'referral_id')->dropDownList([0=>'Select Referral',$modelReferrals])->label('Referral') ?>
        </div>
    <div class="col-sm-6">
        <?php
        echo $form->field($model, 'region_id')->dropDownList($regions, ['class' => 'form-control input-sm', 'prompt' => 'All Regions'])->label('Region');
        ?>
    </div>

    <div class="col-sm-6">
        <?php
        /*print_r($model->loan->area->name);
        die();*/
        $value = !empty($model->area_id) ? $model->area_id : null;
        echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
            'options' => ['class' => 'form-control input-sm'],
            'pluginOptions' => [
                'depends' => ['dynamicreports-region_id'],
                'initialize' => true,
                'initDepends' => ['portfoliosearch-region_id'],
                'placeholder' => 'All Areas',
                'url' => Url::to(['/structure/fetch-area-by-region'])
            ],
            'data' => $value ? [$model->area_id => $value] : []
        ])->label('Area');
        ?>
    </div>

    <div class="col-sm-6">
        <?php
        $value = !empty($model->branch_id) ? $model->branch_id : null;
        echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
            'options' => ['class' => 'form-control input-sm'],
            'pluginOptions' => [
                'depends' => ['dynamicreports-area_id'],
                //'initialize' => true,
                //'initDepends'=>['progressreportdetailssearch-area_id'],
                'placeholder' => 'All Branches',
                'url' => Url::to(['/structure/fetch-branch-by-area'])
            ],
            'data' => $value ? [$model->branch_id => $value] : []
        ])->label('Branch');
        ?>
    </div>
    <div class="col-sm-6 project_div">
        <?php
        /* echo $form->field($model, 'project_id')->widget(Select2::classname(), [
             'data' => array_merge(["" => ""], $projects),
             'options' => ['placeholder' => 'Select Project'],
             'size' => Select2::SMALL,
             'pluginOptions' => [
                 'allowClear' => true,
             ],
         ])->label("Project");*/
        echo $form->field($model, 'project_id')->dropDownList($projects, ['class' => 'form-control input-sm', 'prompt' => 'All Projects'])->label('Projects');

        ?>
    </div>
        <div class="col-sm-6">
            <?php
            echo $form->field($model, 'report_date')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Date'],
                'pluginOptions'=>[
                    'startDate'      => date("y-m-d"),
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ],
                    /*'minDate' => (string) date('Y-m-d', strtotime('- 3 month')),
                    'maxDate' => (string) date('Y-m-d'),*/
                ]
            ])->label("Date");
            ?>
        </div>

        <div class="col-sm-6">
            <?php
            echo $form->field($model, 'notification')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Date Disburse'],
                'pluginOptions'=>[
                    'startDate'      => date("y-m-d"),
                    'locale'=>[
                        'format'=>'Y-m-d',
                    ],
                    /*'minDate' => (string) date('Y-m-d', strtotime('- 3 month')),
                    'maxDate' => (string) date('Y-m-d'),*/
                ]
            ])->label("Date Disburse");
            ?>
        </div>
        <div class="col-sm-6">
            <?php
            echo $form->field($model, 'pmt_score')->dropDownList(['1'=>"1",'2'=>"2",'3'=>"3",'4'=>"4"], ['class' => 'form-control input-sm', 'prompt' => 'Select PMT']);
            ?>
        </div>
        <div class="col-sm-6 upload-file">
        <?= $form->field($model, 'file')->fileInput(['maxlength' => true]) ?>
        </div>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
