<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ArchiveReports */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="archive-reports-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'report_name')->textInput(['maxlength' => true])->dropDownList(array("duelist-report"=>"DueList Report"),['prompt'=>'Select Report Name']) ?>

    <?= $form->field($model, 'source')->textInput(['maxlength' => true])->dropDownList(array("bi"=>"Bank Islami","omni"=>"Omni","telenor"=>"Telenor"),['prompt'=>'Select Source']) ?>

    <!--<?/*= $form->field($model, 'region_id')->textInput() */?>

    <?/*= $form->field($model, 'area_id')->textInput() */?>

    <?/*= $form->field($model, 'branch_id')->textInput() */?>

    <?/*= $form->field($model, 'project_id')->textInput() */?>-->

    <?php
    echo $form->field($model, 'date_filter')->widget(\kartik\daterange\DateRangePicker::classname(), [
        'convertFormat' => true,
        'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Date Filter'],
        'pluginOptions' => [
            'locale' => [
                'format' => 'Y-m-d',
            ]
        ]
    ])->label("Date Filter");
    ?>
    <!--<?/*= $form->field($model, 'activity_id')->textInput() */?>

    <?/*= $form->field($model, 'product_id')->textInput() */?>

    <?/*= $form->field($model, 'gender')->textInput(['maxlength' => true]) */?>-->

    <?= $form->field($model, 'requested_by')->hiddenInput(['value'=>Yii::$app->user->getId()])->label(false) ?>

    <!--<?/*= $form->field($model, 'file_path')->textInput(['maxlength' => true]) */?> -->

    <!--<?/*= $form->field($model, 'status')->textInput() */?>-->

    <!--<?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>

    <?/*= $form->field($model, 'do_delete')->textInput() */?>-->

    <?= $form->field($model, 'branch_codes')->textarea() ?>

    <?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
