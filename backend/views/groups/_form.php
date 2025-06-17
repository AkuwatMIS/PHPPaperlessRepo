<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Groups */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="groups-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'region_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\components\Helpers\StructureHelper::getRegions(), 'id', 'name'), ['prompt' => 'Select Region'])->label('Region') ?>


    <?php
    $value = !empty($model->area_id) ? $model->area_id : null;
    echo $form->field($model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['groups-region_id'],
            'initialize' => true,
            'initDepends' => ['groups-region_id'],
            'placeholder' => 'Select Area',
            'url' => \yii\helpers\Url::to(['/structure/fetch-areas-by-region'])
        ],
        'data' => $value ? [$model->area_id => $value] : []
    ])->label('Area');
    ?>

    <?php
    $value = !empty($model->branch_id) ? $model->branch_id : null;
    echo $form->field($model, 'branch_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['groups-area_id'],
            'initialize' => true,
            'initDepends' => ['groups-area_id'],
            'placeholder' => 'Select Branch',
            'url' => \yii\helpers\Url::to(['/structure/fetch-branches-by-area'])
        ],
        'data' => $value ? [$model->branch_id => $value] : []
    ])->label('Branch');
    ?>

    <?php
    $value = !empty($model->team_id) ? $model->team_id : null;
    echo $form->field($model, 'team_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['groups-branch_id'],
            'initialize' => true,
            'initDepends' => ['groups-branch_id'],
            'placeholder' => 'Select Team',
            'url' => \yii\helpers\Url::to(['/structure/fetch-teams-by-branch'])
        ],
        'data' => $value ? [$model->team_id => $value] : []
    ])->label('Team');
    ?>
    <?php
    $value = !empty($model->field_id) ? $model->field_id : null;
    echo $form->field($model, 'field_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['groups-team_id'],
            //'initialize' => true,
            //'initDepends'=>['progressreportdetailssearch-area_id'],
            'placeholder' => 'Select Field',
            'url' => \yii\helpers\Url::to(['/structure/fetch-fields-by-team'])
        ],
        'data' => $value ? [$model->field_id => $value] : []
    ])->label('Field');
    ?>
    <!--
    <? /*= $form->field($model, 'is_locked')->textInput() */ ?>

    <? /*= $form->field($model, 'br_serial')->textInput() */ ?>

-->
    <?= $form->field($model, 'group_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'grp_type')->textInput(['maxlength' => true])/*->dropDownList(\common\components\Helpers\ListHelper::getLists('Group_type'),['prompt'=>'Select Group Type'])*/ ?>

    <?= $form->field($model, 'grp_no')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'br_serial')->textInput(['maxlength' => true]) ?>


    <!--<? /*= $form->field($model, 'created_at')->textInput() */ ?>

    <? /*= $form->field($model, 'updated_at')->textInput() */ ?>-->


    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
