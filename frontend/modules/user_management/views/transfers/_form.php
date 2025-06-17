<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\UserTransfers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-transfers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList($types,['prompt' => 'Select Type']) ?>

    <?php
    echo $form->field($model, 'user_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        //'data'=> array_merge(["" => ""],$users_data),
        'options' => ['placeholder' => 'Select User'],
        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
        'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
        'pluginOptions'=>[
            'depends'=>['usertransfers-type'],
            'url' => \yii\helpers\Url::to(['get-user']),
            'loadingText' => 'Loading child level 2 ...',
        ]
    ]);
    ?>


    <?php
    /*echo $form->field($model, 'user_id')->widget(Select2::classname(), [
        'data' => array_merge(["" => ""],$users_data),
        'options' => ['placeholder' => 'Select User'],
        'size' => Select2::SMALL,
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);*/
    ?>

    <?= $form->field($model, 'role')->dropDownList($designations,['prompt' => 'Select Designation'])?>

    <?php
    echo $form->field($model, 'division_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        //'data'=> array_merge(["" => ""],$users_data),
        'options' => ['placeholder' => 'Select User'],
        'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
        'select2Options'=>['pluginOptions'=>['allowClear'=>true]],
        'pluginOptions'=>[
            'depends'=>['usertransfers-user_id'],
            'url' => \yii\helpers\Url::to(['get-division']),
            'loadingText' => 'Loading child level 2 ...',
        ]
    ]);
    ?>

    <?php
    /*echo $form->field($model, 'division_id')->widget(Select2::classname(), [
        'data' => array_merge(["" => ""],$divisions),
        'options' => ['placeholder' => 'Select User','value' => 1],
        'size' => Select2::SMALL,
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);*/
    ?>

    <?= $form->field($model, 'region_id')->textInput() ?>

    <?= $form->field($model, 'area_id')->textInput() ?>

    <?= $form->field($model, 'branch_id')->textInput() ?>

    <?= $form->field($model, 'team_id')->textInput() ?>

    <?= $form->field($model, 'field_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
