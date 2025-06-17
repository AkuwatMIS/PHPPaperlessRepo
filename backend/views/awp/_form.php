<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AwpBranchSustainability */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="awp-branch-sustainability-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'region_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Regions::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'area_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Areas::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'branch_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Branches::find()->all(),'id','name')) ?>
    <?= $form->field($model, 'project_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Projects::find()->all(),'id','name')) ?>

    <?= $form->field($model, 'no_of_loans')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'avg_loan_size')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'disbursement_amount')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'monthly_olp')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'active_loans')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'monthly_closed_loans')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'monthly_recovery')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'avg_recovery')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'funds_required')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'actual_recovery')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'actual_disbursement')->textInput(['required'=>true]) ?>
    <?= $form->field($model, 'actual_no_of_loans')->textInput(['required'=>true]) ?>

    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
