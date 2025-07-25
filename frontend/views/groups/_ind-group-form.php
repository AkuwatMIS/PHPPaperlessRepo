<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Groups */
/* @var $form yii\widgets\ActiveForm */
$js="

var form = document.getElementById('guarantors-form');
document.getElementById('guarantor-save').addEventListener('click', function (event) {
var guarantor1=$('#guarantors-0-cnic').val();
var guarantor2=$('#guarantors-1-cnic').val();
var member_cnic = \"".$application->member->cnic."\";
if(guarantor2==guarantor1){
alert('Guarantos CNIC are same');
  event.preventDefault();

}
else if(member_cnic==guarantor1 || member_cnic==guarantor2){
alert('Guarantor and member CNIC are same');
  event.preventDefault();

}

});
";
$this->registerJs($js);
?>
<style>
    .intl-tel-input {
        display:block
    }
</style>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Add Guarantors</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <table id="table-edit" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th width="1">#</th>
                <th>Application No</th>
                <th>Name</th>
                <th>Parentage</th>
                <th>CNIC</th>
                <th>Req Amount</th>
                <th>Activity</th>
                <th>Product</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0; ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= $application->application_no ?></td>
                <td><?= $application->member->full_name ?></td>
                <td><?= $application->member->parentage ?></td>
                <td><?= $application->member->cnic ?></td>
                <td><?= number_format($application->req_amount) ?></td>
                <td><?= isset($application->activity->name) ? $application->activity->name : '' ?></td>
                <td><?= $application->product->name ?></td>
            </tr>
            <?php $i++; ?>
            </tbody>
        </table>
        <br><br>
        <div class="groups-form">
            <?php $i = 0; ?>
            <?php $form = ActiveForm::begin(['id' => 'guarantors-form']); ?>

            <div class="row">
                <?php foreach ($model as $index => $guarantorModel) { ?>
                    <?= $form->errorSummary($guarantorModel) ?>
                    <div class="col-sm-6">
                        <h3>Add Guarantor No. <?= $index + 1 ?></h3>

                        <?= $form->field($guarantorModel, "[{$index}]name", ($index == 0) ? ['enableClientValidation' => true] : ['enableClientValidation' => false])->textInput(['maxlength' => true]) ?>

                        <?= $form->field($guarantorModel, "[{$index}]parentage", ($index == 0) ? ['enableClientValidation' => true] : ['enableClientValidation' => false])->textInput(['maxlength' => true]) ?>

                        <?= $form->field($guarantorModel, "[{$index}]cnic", ($index == 0) ? ['enableClientValidation' => true] : ['enableClientValidation' => false])->widget(\yii\widgets\MaskedInput::className(), [
                            'mask' => '99999-9999999-9',
                        ])->textInput(['maxlength' => true, 'placeholder' => 'CNIC']) ?>

                        <?php if (in_array($application->project_id, \common\components\Helpers\StructureHelper::trancheProjects())) { ?>
                            <?= $form->field($guarantorModel, "[{$index}]marital_status", ($index == 0) ? ['enableClientValidation' => true] : ['enableClientValidation' => false])->dropDownList(\common\components\Helpers\MemberHelper::getMaritalStatus(), ['required' => ($index == 0) ? true : false, 'prompt' => 'Select Marital Status']) ?>

                            <?= $form->field($guarantorModel, "[{$index}]source_of_income", ($index == 0) ? ['enableClientValidation' => true] : ['enableClientValidation' => false])->dropDownList(\common\components\Helpers\MemberHelper::getSourceOfIncome(), ['required' => ($index == 0) ? true : false, 'prompt' => 'Select Source of Income']) ?>

                            <?= $form->field($guarantorModel, "[{$index}]monthly_income", ($index == 0) ? ['enableClientValidation' => true] : ['enableClientValidation' => false])->textInput(['maxlength' => true, 'type' => 'number', 'min' => '0', 'required' => ($index == 0) ? true : false]) ?>

                            <?= $form->field($guarantorModel, "[{$index}]guarantor_relation", ($index == 0) ? ['enableClientValidation' => true] : ['enableClientValidation' => false])->dropDownList(\common\components\Helpers\MemberHelper::getGuarantorRelation(), ['required' => ($index == 0) ? true : false, 'prompt' => 'Select Relationship']) ?>
                        <?php } ?>

                        <?= $form->field($guarantorModel, "[{$index}]phone", ($index == 0) ? ['enableClientValidation' => true] : ['enableClientValidation' => false])->widget(\borales\extensions\phoneInput\PhoneInput::className(), [
                            'jsOptions' => [
                                'preferredCountries' => ['pk'],
                            ]
                        ])->widget(\yii\widgets\MaskedInput::className(), [
                            'mask' => '999999999999',
                        ])->textInput(['maxlength' => true, 'placeholder' => '923011234567'])->label('Phone'); ?>

                        <?= $form->field($guarantorModel, "[{$index}]address", ($index == 0) ? ['enableClientValidation' => true] : ['enableClientValidation' => false])->textArea() ?>
                    </div>
                    <?php $i++; } ?>
            </div>

            <?= $form->field($group, 'group_name')->hiddenInput(['maxlength' => true])->label(false) ?>
            <?= $form->field($application, 'id')->hiddenInput(['maxlength' => true])->label(false) ?>

            <?php if (!Yii::$app->request->isAjax) { ?>
                <div class="form-group">
                    <?= Html::submitButton($model[0]->isNewRecord ? 'Create' : 'Update', ['class' => $model[0]->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'guarantor-save']) ?>
                </div>
            <?php } ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

