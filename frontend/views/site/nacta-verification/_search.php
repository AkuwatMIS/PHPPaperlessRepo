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
        <div id="demo"  style="border:1px solid #d6e9c6;padding:10px">

            <?php $form = ActiveForm::begin([
                'action' => ['nacta-verification'],
                'method' => 'get',
            ]); ?>

            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <?= $form->field($searchModel, 'cnic')
                        ->label("CNIC")
                        ->textInput(['style' => 'font-size:15px;', 'placeholder'=>'Search CNIC', 'class' => 'form-control input', 'required' => 'required', 'value' => isset($searchModel->cnic) ? $searchModel->cnic : '']) ?>
                </div>
                <div class="col-md-6 col-md-offset-4">
                    <?= Html::submitButton('Search', ['class' => 'btn btn-primary btn-md']) ?>
                    <?= Html::resetButton('Reset', ['class' => 'btn btn-default reset']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>