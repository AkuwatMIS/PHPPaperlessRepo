<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $regions */
/* @var $modelRegion */
/* @var $newRegion */
/* @var $model common\models\Branches */
?>
<div class="col-md-4"></div>
<div class="col-md-4">
    <div class="branches-form">

        <?php $form = \yii\widgets\ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true,'readonly'=> true])->label('Area') ?>
        <?= $form->field($modelRegion, 'name')->textInput(['maxlength' => true,'readonly'=> true])->label('Old Region') ?>
        <?= $form->field($model, 'region_id')->hiddenInput()->label(false) ?>

        <?= $form->field($newRegion,'id')->widget(\kartik\select2\Select2::classname(), [
            'data' => $regions,
            'options' => ['placeholder' => 'Select Region'],
            'pluginOptions' => [
                'allowClear' => true,
                'tags' => true,

            ],
        ])->label("New Region"); ?>

        <div class="form-group">
            <?= \yii\helpers\Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>

    </div>

</div>
<div class="col-md-4"></div>
