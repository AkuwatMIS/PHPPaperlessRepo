<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\widgets\form\Form;
/* @var $this yii\web\View */
/* @var $model common\models\Projects */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="projects-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($array['model_projectwithproduct'], 'product_ids')->widget(\kartik\select2\Select2::classname(), [
        'data' => $array['products'],
        'options' => ['placeholder' => 'Select Products', 'multiple' => true,],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,

        ],
    ])->label("Products"); ?>
    <?= $form->field($model, 'project_table')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'donor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'funding_line')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fund_source')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'total_fund')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'fund_received')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'loan_amount_limit')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'charges_percent')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'started_date')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'Start Date']
    ])->label('Start Date')?>

    <?= $form->field($model, 'logo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'short_name')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'sector')->dropDownList(['local'=>'Local','government'=>'Government','international'=>'Intenational']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sc_type')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'project_period')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'ending_date')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'Ending Date']
    ])->label('Ending Date')?>

    <?= $form->field($model, 'status')->textInput() ?>
<!--
    <?/*= $form->field($model, 'assigned_to')->textInput() */?>

   <?/*= $form->field($model, 'created_by')->textInput() */?> -->

    <!--
    <?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>

  -->
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>
    <?php ActiveForm::end(); ?>

