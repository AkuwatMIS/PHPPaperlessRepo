<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectFundDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-fund-detail-form">


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fund_id')->dropDownList($fundLine,
        [ 'class'=>'form-control','readonly' => 'readonly'])->label("Fund Line") ?>

    <?= $form->field($model, 'batch_no')->textInput(['disabled' => true])->label('Batch No') ?>

    <?= $form->field($model, 'fund_batch_amount')->textInput(['disabled' => true])->label('Batch Amount') ?>

    <?= $form->field($model, 'disbursement_source')->textInput(['disabled' => true])->label('Disbursement Source') ?>


    <?= $form->field($model, 'txn_mode')->dropDownList([

                    'cheque' => 'Cheque',
                    'cash' => 'Cash',
                    'online' => 'Online Transfer',
            ],
            [ 'class'=>'form-control','prompt' => 'Please Select Mode'])->label("Mode") ?>

    <?= $form->field($model, 'txn_no')->textInput()->label('Bank Reference Number') ?>

        <?= $form->field($model, "received_date")->widget(\yii\jui\DatePicker::className(),[
            'dateFormat' => 'yyyy-MM-dd',

            'options' => ['class' => 'form-control', 'placeholder' => 'Date',
                'readonly' => 'readonly',
            ],
            'clientOptions'=>[
                'changeMonth' => false,
                'changeYear' => false,
                'minDate' => (string) date('Y-m-d', strtotime('- 820 days')),
//                'minDate' => '',
            'maxDate' => (string) date('Y-m-d'),
            ]
        ])->label('Date');  ?>


  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
