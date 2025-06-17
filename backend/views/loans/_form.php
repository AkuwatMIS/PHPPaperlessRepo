<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\Loans */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loans-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'application_id')->textInput() ?>


    <?= $form->field($model, 'date_disbursed')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'Date Disbursed']
    ]) ?>

    <?= $form->field($model, 'loan_amount')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'disbursed_amount')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'service_charges')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cheque_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inst_amnt')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inst_months')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inst_type')->dropDownList(\common\components\Helpers\LoanHelper::getInstallmentTypes(),['prompt'=>'Select Inst Type']) ?>
<!--
    <?/*= $form->field($model, 'date_disbursed')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'Date Disbursed']
    ]) */?>

    <?/*= $form->field($model, 'cheque_dt')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'Cheaque Date']
    ]) */?>
    -->
<!--
    <?/*= $form->field($model, 'disbursement_id')->textInput() */?>
-->

    <?= $form->field($model, 'project_id')->dropDownList($array['projects'],['prompt'=>'Select Project'])->label('Project') ?>

    <?php
    $value = !empty($model->product_id) ? $model->product->name : null;
    echo $form->field($model, 'product_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['loans-project_id'],
            'initialize' => true,
            'initDepends' => ['loans-project_id'],
            'placeholder' => 'Select Product',
            'url' => Url::to(['/structure/fetch-product-by-project'])
        ],
        'data' => $value ? [$model->product_id => $value] : []
    ])->label('Product');
    ?>

    <?php
    $value = !empty($model->activity_id) ? $model->activity->name : null;
    echo $form->field($model, 'activity_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['loans-product_id'],
            //'initialize' => true,
            //'initDepends'=>['progressreportdetailssearch-area_id'],
            'placeholder' => 'Select Activity',

            'url' => Url::to(['/structure/fetch-activity-by-product'])
        ],
        'data' => $value ? [$model->activity_id => $value] : []
    ])->label('Activity');
    ?>

<!--    --><?php //$form->field($model, 'product_id')->dropDownList($array['products'],['prompt'=>'Select Product'])->label('Product') ?>

<!--    --><?php //$form->field($model, 'activity_id')->dropDownList($array['activities'],['prompt'=>'Select Activity'])->label('Activity') ?>


    <?= $form->field($model, 'group_id')->textInput() ?>


    <?= $form->field($model, 'region_id')->dropDownList(($array['regions']),['prompt'=>'Select Region'])->label('Region') ?>


    <?php
    $value = !empty($model->area_id) ? $model->area_id : null;
    echo $form->field($model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['loans-region_id'],
            'initialize' => true,
            'initDepends' => ['loans-region_id'],
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
            'depends' => ['loans-area_id'],
            'initialize' => true,
            'initDepends' => ['loans-area_id'],
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
            'depends' => ['loans-branch_id'],
            'initialize' => true,
            'initDepends' => ['loans-branch_id'],
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
            'depends' => ['loans-team_id'],
            //'initialize' => true,
            //'initDepends'=>['progressreportdetailssearch-area_id'],
            'placeholder' => 'Select Field',
            'url' => \yii\helpers\Url::to(['/structure/fetch-fields-by-team'])
        ],
        'data' => $value ? [$model->field_id => $value] : []
    ])->label('Field');
    ?>
<!--
    <?/*= $form->field($model, 'loan_expiry')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'Loan Expiry Date']
    ]) */?>

    <?/*= $form->field($model, 'loan_completed_date')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        'value' => strtotime('yyyy-m-d'),
        'options'=>['class'=>'form-control','placeholder'=>'Loan Complete Date']
    ]) */?>

-->
    <?= $form->field($model, 'old_sanc_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'fund_request_id')->textInput() ?>

    <?= $form->field($model, 'br_serial')->textInput() ?>

    <?= $form->field($model, 'sanction_no')->textInput(['maxlength' => true]) ?>
<!--
    <?/*= $form->field($model, 'due')->textInput(['maxlength' => true]) */?>

    <?/*= $form->field($model, 'overdue')->textInput(['maxlength' => true]) */?>

    <?/*= $form->field($model, 'balance')->textInput(['maxlength' => true]) */?>

-->
    <?= $form->field($model, 'status')->dropDownList(\common\components\Helpers\LoanHelper::getDisbursementStatus(),['prompt'=>'Select Loan Status']) ?>

    <?= $form->field($model, 'reject_reason')->textarea(['rows' => 6]) ?>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
