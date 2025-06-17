<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\StructureHelper;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid collapse border-0" id="demo">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-3">
            <?php
            //$regions = ArrayHelper::map(Regions::find()->asArray()->all(), 'id', 'name');
            echo $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['branchessearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['branchessearch-region_id'],
                    'placeholder' => 'Select Area',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->id) ? $model->id : null;
            echo $form->field($model, 'id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['branchessearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['branchessearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->id => $value] : []
            ])->label('Branch');
            ?>
        </div
    </div>
    <div class="form-group" style="margin-top: 20px">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div></div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
            class="glyphicon glyphicon-search"></span> Advanced Search</a>
