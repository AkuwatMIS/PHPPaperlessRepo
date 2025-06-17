<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */
/* @var $form yii\widgets\ActiveForm */
$js = "
";
$this->registerJs($js);

?>
<div class="modal fade" id="updatePledgeModal" tabindex="-1" role="dialog" aria-labelledby="updatePledgeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePledgeModalLabel">Update Pledge Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'pledge-form',
                    'action' => ['update-pledge'], // The action to handle the form submission
                ]); ?>

                <?= Html::hiddenInput('id', '', ['id' => 'pledge-id']); ?>
                <?= $form->field($model, 'is_pledged')
                    ->dropDownList(
                        ['1' => 'Pledged'],
                        ['prompt' => 'Select Status'] // Add a prompt for optional selection
                    ) ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

