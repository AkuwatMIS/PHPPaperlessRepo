<?php

use app\models\Branches;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LoansSearch */
/* @var $form yii\widgets\ActiveForm */

$js = '';
$js .= "
$(document).ready(function(){
       
        $(\".region\").hide();
        $(\".area\").hide();
        $(\".branch\").hide();
              
         $(\"#disbursementdetailssearch-type\").change(function(){
         
        var selected_value =  $(\"#disbursementdetailssearch-type\").val();
        console.log(selected_value);
        if(selected_value == 1){
            $(\".region\").show();
            $(\".area\").hide();
            $(\".branch\").hide();
        }
        else if(selected_value == 2)
        {
            $(\".region\").hide();
            $(\".area\").show();
            $(\".branch\").hide();
        } 
        else if(selected_value == 3)
        {
            $(\".region\").hide();
            $(\".area\").hide();
            $(\".branch\").show();
        }
         });
         $(\"#disbursementdetailssearch-sanction_no\").change(function(){
          var sanction_values =  $(\"#disbursementdetailssearch-sanction_no\").val();
            const regex = /[^a-zA-Z0-9  , ' -]/g;
            const found = sanction_values.match(regex);
            
         });
});
";
$this->registerJs($js);
?>
<div class="container-fluid">

    <?php $form = ActiveForm::begin([
        'action' => ['allocate-funds'],
        'method' => 'post',
    ]);

    ?>
    <div class="row">

        <div class="col-sm-4">
            <?= $form->field($model, 'bank_name')->dropdownList(\common\components\Helpers\MemberHelper::getBankAccountsAll(), ['prompt' => 'Select Disbursement Source','options'=>[$bank_name_filter=>['Selected'=>true]]])->label('Disbursement Source*'); ?>
        </div>
        <div class="col-sm-4">
            <?php
            $projects = ArrayHelper::map(\common\models\Projects::find()->where(['in','id',[77,78,79,105,106,132]])->asArray()->all(), 'id', 'name');
            echo $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project','options'=>[$query['project_id']=>['Selected'=>true]]])->label('Project*');
            ?>
        </div>
    </div>
    <div class="row collapse border-0" id="demo">
    <?= $form->field($model, 'sanction_no')->label("Sanction Numbers")->textarea(array('rows'=>5,'cols'=>5,'value'=>$query['sanction_no']),['placeholder'=>'Sanction Numbers', 'class'=>'form-control form-control-sm']) ?>
    <div class="col-sm-3">
        <?= $form->field($model, 'type')->dropdownList(
                [
                        1 => 'Region',
                        2 => 'Area',
                        3 => 'Branch'
                ], ['prompt' => 'Select Multi-selection Type'])->label('Select Multi-selection Type'); ?>
    </div>
    <div class="col-sm-3 region">
        <?php
        $value = !empty($query['region_id']) ? $query['region_id'] : null;
        echo $form->field($model, 'region_id')->widget(Select2::classname(), [
            'data' => $regions,
            'language' => 'en',
            'name' => 'region_id[]',
            'options' => ['placeholder' => 'Region'],
            'pluginOptions' => [
                'tags' => true,
                'allowClear' => true,
                'multiple' => true
            ],
        ])->label('Region');

        ?>
    </div>
    <div class="col-sm-3 area">
        <?php
        $value = !empty($query['area_id']) ? $query['area_id'] : null;
        echo $form->field($model, 'area_id')->widget(Select2::classname(), [
            'data' => $areas,
            'language' => 'en',
            'name' => 'area_id[]',
            'options' => ['placeholder' => 'Area'],
            'pluginOptions' => [
                'tags' => true,
                'allowClear' => true,
                'multiple' => true
            ],
        ])->label('Area');
        ?>
    </div>
    <div class="col-sm-3 branch">
        <?php
        $value = !empty($query['branch_id']) ? $query['branch_id'] : null;
        echo $form->field($model, 'branch_id')->widget(Select2::classname(), [
            'data' => $branches_names,
            'language' => 'en',
            'name' => 'branch_id[]',
            'options' => ['placeholder' => 'Branch'],
            'pluginOptions' => [
                'tags' => true,
                'allowClear' => true,
                'multiple' => true
            ],
        ])->label('Branch');
        ?>
    </div>
    </div>

        <a href="#demo" class="btn btn-primary" data-toggle="collapse"><span
        class="glyphicon glyphicon-search"></span> Advance Filter </a>
        <div class="row pull-right">
            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary search_button','id'=>'seacrch']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>

</div>

<br><br><br><br>