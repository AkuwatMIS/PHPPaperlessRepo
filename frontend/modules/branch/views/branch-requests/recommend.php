<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */

$this->title = 'Recommend Branch Requests: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branch Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$js = "

    $(document).ready(function(){
        $('.field-branchrequests-recommended_remarks').hide();
        $('.field-branchrequests-reject_reason').hide();
        $('#branchrequests-status').change(function(){
            var selected_value =  $('#branchrequests-status').val();
            if(selected_value == 'recommended'){
                   $('.field-branchrequests-recommended_remarks').show();
                   $('.field-branchrequests-reject_reason').hide();
            }else if(selected_value == 'rejected'){
                   $('.field-branchrequests-reject_reason').show();
                   $('.field-branchrequests-remarks').hide();
            }
        });
    });

";

$this->registerJs($js);
?>
<div class="container-fluid">

    <h5><?= Html::encode($this->title) ?></h5>

    <div class="branch-requests-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'status')->dropDownList(['recommended' => 'Recommended', 'rejected' => 'Rejected'], ['prompt' => 'Select...']) ?>
        <?= $form->field($model, 'recommended_remarks')->textInput(['maxlength' => true])->label('Reviewed Remarks') ?>
        <?= $form->field($model, 'reject_reason')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'projects')->widget(Select2::classname(), [
            'data' => $projects,
            'options' => ['placeholder' => 'Select Project'],
            //'size' => Select2::SMALL,
            'pluginOptions' => [
                'multiple' =>true,
                'allowClear' => true
            ],
        ])->label("Projects");
        ?>

            <?php
            //For hidden fields errors
            if ($model->hasErrors() && !empty($model->getErrors())) {
                ?>
                <div class="form-group has-error">
                    <?php
                    $errors = implode(" ", array_map(function ($arr) {
                        return implode(" ", $arr);
                    }, $model->getErrors()));
                    echo "<div class='help-block'><h4>More Errors</h4></div>";
                    echo "<div class='help-block'>" . $errors . "</div>";
                    ?>
                </div>
                <?php
            };
            ?>

            <div class="form-group">
                <?= Html::submitButton('Recommend', ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>

    </div>

</div>
