<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MobilePermissions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mobile-permissions-form">

    <?= Html::beginForm(); ?>
    <table class="table">
        <tbody>
        <?php echo Html::checkboxList('Permission', $mobile_permissions, $mobile_screens, ['item'=>function ($index, $label, $name, $checked, $value){
            return Html::checkbox($name, $checked, [
                'value' => $value,
                'label' => $label,
                'class' => 'any class',
                'labelOptions' => [

                    'class' => 'col-md-4',

                ],
            ]);
        }]);?>
        </tbody>
    </table>
    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::submitButton( 'Create', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php Html::endForm(); ?>
</div>
