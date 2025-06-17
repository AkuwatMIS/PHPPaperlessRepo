<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LoanWriteOff */

?>

<div class="loan-write-off-form">

    <?php $form = ActiveForm::begin([
        'action' => ['bulk-update'],
        'method' => 'post',
    ]); ?>

        <div class="col-sm-6">
            <input type="hidden" value="<?=$idArray?>" name="id">
            <?= $form->field($model, 'status')->dropDownList([1=>'Approve',2=>'Reject']) ?>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
                <?= Html::submitButton('Update-bulk', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
