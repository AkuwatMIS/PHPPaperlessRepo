<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProgressReportUpdate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="progress-report-update-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'report_id')->textInput()->dropDownList($progress_reports) ?>

    <?= $form->field($model, 'region_id')->textInput()->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region') ?>
<!--
    <?/*= $form->field($model, 'area_id')->textInput() */?>

    <?/*= $form->field($model, 'branch_id')->textInput() */?>-->
    <?php
    $value = !empty($model->area_id) ? $model->area->name : null;
    echo $form->field($model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['progressreportupdate-region_id'],
            'initialize' => true,
            'initDepends' => ['progressreportupdate-region_id'],
            'placeholder' => 'Select Area',
            'url' => \yii\helpers\Url::to(['/progress-report-update/fetch-area-by-region'])
        ],
        'data' => $value ? [$model->area_id => $value] : []
    ])->label('Area');
    ?>
    <?php
    $value = !empty($model->branch_id) ? $model->branch->name : null;
    echo $form->field($model, 'branch_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['progressreportupdate-area_id'],
            'initialize' => true,
            'initDepends' => ['progressreportupdate-area_id'],
            'placeholder' => 'Select Branch',
            'url' => \yii\helpers\Url::to(['/progress-report-update/fetch-branch-by-area'])
        ],
        'data' => $value ? [$model->branch_id => $value] : []
    ])->label('Branch');
    ?>
   <!--<?/*= $form->field($model, 'status')->textInput() */?>-->
<!--
    <?/*= $form->field($model, 'created_by')->textInput() */?>

    <?/*= $form->field($model, 'updated_by')->textInput() */?>

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
