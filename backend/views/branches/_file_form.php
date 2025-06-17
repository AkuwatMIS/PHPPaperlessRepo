<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Branches */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="branches-form">

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data','method' => 'POST']]); ?>
        <div class="form-group">
            <label for="exampleInputEmail1">FIle</label>
            <input type="file" class="form-control" id="file" name="file"  placeholder="Upload Bulk File">
        </div>
        <?php if (Yii::$app->request->isAjax){ ?>
            <div class="form-group">
                <button type="submit" class="btn btn-primary pull-right">Upload</button>
            </div>
        <?php } ?>
        <?php ActiveForm::end(); ?>
    </div>
    
</div>
