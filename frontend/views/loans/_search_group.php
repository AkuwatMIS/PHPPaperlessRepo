<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\Groups */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin([
    'action' => ['lac'],
    'method' => 'post',
]); ?>
<div class="col-md-4">
    <?= $form->field($application, 'grp_no')->textInput(['maxlength' => true])->label('Enter Group No') ?>
</div>

<div class="col-md-4">
    <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
