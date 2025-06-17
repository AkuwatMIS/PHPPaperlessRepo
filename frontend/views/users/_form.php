<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use common\components\Helpers\MemberHelper;
use borales\extensions\phoneInput\PhoneInput;

/* @var $this yii\web\View */
/* @var $model common\models\Members */
/* @var $form yii\widgets\ActiveForm */

$js = '

$(document).ready(function(){
    $(\'#membersphone-0-phone_type\').parent(\'.field-membersphone-0-phone_type\').hide();
});
$(document).ready(function(){
    $(\'#membersphone-1-phone_type\').parent(\'.field-membersphone-1-phone_type\').hide();
});
';

$this->registerJs($js);

/*echo '<pre>';
print_r($modelAddress);
die();*/

?>



<?php $form = ActiveForm::begin(); ?>
<?= $form->errorSummary($model) ?>
<div class="row">
    <!--<div class="col-lg-3">
        <?php
    /*        // With model & without ActiveForm
            // Note for multiple file upload, the attribute name must be appended with
            // `[]` for PHP to be able to read an array of files
            echo '<label class="control-label">Add Attachments</label>';
            echo FileInput::widget([
                'model' => $model,
                'attribute' => 'profile_pic[]',
                'options' => ['multiple' => true]
            ]);
            */ ?>
    </div>-->
</div>
<div class="row">
    <!--<div class="col-lg-6">
        <?/*= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => 'User Name', 'class' => 'form-control form-control-sm']) */?>
    </div>-->
    <div class="col-lg-6">
        <?= $form->field($model, 'fullname')->textInput(['maxlength' => true, 'placeholder' => 'Full Name', 'class' => 'form-control form-control-sm']) ?>
    </div>
    <div class="col-lg-6">
        <?= $form->field($model, 'father_name')->textInput(['maxlength' => true, 'placeholder' => 'Father Name', 'class' => 'form-control form-control-sm']) ?>
    </div>
    <!--<div class="col-lg-6">
        <?/*= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Email', 'class' => 'form-control form-control-sm']) */?>
    </div>-->
    <div class="col-lg-6">
        <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'placeholder' => 'Address', 'class' => 'form-control form-control-sm']) ?>
    </div>
    <div class="col-lg-6">
        <?= $form->field($model, 'alternate_email')->textInput(['maxlength' => true, 'placeholder' => 'Alternate Email', 'class' => 'form-control form-control-sm']) ?>
    </div>
    <!--<div class="col-lg-6">
        <?/*= $form->field($model, 'mobile')->textInput(['maxlength' => true, 'placeholder' => 'Mobile', 'class' => 'form-control form-control-sm']) */?>
    </div>-->
</div>
<br>
<?php if (!Yii::$app->request->isAjax) { ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php } ?>

<?php ActiveForm::end(); ?>


