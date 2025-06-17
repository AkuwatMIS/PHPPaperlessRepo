<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectFundDetail */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="project-fund-detail-view">

        <?php $form = \yii\widgets\ActiveForm::begin([ 'action' => ['project-fund-detail/edit-loan-batch-no?id='.$model->id],'method'=>'post']); ?>

            <?php echo $form->field($model, 'batch_id')->dropdownList($batches_names, ['prompt' => 'Select Batch No'])->label('Batch No'); ?>

            <?php
/*            echo $form->field($model, 'batch_id')->widget(Select2::classname(), [
                'data' => $batches_names,
                'options' => ['placeholder' => 'Select Batch'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                    'multiple' => true
                ],
            ])->label('Branch');
            */?>

            <div class="form-group text-right">
                <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
            </div>
        <?php ActiveForm::end(); ?>

</div>
