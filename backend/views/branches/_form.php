<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Branches */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="branches-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?=$form->field($array['model_branchwitproject'], 'project_ids')->widget(\kartik\select2\Select2::classname(), [
        'data' => $array['projects'],
        'options' => ['placeholder' => 'Select Project', 'multiple' => true,],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,

        ],
    ])->label("Projects"); ?>
<!--     $form->field($array['model_branchwitaccount'], 'account_ids')->widget(\kartik\select2\Select2::classname(), [-->
<!--        'data' => $array['accounts'],-->
<!--        'options' => ['placeholder' => 'Select Account', 'multiple' => true,],-->
<!--        'pluginOptions' => [-->
<!--            'allowClear' => true,-->
<!--            'tags' => true,-->
<!---->
<!--        ],-->
<!--    ])->label("Accounts"); -->

    <h3>Organization Hierarchy</h3>

    <?= $form->field($model, 'cr_division_id')->textInput()->dropDownList($array['credit_division']) ?>

    <?= $form->field($model, 'region_id')->textInput()->dropDownList($array['regions']) ?>

    <?= $form->field($model, 'area_id')->textInput()->dropDownList($array['areas']) ?>

    <h3>Demographic Hierarchy</h3>

    <?= $form->field($model, 'country_id')->textInput()->dropDownList($array['countries']) ?>

    <?= $form->field($model, 'province_id')->textInput()->dropDownList($array['provinces']) ?>

    <?= $form->field($model, 'division_id')->textInput()->dropDownList($array['divisions']) ?>

    <?= $form->field($model, 'city_id')->textInput()->dropDownList($array['cities']) ?>

    <?= $form->field($model, 'district_id')->textInput()->dropDownList($array['districts']) ?>

    <?= $form->field($model, 'tehsil_id')->textInput() ?>

    <h3>Other Info</h3>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'village')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>







    <?= $form->field($model, 'latitude')->textInput() ?>

    <?= $form->field($model, 'longitude')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'opening_date')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>
<!--

    <?/*= $form->field($model, 'assigned_to')->textInput() */?>

    <?/*= $form->field($model, 'created_by')->textInput() */?>

    <?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>

-->
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
