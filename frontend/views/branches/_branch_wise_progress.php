<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Branch-Wise Progress Report';
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
<div  id="demo" style="border:1px solid #d6e9c6;padding:10px;">
<?php $form = ActiveForm::begin([
    'action' => ['branch-wise-progress'],
    'method' => 'get',
]); ?>
<h1>Progress Report(GIS)</h1>
    <br>
<div class="row">

    <div class="col-sm-2">
        <?= $form->field($searchModel, 'date')->widget(\yii\jui\DatePicker::className(), ['dateFormat' => 'y-MM-dd',
            'options' => ['class' => 'form-control','placeholder'=>'Enter Report Date'],
        ])->label('Report Date'); ?>

    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <?= Html::submitButton('Export', ['class' => 'btn btn-primary pull-right', 'name' => 'export', 'value' > 'export','style'=>'margin-top:22px;width:100px']) ?>
        </div>
    </div>
</div>
<input type="hidden" name="export" value="export">


<?php ActiveForm::end(); ?>
</div>
    </div>
</div>

