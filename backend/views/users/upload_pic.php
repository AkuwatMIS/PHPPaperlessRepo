<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\UsersCopy */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="users-form">
    <?php
    Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../../'));
    ?>
    <?php $form = ActiveForm::begin(); ?>
    <?php $role = !empty($model->role->item_name) ? $model->role->item_name : null; ?>
    <h1><?php echo $role?></h1>
    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?php if(!$model->isNewRecord){?>
        <?php echo \yii\helpers\Html::img(\common\components\Helpers\ImageHelper::getAttachmentPath() .'/uploads/' . '/users/'.$model->image, ['alt' => 'noimage.png','class' => 'online' ,'style'=>' border-radius: 8px;width:200px']); ?>
    <?php }?>
    <br>
    <br>
    <?= $form->field($model, 'image')->fileInput()->label(false) ?>
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
