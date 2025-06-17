<?php
use yii\helpers\Html;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StructureTransfer */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .vl {
        border-left: 2px solid black;
        height: 300px;
    }
</style>
<div class="structure-transfer-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'obj_type')->dropDownList($obj_types, ['prompt' => 'Select Type'])->label('Type'); ?>
        </div>
        <div class="col-md-6">
            <?php
            $value = !empty($model->obj_id) ? $model->obj_id : null;
            echo $form->field($model, 'obj_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['structuretransfer-obj_type'],
                    'initialize' => true,
                    'initDepends' => ['structuretransfer-obj_type'],
                    'placeholder' => 'Select Type Values',
                    'url' => Url::to(['/structure/structure'])
                ],
                'data' => $value ? [$model->obj_id => $value] : []
            ])->label('Type Values');
            ?>
        </div>
    </div>
    <div class="row">

        <div class="col-md-6">
            <h2>OLD Hierarchy</h2>
            <?= $form->field($model, 'old_value')->textInput(['disabled'=>'disabled']) ?>
        </div>

        <div class="col-md-6 vl">
            <h2>New Hierarchy</h2>
            <?= $form->field($model, 'new_value')->textInput() ?>
        </div>
    </div>

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>




  

