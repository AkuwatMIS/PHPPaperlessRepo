<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Branches */
/* @var $form yii\widgets\ActiveForm */
/*echo "<pre>";
print_r($model);
die();*/
(isset($model->latitude) && isset($model->latitude)) ? $model->coordinates = $model->latitude.','.$model->longitude : $model->coordinates = '33.5753184,73.14307400000007';
?>

<div class="branches-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'uc')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'village')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true,'id'=>'us2-address']) ?>

    <?php
        echo $form->field($model, 'coordinates')->widget('\pigolab\locationpicker\CoordinatesPicker' , [
            'key' => 'AIzaSyD5vhqpbUx6YQTWXrnjt1MXwh7rkI27OkI' ,   // optional , Your can also put your google map api key
            'valueTemplate' => '{latitude},{longitude}' , // Optional , this is default result format
            'options' => [
                'style' => 'width: 100%; height: 300px',  // map canvas width and height
            ],
            'enableSearchBox' => true , // Optional , default is true
            'searchBoxOptions' => [ // searchBox html attributes
                'style' => 'width: 500px;', // Optional , default width and height defined in css coordinates-picker.css
            ],
            'mapOptions' => [
                // set google map optinos
                'rotateControl' => true,
                'scaleControl' => false,
                'streetViewControl' => true,
                'mapTypeId' => new JsExpression('google.maps.MapTypeId.ROADMAP'),
                'heading'=> 90,
                'tilt' => 45 ,

                'mapTypeControl' => true,
                'mapTypeControlOptions' => [
                    'style'    => new JsExpression('google.maps.MapTypeControlStyle.HORIZONTAL_BAR'),
                    'position' => new JsExpression('google.maps.ControlPosition.TOP_CENTER'),
                ]
            ],
            'clientOptions' => [
                'radius'    => 50,
                'addressFormat' => 'street_number',
                'inputBinding' => [
                    'latitudeInput'     => new JsExpression("$('#us2-lat')"),
                    'longitudeInput'    => new JsExpression("$('#us2-lon')"),
                    'locationNameInput' => new JsExpression("$('#us2-address')")
                ],
                'autoComplete' => true,
            ]
        ]);
    ?>
    <?= $form->field($model, 'latitude')->textInput(['maxlength' => true,'id'=>'us2-lat']) ?>
    <?= $form->field($model, 'longitude')->textInput(['maxlength' => true,'id'=>'us2-lon']) ?>
    
    <?php 
    //Auto generated 'code' field might have some issue so its error goes here
    if($model->hasErrors() && !empty($model->getErrors()['code'])) {
    ?>
    <div class="form-group field-branches-code has-error">
    <?php
    $errors = $model->getErrors()['code'];
    echo "<div class='help-block'>More Errors</div>";
    echo "<div class='help-block'>".implode(". ", $errors)."</div>";
    ?>
    </div>
    <?php 
    };
    ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
