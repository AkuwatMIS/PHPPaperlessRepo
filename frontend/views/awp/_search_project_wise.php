<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */

$js = '
$(document).ready(function(){
    
    $("#crop-type").hide();
    if($("#loanssearch-project_ids").val()=="3"){
            $("#crop-type").show();
       }else{
            $("#crop-type").hide();
       }
    $("#loanssearch-project_ids").change(function(){
       var selected_project =  $("#loanssearch-project_ids").val();
       if(selected_project=="3"){
            $("#crop-type").show();
       }else{
            $("#crop-type").hide();
       }
    });
});
';

$this->registerJs($js);

?>

<div style="border:1px solid #d6e9c6;padding:10px;">

    <?php $form = ActiveForm::begin([
        'action' => ['awp-project-wise'],
        'method' => 'post',
    ]); ?>

    <div class="col-sm-4">
        <?php
        echo $form->field($model, 'month')->dropDownList($months)->label("Report Month");
        ?>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>