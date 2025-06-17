<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */

$this->title = 'Approve User Transfer: ' . $model->type;
$this->params['breadcrumbs'][] = ['label' => 'User Transfer', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->type, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$js = "

    $(document).ready(function(){
        $('.field-usertransfers-recommend_remarks').show();
        $('.field-usertransfers-reject_reason').hide();
        $('#usertransfers-status').change(function(){
            var selected_value =  $('#usertransfers-status').val();
            if(selected_value == '0'){
                   $('.field-usertransfers-recommend_remarks').show();
                   $('.field-usertransfers-reject_reason').hide();
            }else if(selected_value == '2'){
                   $('.field-usertransfers-reject_reason').show();
                   $('.field-usertransfers-recommend_remarks').hide();
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

            <?= $form->field($model, 'status')->dropDownList(['0' => 'Accept', '2' => 'Rejected'], ['prompt' => 'Select...']) ?>
            <?= $form->field($model, 'recommend_remarks')->textarea(['maxlength' => true])->label('Accept Remarks') ?>
            <?= $form->field($model, 'reject_reason')->textarea(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Recommend', ['class' => 'btn btn-primary']) ?>
            </div>
    
        <?php ActiveForm::end(); ?>
    
    </div>

</div>
