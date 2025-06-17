<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */

$js = '
$(document).ready(function(){
 function clearInput(element){
element.value="";
}
        type = $(\'input[name="GlobalsSearch[type]"]:checked\', \'#globalssearch-type\').val();
            
            if(type == \'sanction_no\'){
                $("label[for=\'globalssearch-sanction_no\']").text("Sanction No");
                $("#globalssearch-sanction_no").attr("name", "GlobalsSearch[sanction_no]");
                $("#globalssearch-sanction_no").attr("placeholder", "Search by Sanction No");
            }else if(type == \'borrower_cnic\'){
                $("label[for=\'globalssearch-sanction_no\']").text("CNIC");
                $("#globalssearch-sanction_no").attr("name", "GlobalsSearch[borrower_cnic]");
                $("#globalssearch-sanction_no").attr("placeholder", "Search by CNIC");
            }else if(type == \'grpno\'){
                $("label[for=\'globalssearch-sanction_no\']").text("Group No");
                $("#globalssearch-sanction_no").attr("name", "GlobalsSearch[grpno]");
                $("#globalssearch-sanction_no").attr("placeholder", "Search by Group No");
            }
        $(\'#globalssearch-type input\').on(\'change\', function() {
        
            type = $(\'input[name="GlobalsSearch[type]"]:checked\', \'#globalssearch-type\').val();
            
            if(type == \'sanction_no\'){
                $("label[for=\'globalssearch-sanction_no\']").text("Sanction No");
                $("#globalssearch-sanction_no").attr("name", "GlobalsSearch[sanction_no]");
                $("#globalssearch-sanction_no").attr("placeholder", "Search by Sanction No");
            }else if(type == \'borrower_cnic\'){
                $("label[for=\'globalssearch-sanction_no\']").text("CNIC");
                $("#globalssearch-sanction_no").attr("name", "GlobalsSearch[borrower_cnic]");
                $("#globalssearch-sanction_no").attr("placeholder", "Search by CNIC");
            }else if(type == \'grpno\'){
                $("label[for=\'globalssearch-sanction_no\']").text("Group No");
                $("#globalssearch-sanction_no").attr("name", "GlobalsSearch[grpno]");
                $("#globalssearch-sanction_no").attr("placeholder", "Search by Group No");
            }
            
        });
        $(\'.reset\').on(\'click\', function() {
            $(\'.input\').attr(\'value\',\'\');
            $("label[for=\'globalssearch-sanction_no\']").text("Sanction No");
            $("#globalssearch-sanction_no").attr("name", "GlobalsSearch[sanction_no]");
            $("#globalssearch-sanction_no").attr("placeholder", "Search by Sanction No");
            //alert($(\'input:radio[name="GlobalsSearch[type]"][value="borrower_cnic"]\'));
            $(\'input:radio[name="GlobalsSearch[type]"][value="borrower_cnic"]\').attr(\'checked\', false);
            $(\'input:radio[name="GlobalsSearch[type]"][value="grpno"]\').attr(\'checked\', false);
            $(\'input:radio[name="GlobalsSearch[type]"][value="sanction_no"]\').attr(\'checked\', true);
            
        });
        });
';
$this->registerJs($js);
?>
<style>
    .btn-md {
        font-size: 15px;
        width: 160px;
        background-color: #5A738E;
    }

    .reset {
        font-size: 15px;
        width: 160px;
    }
    .radio input {
        position: relative;
        visibility: visible;
    }
    label {
         display: inline;
    }
</style>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <div id="demo">

            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
            ]); ?>

            <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($searchModel, 'sanction_no')->label(false)
                            ->textInput(['placeholder'=>'Search with Sanction No','style' => 'font-size:15px;', 'class' => 'form-control input', 'required' => 'required', 'value' => isset($searchModel->$type) ? $searchModel->$type : '']) ?>
                    </div>
                    <div class="col-md-3 text-center">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-primary btn-md']) ?>
                    </div>

            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>