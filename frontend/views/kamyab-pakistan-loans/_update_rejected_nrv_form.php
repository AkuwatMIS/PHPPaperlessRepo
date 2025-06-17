<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\StructureHelper;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid border-0" id="demo">
    <?php $form = ActiveForm::begin([
        'action' => ['/kamyab-pakistan-loans/add-remarks-nadra-verisys'],
        'method' => 'post',
    ]); ?>

    <div class="row">

        <div class="col-md-12">
            <?php

            echo $form->field($model, 'reject_reason')->textInput(['readonly' => 'readonly','value' => $model->reject_reason]);
            ?>

        </div>
        <div class="col-md-12">
            <?php
            echo $form->field($model, 'remarks')->textarea(['rows' => '4']);
            ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'status')->hiddenInput(['value'=>1])->label(false); ?>
            <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('submit', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
