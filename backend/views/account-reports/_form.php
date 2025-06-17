<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProgressReports */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="progress-reports-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if($model->isNewRecord){

        $projects_list = array();
        $projects_list = $projects;
        $projects_list[0] = 'Overall';
        ksort($projects_list,1);

        ?>

        <?= $form->field($model, "report_date")->widget(\yii\jui\DatePicker::className(),[
            'dateFormat' => 'yyyy-MM-dd',
            'options' => ['class' => 'form-control', 'placeholder' => 'Report Date']
        ])->label(false); ?>

        <?= $form->field($model, 'project_id')->dropDownList($projects_list)->label('Project') ?>


        <?= $form->field($model, 'period')->dropDownList($period) ?>

        <?= $form->field($model, 'comments')->textarea(['rows' => '6']) ?>

    <?php }else{ ?>

        <?= $form->field($model, 'report_date')->textInput(['disabled'=>'disabled']) ?>


        <?= $form->field($model, 'status')->dropDownList($status) ?>

        <?= $form->field($model, 'is_verified')->dropDownList($flags) ?>

        <?= $form->field($model, 'do_update')->dropDownList($flags) ?>

        <?= $form->field($model, 'do_delete')->dropDownList($flags) ?>

        <?= $form->field($model, 'is_awp')->dropDownList($flags) ?>

    <?php } ?>


    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
