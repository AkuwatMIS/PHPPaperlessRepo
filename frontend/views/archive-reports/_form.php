<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model common\models\ArchiveReports */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin(['validateOnSubmit' => false]); ?>
<?= $form->errorSummary($model); ?>
<div class="row">
    <div class="col-sm-4">
        <?= $form->field($model, 'report_name')->dropDownList($report_names) ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'source')->dropDownList($sources, ['prompt' => '']) ?>
    </div>
    <div class="col-sm-4">
        <?php
        echo $form->field($model, 'date_filter')->widget(DateRangePicker::classname(), [
            'convertFormat' => true,
            'options' => ['class' => 'form-control input-sm', 'placeholder' => 'Date'],
            'pluginOptions' => [
                'locale' => [
                    'format' => 'Y-m-d',
                ]
            ]
        ])->label("Date");
        ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">

        <?php
        $regions['0'] = 'All';
        ksort($regions);
        echo $form->field($model, 'region_id')->dropDownList($regions)->label('Region') ?>
    </div>
    <div class="col-sm-4">
        <?php
        $value = !empty($model->area_id) ? $model->area->name : null;
        echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['archivereports-region_id'],
                'initialize' => true,
                'initDepends' => ['archivereports-region_id'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/structure/fetch-area-by-region'])
            ],
            'data' => $value ? [$model->area_id => $value] : []
        ])->label('Area');
        ?>
    </div>
    <div class="col-sm-4">
        <?php
        $value = !empty($model->branch_id) ? $model->branch->name : null;
        echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['archivereports-area_id'],
                'initialize' => true,
                'initDepends' => ['archivereports-region_id'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/structure/fetch-branch-by-area'])
            ],
            'data' => $value ? [$model->branch_id => $value] : []
        ])->label('Branch');
        ?>
    </div>
</div>
<div class="row">

    <div class="col-sm-4">
        <?php
        $value = !empty($model->team_id) ? $model->team->name : null;
        echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['archivereports-branch_id'],
                'initialize' => true,
                'initDepends' => ['archivereports-branch_id'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/structure/fetch-team-by-branch'])
            ],
            'data' => $value ? [$model->team_id => $value] : []
        ])->label('Team');
        ?>
    </div>
    <div class="col-sm-4">
        <?php
        $value = !empty($model->field_id) ? $model->field->name : null;
        echo $form->field($model, 'field_id')->widget(DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['archivereports-team_id'],
                'initialize' => true,
                'initDepends' => ['archivereports-team_id'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/structure/fetch-field-by-team'])
            ],
            'data' => $value ? [$model->field_id => $value] : []
        ])->label('Field');
        ?>
    </div>
    <div class="col-sm-4">
        <?php
        $projects['0'] = 'All';
        ksort($projects);
        echo $form->field($model, 'project_id')->dropDownList($projects)->label('Projects') ?>
    </div>

</div>

<div class="row">
    <div class="col-sm-4">
        <?= $form->field($model, 'product_id')->dropDownList(array_merge(array('0' => 'All'), $products))->label('Products') ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'activity_id')->dropDownList(array_merge(array('0' => 'All'), $activities))->label('Activities') ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'gender')->dropDownList(array_merge(array('0' => 'All'), \common\components\Helpers\ListHelper::getLists('gender')))->label('Gender') ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?= $form->field($model, 'branch_codes')->textarea() ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?= $form->errorSummary($model); ?>
        <?php if (!Yii::$app->request->isAjax) { ?>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
    

