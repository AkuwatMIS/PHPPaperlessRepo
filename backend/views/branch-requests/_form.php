<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="branch-requests-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'region_id')->dropDownList($array['regions']) ?>

    <?= $form->field($model, 'area_id')->dropDownList($array['areas']) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'short_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city_id')->dropDownList($array['cities'])?>

    <?= $form->field($model, 'tehsil_id')->textInput() ?>

    <?= $form->field($model, 'district_id')->dropDownList($array['districts']) ?>

    <?= $form->field($model, 'division_id')->dropDownList($array['divisions']) ?>

    <?= $form->field($model, 'province_id')->dropDownList($array['provinces']) ?>

    <?= $form->field($model, 'country_id')->dropDownList($array['countries']) ?>

    <?= $form->field($model, 'latitude')->textInput() ?>

    <?= $form->field($model, 'longitude')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'opening_date')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'cr_division_id')->textInput() ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'recommended_on')->textInput() ?>

    <?= $form->field($model, 'recommended_by')->textInput() ?>

    <?= $form->field($model, 'recommended_remarks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'approved_on')->textInput() ?>

    <?= $form->field($model, 'approved_by')->textInput() ?>

    <?= $form->field($model, 'approved_remarks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'assigned_to')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>
<!--
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
