<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AgingReports */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="aging-reports-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList(array('due'=>'OLP Aging','overdue'=>'Overdue Aging')) ?>

    <?= $form->field($model, "start_month")->widget(\yii\jui\DatePicker::className(),[
        'dateFormat' => 'yyyy-MM',
        'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Month',
            'readonly' => 'readonly'

        ],
        'clientOptions'=>[
            'changeMonth' => true,
            'changeYear' => true,
        ]
    ])->label('Month');  ?>

  
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
