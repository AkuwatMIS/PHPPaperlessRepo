<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $areas */
/* @var $modelArea */
/* @var $newArea */
/* @var $model common\models\Branches */
?>
<div class="col-md-4"></div>
<div class="col-md-4">
    <div class="branches-form">

        <?php $form = \yii\widgets\ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true,'readonly'=> true])->label('Branch') ?>
        <?= $form->field($modelArea, 'name')->textInput(['maxlength' => true,'readonly'=> true])->label('Old Area') ?>
        <?= $form->field($model, 'area_id')->hiddenInput()->label(false) ?>

        <?= $form->field($newArea,'id')->widget(\kartik\select2\Select2::classname(), [
            'data' => $areas,
            'options' => ['placeholder' => 'Select Area'],
            'pluginOptions' => [
                'allowClear' => true,
                'tags' => true,

            ],
        ])->label("New Areas"); ?>

        <div class="form-group">
            <?= \yii\helpers\Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>

    </div>

</div>
<div class="col-md-4"></div>
