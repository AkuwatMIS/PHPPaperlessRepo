<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
//use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
$this->params['breadcrumbs'][] = $this->title;
$js = '

    $(document).ready(function(){
        
     // alert("jjj")
        $("#applicationdetails-status").change(function(){
            var selected_value =  $("#applicationdetails-status").val();
           // alert(selected_value)
            if(selected_value ==1){
            $("#applicationdetails-created_at").attr(\'required\', "true");
                   
                
            }
        });
    });

';

$this->registerJs($js);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <p>Export PMT Data</p>
                </div>

                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                    <div class="form-group col-md-12">
                        <div class="col-md-3">
                            <?= $form->field($model, 'status')->dropDownList(['1' => 'Processed', '0' => 'Pending'], ['prompt' => 'Select PMT Status']) ?>
                        </div>
                        <div class="col-md-3">
                            <?php
                            echo $form->field($model, 'created_at')->widget(DateRangePicker::classname(), [
                                'convertFormat' => true,
                                'options' => ['class' => 'form-control input-md', 'placeholder' => 'Select Date Range'],
                                'pluginOptions' => [
                                    'startDate' => date("y/m/d"),
                                    'locale' => [
                                        'format' => 'Y/m/d',
                                    ]
                                ]
                            ]);


                            ?>
                        </div>
                    </div>
                    <div class="row pull-right">
                        <div class=" col-sm-12 form-group">
                            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary pull-right']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>