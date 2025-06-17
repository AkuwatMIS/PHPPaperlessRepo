<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\UsersCopy */
/* @var $form yii\widgets\ActiveForm */
$js = '$(document).ready(function () {
        $("#designation").change(function(){
            var a=$(\'#designation\').val();
            if(a==\'LO\'){
                $(\'#extra_fields\').show();
                $(\'#multi_branches\').hide();

            }
            else {
                $(\'#extra_fields\').hide();
                $(\'#multi_branches\').show();
                $(\'#branch_id\').val("");
                $(\'#team_id\').val("");
                $(\'#field_id\').val("");

            }
        });
    });';
$this->registerJs($js);
?>
<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>
    <h4>Sructural Mapping</h4>
    <div class="row">
        <div class="col-sm-4">
            <label>Designation</label>
            <?= Html::dropDownList('designation', null, $array['designations'], array('class' => 'form-control designation', 'initialize' => false, 'id' => 'designation', 'prompt' => 'SELECT Designation', 'id' => 'designation')) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($array['model_userwithproject'], 'project_ids')->widget(\kartik\select2\Select2::classname(), [
                'data' => $array['projects'],
                'options' => ['placeholder' => 'Select Project', 'multiple' => true,],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,

                ],
            ])->label("Projects"); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($array['model_userwithregions'], 'region_ids')->widget(\kartik\select2\Select2::classname(), [
                'data' => $array['regions'],
                'options' => ['placeholder' => 'Select Region', 'multiple' => true,],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,

                ],
            ])->label("Regions"); ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($array['model_userwithareas'], 'area_ids')->widget(\kartik\select2\Select2::classname(), [
                'data' => $array['areas'],
                'id' => 'areas',
                'options' => ['placeholder' => 'Select Areas', 'multiple' => true,],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,

                ],
            ])->label("Areas"); ?>
        </div>
        <div class="col-sm-4">
            <div id="multi_branches">
                <?= $form->field($array['model_userwithbranches'], 'branch_ids')->widget(\kartik\select2\Select2::classname(), [
                    'data' => $array['branches'],
                    'options' => ['placeholder' => 'Select Branches', 'multiple' => true,],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true,

                    ],
                ])->label("Branches"); ?>
            </div>
        </div>
    </div>
    <div style="display: none" id="extra_fields">
        <label>Branch</label>


        <?= Html::dropDownList('branch_id', null, $array['branches'], array('class' => 'form-control branch_id', 'initialize' => true, 'id' => 'branch_id', 'prompt' => 'SELECT Branch', 'id' => 'branch_id')) ?>

        <label>Team</label>

        <?php echo \kartik\depdrop\DepDrop::widget([
            'name' => 'team_id',
            'id' => 'team_id',
            'pluginOptions' => ['name' => 'team_id', 'id' => 'team_id',
                'depends' => ['branch_id'],
                'placeholder' => 'Select Team',
                'url' => \yii\helpers\Url::to(['/structure/fetch-team-by-branch'])
            ]]); ?>

        <label>Field</label>

        <?php echo \kartik\depdrop\DepDrop::widget([
            'name' => 'field_id',
            'id' => 'field_id',
            'pluginOptions' => ['name' => 'field_id', 'id' => 'field_id',
                'depends' => ['team_id'],
                'placeholder' => 'Select Field',
                'url' => \yii\helpers\Url::to(['/structure/fetch-field-by-team'])
            ]]); ?>

    </div>

    <h4>Personal Information</h4>

    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'father_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'alternate_email')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'joining_date')->textInput() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'city_id')->textInput()->dropDownList($array['cities']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'emp_code')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
