<?php

use yii\helpers\Html;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\components\Helpers\StructureHelper;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use common\components\Helpers\ApplicationHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Applications */
/* @var $form yii\widgets\ActiveForm */

$js = "

$(\".fee\").hide();
$(\".cib-fee\").hide();

member_id = $(\"#applications-member_id\").val();

if(member_id){
    if($('#application-save').attr('action')=='/applications/create' || $('#application-save').attr('action')=='/applications/create?id='+member_id){
        $.ajax({
                type: \"POST\",
                url: '/members/application-check?id='+member_id,
                success: function(data){
                    var obj = $.parseJSON(data);
                    if(obj.status_type == 'error'){ 
                        $('#status').append(obj.message);
                        $('#status').show();
                    } 
                  
                    else {
                        $('#status').hide();
                        $('#application-submit').removeAttr('disabled');
                    }
                }
        });
    }
    /*$.ajax({
        type: \"POST\",
        url: '/members/search?id='+member_id,
        success: function(data){
            var obj = $.parseJSON(data);
            $('#applications-region_id').val(obj.region_id);
            $(\"#applications-area_id\").val(obj.area_id);
            $(\"#applications-branch_id\").val(obj.branch_id);
            $(\"#applications-team_id\").val(obj.team_id);
            $(\"#applications-field_id\").val(obj.field_id);
        }
    });*/
}

$(\"#applications-member_id\").on('change', function() {
        member_id = $(\"#applications-member_id\").val();
        if(member_id){
        if($('#application-save').attr('action')=='/applications/create' || $('#application-save').attr('action')=='/applications/create?id='+member_id){
            $.ajax({
                type: \"POST\",
                url: '/members/application-check?id='+member_id,
                success: function(data){
                    var obj = $.parseJSON(data);
                    if(obj.status_type == 'error'){
                        $('#status').append(obj.message);
                        $('#status').show();
                    } else if(obj.status_type == 'info'){
                        $('#status').append(obj.message);
                        $('#status').show();
                          $('#application-submit').removeAttr('disabled');
                    }
                    else {
                        $('#status').hide();
                        $('#application-submit').removeAttr('disabled');
                    }
                }
            });
          }
        }
        /*$.ajax({
            type: \"POST\",
            url: '/members/search?id='+member_id,
            success: function(data){
                var obj = $.parseJSON(data);
                $('#applications-region_id').val(obj.region_id);
                $(\"#applications-area_id\").val(obj.area_id);
                $(\"#applications-branch_id\").val(obj.branch_id);
                $(\"#applications-team_id\").val(obj.team_id);
                $(\"#applications-field_id\").val(obj.field_id);
            }
        });*/
    });

$(document).ready(function(){
    //$(\".field-applicationscib-fee\").hide();
    //$(\".field-applicationscib-receipt_no\").hide();
    $(\"#SIDB-disability_SIDB-div\").hide();
    //$('#applicationscib-fee').val('');
    //$('#applicationscib-receipt_no').val('');
    //$('#applicationscib-fee').removeAttr('required');
    //$('#applicationscib-receipt_no').removeAttr('required');


$(\"#applications-branch_id\").on('change', function() {
       var branch_id =  $(\"#applications-branch_id\").val();
         $.ajax({
            type: \"POST\",
            url: '/applications/validate-cib-amount?id='+branch_id,
            success: function(data){
                var obj = $.parseJSON(data);
                $(\"#applicationscib-fee\").val(obj.cib_fee);
            }
        });

});

$(\".fee\").hide();
$(\".cib-fee\").hide();
project_id = $(\"#applications-project_id\").val();
        
         if(project_id=='98' || project_id=='83' || project_id=='3' || project_id=='79' || project_id=='52' ||project_id=='90' || project_id=='77' || project_id=='67' || project_id=='59'|| project_id=='60' || project_id==61 || project_id==62 || project_id==64 || project_id==76 || project_id==103 || project_id==113 || project_id==127 || project_id==132){
             $('.bzns').hide();
             $('.work').hide();
         }
         
          if(project_id === '132'){
             $('.applicant_property_id_div').show();
          }else{
            $('.applicant_property_id_div').hide();
          }
          
         if(project_id=='109' || project_id=='103' || project_id=='97' || project_id=='83' || project_id=='77' || project_id=='52' ||project_id=='90'  || project_id=='67' || project_id=='61' || project_id=='62' || project_id=='64' || project_id=='2' || project_id=='35' || project_id=='10' || project_id=='19' || project_id=='11' || project_id=='46' || project_id=='53' || project_id=='76' || project_id=='127' || project_id=='132'){
           $('.sub-activity').show();
           if(project_id=='109' || project_id=='103' || project_id=='97' || project_id=='83' || project_id=='52' || project_id=='90'  || project_id=='77' || project_id=='61' || project_id=='62' || project_id=='64' || project_id=='67' || project_id=='76' || project_id=='127' || project_id=='132'){
             $('#applications-clint_contribution').attr('required', 'required');
           }
           
           if(project_id=='52' || project_id=='90'  || project_id=='61' || project_id=='62' || project_id=='64' || project_id=='67' || project_id=='76' || project_id=='127' || project_id=='132' || project_id=='83' || project_id=='98' || project_id=='103' || project_id=='109' || project_id=='113' || project_id=='132'){
            $('#applications-sub_activity').attr('required', 'required');
           }
           
           $('.clint-contribution').show();
          
           //$('.field-applicationscib-fee').show();
           //$('.field-applicationscib-receipt_no').show();
           //$('#applicationscib-fee').val('20');
           ////////$('#applicationscib-receipt_no').val('');
           //$('#applicationscib-fee').attr('required', 'required');
           //$('#applicationscib-receipt_no').attr('required', 'required');
           

           $.ajax({
                type:'POST',
                url: '/members/application-check?id='+member_id+'&project_id='+project_id,
                success: function(data){
                    var obj = $.parseJSON(data);
                    if(obj.status_type == 'error'){
                        $('#status').text('');
                        $('#status').append(obj.message);
                        $('#status').show();
                        $('#application-submit').attr('disabled', 'true');

                    }
                    else {
                        $('#status').hide();
                        $('#application-submit').removeAttr('disabled');
                    }
                }
           });
         }else{
           $('.sub-activity').hide();
           $('#applications-sub_activity').removeAttr('required');
           $('.clint-contribution').hide();
           $('#applications-clint_contribution').removeAttr('required');
           //$('.field-applicationscib-fee').hide();
           //$('.field-applicationscib-receipt_no').hide();
           //$('#applicationscib-fee').val('');
           //$('#applicationscib-receipt_no').val('');
           //$('#applicationscib-fee').removeAttr('required');
           //$('#applicationscib-receipt_no').removeAttr('required');
           $.ajax({
                type:'POST',
                url: '/members/application-check?id='+member_id+'&project_id='+project_id,
                success: function(data){
                    var obj = $.parseJSON(data);
                    if(obj.status_type == 'error'){
                        $('#status').text('');
                        $('#status').append(obj.message);
                        $('#status').show();
                        $('#application-submit').attr('disabled', 'true');

                    }
                    else {
                        $('#status').hide();
                        $('#application-submit').removeAttr('disabled');
                    }
                }
           });
         }
        if(project_id=='98' || project_id=='83' || project_id=='3' || project_id=='79' || project_id=='4' || project_id=='26' || project_id=='52'  || project_id=='90' || project_id=='77' || project_id=='59' || project_id=='60' || project_id=='67' || project_id=='76' || project_id=='77' || project_id=='75' || project_id=='78' || project_id=='79' || project_id=='132'){
             $(\".fee\").hide();
             $(\".cib-fee\").hide();
             $('#applications-fee').removeAttr('required');
             $('#applications-fee').val('');
        }
        
        else if(project_id=='17'){
        $(\".fee\").hide();
            $('#applications-fee').attr('required', 'required');
            $('#applications-fee').attr('readonly', 'readonly');
            $('#applications-fee').val('300');
        }else if(project_id=='67' || project_id=='64'){
        $(\".fee\").hide();
            $('#applications-fee').attr('required', 'required');
            $('#applications-fee').attr('readonly', 'readonly');
            $('#applications-fee').val('500');
        }
        else{
            $(\".fee\").hide();
            $('#applications-fee').attr('required', 'required');
            $('#applications-fee').attr('readonly', 'readonly');
            $('#applications-fee').val('200');
        }
        if(project_id){
            $.ajax({
                type: \"POST\",
                url: '/applications/validate-req-amount?id='+project_id,
                success: function(data){
                    var obj = $.parseJSON(data);
                  $('#applications-req_amount').attr('max',obj.limit);
    
                }
            });
        }
 $(\"#applications-project_id\").on('change', function() {
     var project_id = $(\"#applications-project_id\").val();
     var branch_id = $(\"#applications-branch_id\").val();
     var req_amnt = $(\"#applications-req_amount\").val();
     
      $.ajax({
                type:'POST',                
                url: '/applications/get-application-fee?id='+project_id+'&branch_id='+branch_id,
                success: function(data){
                    var obj = $.parseJSON(data);
                    if(obj.status == 'success'){
                        $(\"# applications-fee\").val(obj.fee);
                        $(\"# applicationscib-fee\").val(obj.cib);
                    }
                }
           });
     
     if(req_amnt  && req_amnt<=10000 && (project_id=='59' || project_id=='60')){
         $('#applicationscib-receipt_no').removeAttr('required');
         $('#applicationscib-fee').removeAttr('required');
         $('.field-applicationscib-fee').hide();
         $('.field-applicationscib-receipt_no').hide();
     }else{
         //$('#applicationscib-receipt_no').attr('required', 'required');
         //$('#applicationscib-fee').attr('required', 'required');
         $('.field-applicationscib-fee').show();
         $('.field-applicationscib-receipt_no').show();
     }
 });
 $(\"#applications-req_amount\").on('change', function() {
    var project_id = $(\"#applications-project_id\").val();
     var req_amnt = $(\"#applications-req_amount\").val();
     if(req_amnt  && req_amnt<=10000 && (project_id=='59' || project_id=='60')){
         $('#applicationscib-receipt_no').removeAttr('required');
         $('#applicationscib-fee').removeAttr('required');
         $('.field-applicationscib-fee').hide();
         $('.field-applicationscib-receipt_no').hide();
     }else{
         //$('#applicationscib-receipt_no').attr('required', 'required');
         //$('#applicationscib-fee').attr('required', 'required');
         $('.field-applicationscib-fee').show();
         $('.field-applicationscib-receipt_no').show();
     }
 });
 $(\"#applications-project_id\").on('change', function() {
        project_id = $(\"#applications-project_id\").val();    
          if(project_id === '132'){
             $(\"#applicant_property_id_div\").show();
          }else{
             $(\"#applicant_property_id_div\").hide();
          }
        
        if(project_id=='98' || project_id=='83' || project_id=='3' || project_id=='79' || project_id=='4' ||project_id=='90' || project_id=='26' || project_id=='52' || project_id=='77'  || project_id=='67' || project_id=='59' || project_id=='60' || project_id==61 || project_id==62 || project_id==64 || project_id==76  || project_id=='76' || project_id=='77' || project_id=='75' || project_id=='78' || project_id=='79' || project_id=='132'){
             $(\".fee\").hide();
             $('#applications-fee').removeAttr('required');
             $('#applications-fee').val('');
             
        }
        else if(project_id=='17'){
        $(\".fee\").hide();
            $('#applications-fee').attr('required', 'required');
            $('#applications-fee').attr('readonly', 'readonly');
            $('#applications-fee').val('300');
        }else if(project_id=='67' || project_id=='64'){
        $(\".fee\").hide();
            $('#applications-fee').attr('required', 'required');
            $('#applications-fee').attr('readonly', 'readonly');
            $('#applications-fee').val('500');
        }else if(project_id=='105' || project_id=='106'){
        $(\".fee\").hide();
            $('#applications-fee').attr('required', 'required');
            $('#applications-fee').attr('readonly', 'readonly');
            $('#applications-fee').val('100');
        }
        else{
            $(\".fee\").hide();
            $('#applications-fee').attr('required', 'required');
            $('#applications-fee').attr('readonly', 'readonly');
            $('#applications-fee').val('200');
          
        }
        if(project_id=='98' || project_id=='83' || project_id=='3' || project_id=='79' ||project_id=='90' || project_id=='52' || project_id=='77' || project_id=='67' || project_id=='59' || project_id=='60' || project_id==61 || project_id==62 || project_id==64 || project_id==76 || project_id==97 || project_id==127 || project_id==103 || project_id==109 || project_id==113 || project_id=='132'){
            $('.bzns').hide();
            $('.work').hide();
        } else {
            $('.bzns').show();
            $('.work').show();
        }
        if(project_id=='109' || project_id=='103' || project_id=='97' || project_id=='83' || project_id=='90' || project_id=='52' || project_id=='77' || project_id=='67' || project_id==61 || project_id==62 || project_id==64 || project_id=='2' || project_id=='35' || project_id=='10' || project_id=='19' || project_id=='11' || project_id=='46' || project_id=='53' || project_id=='76' || project_id=='127' || project_id=='132'){
           $('.sub-activity').show();
           if(project_id=='109' || project_id=='103' || project_id=='97' || project_id=='83' || project_id=='90' || project_id=='52' || project_id=='77' || project_id=='61' || project_id=='62' || project_id=='64' || project_id=='67' || project_id=='76' || project_id=='127' || project_id=='132'){
             $('#applications-clint_contribution').attr('required', 'required');
           }
           
             if(project_id=='83' ||project_id=='90' || project_id=='52' || project_id=='61' || project_id=='62' || project_id=='64' || project_id=='67' || project_id=='76' || project_id=='127' || project_id=='98' || project_id=='103' || project_id=='109' || project_id=='113' || project_id=='132'){
            $('#applications-sub_activity').attr('required', 'required');
           }
           
           $('.clint-contribution').show();
          
           //$('.field-applicationscib-fee').show();
           //$('.field-applicationscib-receipt_no').show();
           //$('#applicationscib-fee').val(20);
           //////$('#applicationscib-receipt_no').val('');
           //$('#applicationscib-fee').attr('required', 'required');
           //$('#applicationscib-receipt_no').attr('required', 'required');
           $.ajax({
                type:'POST',
                url: '/members/application-check?id='+member_id+'&project_id='+project_id,
                success: function(data){
                    var obj = $.parseJSON(data);
                    if(obj.status_type == 'error'){
                        $('#status').text('');
                        $('#status').append(obj.message);
                        $('#status').show();     
                        $('#application-submit').attr('disabled', 'true');

                    }
                    else {
                        $('#status').hide();
                        $('#application-submit').removeAttr('disabled');
                    }
                }
           });
         }else{
           $('.sub-activity').hide();
           $('#applications-sub_activity').removeAttr('required');
           $('.clint-contribution').hide();
           $('#applications-clint_contribution').removeAttr('required');
           //$('.field-applicationscib-fee').hide();
           //$('.field-applicationscib-receipt_no').hide();
           //$('#applicationscib-fee').val('');
           //$('#applicationscib-receipt_no').val('');
           //$('#applicationscib-fee').removeAttr('required');
           //$('#applicationscib-receipt_no').removeAttr('required');
           $.ajax({
                type:'POST',
                url: '/members/application-check?id='+member_id+'&project_id='+project_id,
                success: function(data){
                    var obj = $.parseJSON(data);
                    if(obj.status_type == 'error'){
                        $('#status').text('');
                        $('#status').append(obj.message);
                        $('#status').show();
                        $('#application-submit').attr('disabled', 'true');

                    }
                    else {
                        $('#status').hide();
                        $('#application-submit').removeAttr('disabled');
                    }
                }
           });
         }
        $.ajax({
            type: \"POST\",
            url: '/applications/validate-req-amount?id='+project_id,
            success: function(data){
                var obj = $.parseJSON(data);
              $('#applications-req_amount').attr('max',obj.limit);

            }
        });
    });
        var selected_value =  $(\"#applications-who_will_work\").val();
        if(selected_value != \"self\" && selected_value!=''){
               $(\".field-applications-name_of_other\").show();
               $(\".field-applications-other_cnic\").show();
               $('#applications-name_of_other').attr('required', 'required');
               $('#applications-other_cnic').attr('required', 'required');
               
        }else{
               $(\"#applications-name_of_other\").val('');
               $(\"#applications-other_cnic\").val('');
               $('#applications-name_of_other').removeAttr('required');
               $(\".field-applications-name_of_other\").hide();
               $(\".field-applications-other_cnic\").hide();
        }
 
   

    //$(\".field-applications-name_of_other\").hide();
    //$(\".field-applications-name_of_other\").val();
    //$(\".field-applications-other_cnic\").hide();
    $(\"#applications-who_will_work\").change(function(){
        var selected_value =  $(\"#applications-who_will_work\").val();
        if(selected_value != \"self\" && selected_value!=''){
               $(\"#applications-name_of_other\").val('');
               $(\"#applications-other_cnic\").val(''); 
               $('#applications-name_of_other').attr('required', 'required');
               $('#applications-other_cnic').attr('required', 'required');
               $(\".field-applications-name_of_other\").show();
               $(\".field-applications-other_cnic\").show();

        }else{
               $(\"#applications-name_of_other\").val('');
               $(\"#applications-other_cnic\").val('');
               $('#applications-name_of_other').removeAttr('required');
               $('#applications-other_cnic').removeAttr('required');
               $(\".field-applications-name_of_other\").hide();
               $(\".field-applications-other_cnic\").hide();
        }
    });
    var selected_value =  $(\"#applications-status\").val();
        if(selected_value == \"rejected\"){
           $(\"#rejectreason\").show();
        }else{
           $(\"#rejectreason\").hide();
        }
$(\"#applications-status\").change(function(){
        var selected_value =  $(\"#applications-status\").val();
        if(selected_value == \"rejected\"){
           $(\"#rejectreason\").show();
        }else{
           $(\"#rejectreason\").hide();
        }
    });
    
$(\"#applications-activity_id\").change(function(){
    var selected_value =  $(\"#applications-activity_id\").val();
    if(selected_value == 1){
     $(\"#applications-req_amount\").val('');
    }
});

$(\"#applications-req_amount\").change(function(){
    var selected_value =  $(\"#applications-req_amount\").val();
    var activity_id =  $(\"#applications-activity_id\").val();
    if(activity_id == 1){
    if(selected_value > 200000){
       $(\"#applications-req_amount\").val('');
       alert('Max amount should be 150000.');
     }
    }
    
});
    
});
";
$this->registerJs($js);

?>

    <div class="applications-form">

        <?php $form = ActiveForm::begin(['id' => 'application-save']); ?>
        <?= $form->errorSummary($model) ?>
        <div class="row">
            <div class="col-sm-12">
                <?php
                $url = \yii\helpers\Url::to(['/applications/search-member']);
                if (!empty($model->member_id)) {
                    $member = \common\models\Members::findOne($model->member_id);
                    $cityDesc = '<strong>Name</strong>: ' . $member->full_name . ' <strong>CNIC</strong>: ' . $member->cnic;
                } else {
                    $cityDesc = '';
                }
                //$cityDesc =  '<strong>Name</strong>: ' . $member ->full_name . ' <strong>CNIC</strong>: ' . $member->cnic;
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
            </div>
        </div>
        <div id="status" style="display:none;" class="alert alert-danger status"></div>
        <div class="row">
            <div class="col-sm-4">
                <?php
                //$regions = ArrayHelper::map(Regions::find()->asArray()->all(), 'id', 'name');
                echo $form->field($model, 'branch_id')->dropDownList($branches, ['prompt' => 'Select Branch'])->label('Branch'); ?>
            </div>
            <div class="col-sm-4">
                <?php
                $value = !empty($model->team_id) ? $model->team->name : null;
                echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['applications-branch_id'],
                        'initialize' => true,
                        'initDepends' => ['applications-branch_id'],
                        'placeholder' => 'Select Team',
                        'url' => Url::to(['/structure/fetch-team-by-branch'])
                    ],
                    'data' => $value ? [$model->team_id => $value] : []
                ])->label('Team');
                ?>
            </div>
            <div class="col-sm-4">
                <?php
                $value = !empty($model->field_id) ? $model->field->name : null;
                echo $form->field($model, 'field_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['applications-team_id'],
                        //'initialize' => true,
                        //'initDepends'=>['progressreportdetailssearch-area_id'],
                        'placeholder' => 'Select Field',
                        'url' => Url::to(['/structure/fetch-field-by-team'])
                    ],
                    'data' => $value ? [$model->field_id => $value] : []
                ])->label('Field');
                ?>
            </div>
        </div>
        <!--<? /*= $form->field($model, 'region_id')->hiddenInput()->label(false); */ ?>
    <? /*= $form->field($model, 'area_id')->hiddenInput()->label(false); */ ?>
    <? /*= $form->field($model, 'branch_id')->hiddenInput()->label(false); */ ?>
    <? /*= $form->field($model, 'team_id')->hiddenInput()->label(false); */ ?>
    <? /*= $form->field($model, 'field_id')->hiddenInput()->label(false); */ ?>-->
        <div class="row">
            <div class="col-sm-4">
                <?php
                $value = !empty($model->project_id) ? $model->project->name : null;
                echo $form->field($model, 'project_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['applications-branch_id'],
                        'initialize' => true,
                        'initDepends' => ['applications-branch_id'],
                        'placeholder' => 'Select Project',
                        'url' => Url::to(['/structure/fetch-project-by-branch'])
                    ],
                    'data' => $value ? [$model->project_id => $value] : []
                ])->label('Project');
                ?>
                <!--                --><?php //$form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project'); ?>
            </div>
            <div class="col-sm-4">
                <?php
                $value = !empty($model->product_id) ? $model->product->name : null;
                echo $form->field($model, 'product_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['applications-project_id'],
                        'initialize' => true,
                        'initDepends' => ['applications-project_id'],
                        'placeholder' => 'Select Product',
                        'url' => Url::to(['/structure/fetch-product-by-project'])
                    ],
                    'data' => $value ? [$model->product_id => $value] : []
                ])->label('Product');
                ?>
            </div>

            <div class="col-sm-4 activity">
                <?php
                $value = !empty($model->activity_id) ? $model->activity->name : null;
                echo $form->field($model, 'activity_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['applications-product_id'],
                        //'initialize' => true,
                        //'initDepends'=>['progressreportdetailssearch-area_id'],
                        'placeholder' => 'Select Activity',

                        'url' => Url::to(['/structure/fetch-activity-by-product'])
                    ],
                    'data' => $value ? [$model->activity_id => $value] : []
                ])->label('Activity');
                ?>
            </div>
            <div class="col-sm-4 sub-activity" style="display:none">
                <?php
                $value = !empty($model->sub_activity) ? $model->sub_activity : null;
                echo $form->field($model, 'sub_activity', ['enableClientValidation' => false])->widget(DepDrop::classname(), [
                    'type' => DepDrop::TYPE_SELECT2,
                    'options' => [
                        'multiple' => true,
                        'theme' => Select2::THEME_BOOTSTRAP,
                    ],
                    'pluginOptions' => [
                        'depends' => ['applications-activity_id'],
                        //'initialize' => true,
                        //'initDepends'=>['progressreportdetailssearch-area_id'],
                        'placeholder' => 'Select Sub Activity',
                        'url' => Url::to(['/structure/fetch-sub-activity-by-activity'])
                    ],
                    'data' => $value
                ])->label('Sub Activity');
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 bzns">
                <!--<? /*= $form->field($model, 'bzns_cond')->textInput(['maxlength' => true]) */ ?>-->
                <?= $form->field($model, 'bzns_cond')->dropDownList(['old' => 'Old', 'new' => 'New'], ['prompt' => 'Select Bzns Cond', 'class' => 'form-control form-control-sm'])->label('Business Cond') ?>

            </div>
            <div class="col-lg-3 work">
                <!--<? /*= $form->field($model, 'who_will_work')->textInput(['maxlength' => true]) */ ?>-->
                <?= $form->field($model, 'who_will_work')->dropDownList(\common\components\Helpers\ApplicationHelper::getWhoWillWork(), ['prompt' => 'Select Who Will Work', 'class' => 'form-control form-control-sm'])->label('Who Will Work') ?>

            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'name_of_other')->textInput(['maxlength' => true, 'placeholder' => 'Enter Name of Other']) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'other_cnic')->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '99999-9999999-9',
                ])->textInput(['maxlength' => true, 'placeholder' => 'Other CNIC', 'class' => 'form-control']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'req_amount')->textInput(['min' => 0, 'max' => 50000, 'type' => 'number', 'placeholder' => 'Enter Req. Amount'])->label('Requested Amount') ?>
            </div>

            <div class="col-sm-4 clint-contribution" style="display:none">
                <!--<? /*= $form->field($model, 'bzns_cond')->textInput(['maxlength' => true]) */ ?>-->
                <?= $form->field($model, 'client_contribution')->textInput(['min' => 0, 'type' => 'number', 'placeholder' => 'Enter Clent Contribution'])->label('Client Contribution') ?>
            </div>
            <!--<div class="col-lg-3 fee">
            <? /*= $form->field($model, 'fee')->textInput(['maxlength' => true]) */ ?>
        </div>-->


            <div class="col-lg-3">
                <?= $form->field($model, 'is_urban')->dropDownList(\common\components\Helpers\ApplicationHelper::getIsUrban(), ['prompt' => 'Select Urban/Rural', 'class' => 'form-control form-control-sm'])->label('Urban/Rural') ?>
            </div>
            <div class="col-lg-3">
                <?php
                $value = common\components\Helpers\ApplicationHelper::getReferredByList();
                echo $form->field($model, 'referral_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['applications-project_id'],
//                    'initialize' => true,
//                    'initDepends' => ['applications-project_id'],
                        'placeholder' => 'Select Referral',
                        'url' => Url::to(['/structure/fetch-referral-by-project'])
                    ],
                    'data' => $value
                ])->label('Referred By');
                ?>
            </div>

            <?php if (!$model->isNewRecord) {
                $status = [$model->status => $model->status, 'rejected' => 'Rejected']; ?>
                <div class="col-lg-3">
                    <?= $form->field($model, 'status')->dropDownList($status, ['class' => 'form-control', 'prompt' => 'Select Status'])->label("Status") ?>
                </div>
                <div class="col-lg-3" id="rejectreason" style="display:none">
                    <?= $form->field($model, 'reject_reason')->textInput(['class' => 'form-control', 'placeholder' => 'Select Reject Reason'])->label("Reject Reason") ?>
                </div>
            <?php } ?>
        </div>

        <div class="row">
            <div class="col-lg-6 ml-0 pl-0">
                <div class="col-lg-12">
                    <div class="col-lg-4">
                        <?php if ($model->isNewRecord) { ?>
                            <?= $form->field($model, 'application_no')->textInput(['maxlength' => true, 'type' => 'number', 'min' => '0', 'placeholder' => 'Enter Application Number']) ?>
                        <?php } else { ?>
                            <?= $form->field($model, 'application_no')->textInput(['maxlength' => true, 'type' => 'number', 'min' => '0', 'placeholder' => 'Enter Application Number']) ?>
                        <?php } ?>

                    </div>
                    <?php if ($model->isNewRecord) {
                        ?>
                        <div class="col-lg-4 fee" style="display:none">
                            <label>Application Fee</label>
                            <?= $form->field($model, 'fee')->textInput(['maxlength' => true, 'placeholder' => 'Enter Fee'])->label(false) ?>
                        </div>
                        <?php
                    } else {
                        if ($model->project_id == 3 || $model->project_id == 4 || $model->project_id == 26 || in_array($model->project_id, StructureHelper::trancheProjects())) { ?>
                            <div class="col-lg-4 fee" style="display:none">
                                <label>Application Fee</label>
                                <?= $form->field($model, 'fee')->textInput(['maxlength' => true])->label(false) ?>
                            </div>
                        <?php } else {
                            ?>
                            <div class="col-lg-4 fee" style="display:block">
                                <label>Application Fee</label>
                                <?= $form->field($model, 'fee')->textInput(['maxlength' => true])->label(false) ?>
                            </div>
                            <?php
                        }
                    } ?>
                    <?php
                    if ($model->isNewRecord) {
                        $model->application_date = date('Y-m-d');
                    }
                    ?>
                    <div class="col-lg-4">
                        <?= $form->field($model, "application_date")->widget(\yii\jui\DatePicker::className(), [
                            'dateFormat' => 'yyyy-MM-dd',
                            //'value' => $model->isNewRecord?date('Y-m-d'):$model->application_date,

                            'options' => ['class' => 'form-control', 'placeholder' => 'Application Date',
                                'readonly' => 'readonly',
                                //'value'=>date('Y-m-d'),
                            ],
                            'clientOptions' => [
                                'changeMonth' => true,
                                'changeYear' => true,
                                //'value'=>date('Y-m-d')
                            ]
                        ])->label('Application Date'); ?>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="col-lg-6">
                        <?= $form->field($cib_model, 'receipt_no', ['enableClientValidation' => false])->textInput([/*'required'=>true,*/
                            'maxlength' => true, 'type' => 'number', 'min' => '0', 'placeholder' => 'Enter CIB Receipt No'])->label('CIB Receipt No') ?>
                    </div>
                    <div class="col-lg-6 cib-fee">
                        <?= $form->field($cib_model, 'fee', ['enableClientValidation' => false])->textInput(['maxlength' => true, 'value' => 12, 'readonly' => true, 'placeholder' => 'Enter CIB Fee'])->label('CIB Fee') ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12" id="SIDB-disability_SIDB-div">
                <div class="col-lg-3">
                    <div class="form-group field-SIDB-disability_SIDB required">
                        <label class="control-label" for="SIDB-disability_SIDB">Disability
                            SIDB</label><select type="text" placeholder="Disability SIDB" id="SIDB-disability_SIDB"
                                                class="form-control" name="Sidb" aria-required="true">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12" id="applicant_property_id_div">
                <div class="col-lg-3">
                    <?= $form->field($model, 'applicant_property_id')->dropDownList(\common\components\Helpers\ApplicationHelper::getPropertyRecordType(), ['prompt' => 'Select Property Record Type', 'class' => 'form-control form-control-sm'])->label('Property Record Type') ?>
                </div>
            </div>
        </div>

        <header class="section-header" id="project-header">

        </header>
        <div class="row" id="project-details">

        </div>
        <?php if (!Yii::$app->request->isAjax) { ?>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'application-submit', 'disabled' => $model->isNewRecord ? 'disabled' : false]) ?>
            </div>
        <?php } ?>

        <?php ActiveForm::end(); ?>

    </div>

<?php
$script = <<< JS

$("#applications-project_id").change(function(){
  project_id_check = $("#applications-project_id").val();
 if(project_id_check === '71'){
         $("#SIDB-disability_SIDB-div").show();
  }
  
});

$("#applications-application_date").change(function(){
 var selected_date = $("#applications-application_date").val();
  editDays(selected_date)
});

function editDays(Selected_date) {
    var project_id = $("#applications-project_id").val();
    if(project_id == 3){
        var myDate = new Date();
        var dd = String(myDate.getDate()).padStart(2, '0');
        var mm = String(myDate.getMonth() - 5).padStart(2, '0');
        var yyyy = myDate.getFullYear();
        myDate = yyyy + '-' + mm + '-' + dd;
        if(Selected_date >= myDate){
        }else {
            alert('only past 6 month allowed!')
            $("#applications-project_id").val('');
        }
    } else {
        var dt = new Date();
        year  = dt.getFullYear();
        month = (dt.getMonth() + 1).toString().padStart(2, "0");
        day   = dt.getDate().toString().padStart(2, "0");
        
        var myDate = year  + '-' + month + '-' + day;
        
         if(Selected_date <= myDate){
            var myDate2 = new Date(year, month, -1);
            var dd2 = String(myDate2.getDate()).padStart(2, '0');
            var mm2 = String(myDate2.getMonth()).padStart(2, '0');
            var mm2 = String(myDate2.getMonth()).padStart(2, '0');
            var yyyy2 = myDate2.getFullYear();
            myDate2 = yyyy2 + '-' + mm2 + '-' + dd2;

            //console.log(Selected_date);
            //console.log(myDate2);
            
            if(Selected_date < myDate2){
               alert('Only current month posting allowed!');
               location.reload();
            }
        }else {
               alert('Future days not allowed!');
               location.reload();
        }
    }
}

JS;
$this->registerJs($script);
?>