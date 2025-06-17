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
var member_cnic = \"".$model->applications[0]->member->cnic."\";
if(guarantor2==guarantor1){
alert('Guarantos CNIC are same');
}
else if(member_cnic==guarantor1 || member_cnic==guarantor2){
alert('Guarantor and member CNIC are same');
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
                                        <h4>Update Guarantors</h4>
                                    </div>
                           </div>
                    </div>
            </header>
        <div class="box-typical box-typical-padding">
                <table id="table-edit" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                                <th width="1">#</th>
                                <th>Group No</th>
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
                                <td><?= $model->grp_no ?></td>
                                <td><?= $model->applications[0]->application_no ?></td>
                                <td><?= $model->applications[0]->member->full_name ?></td>
                                <td><?= $model->applications[0]->member->parentage ?></td>
                                <td><?= $model->applications[0]->member->cnic ?></td>
                                <td><?= number_format($model->applications[0]->req_amount) ?></td>
                                <td><?= isset($model->applications[0]->activity->name) ? $model->applications[0]->activity->name : '' ?></td>
                                <td><?= $model->applications[0]->product->name ?></td>
                            </tr>
                        <?php  ?>
                        </tbody>
                    </table>
                <br><br>
                <div class="groups-form">
                        <?php $id = ['0', '1'] ?>
                        <?php $form = ActiveForm::begin(['id'=>'guarantors-form'
                            ]); ?>
                        <div class="row">
                                <?php foreach ($guarantors as $guarantor) { ?>
                                        <div class="col-sm-6">
                                                <h3>Add Guarantor No.<?php echo $i + 1 ?></h3>
                                                <?= $form->field($guarantor, "[{$i}]id")->hiddenInput()->label(false) ?>
                                                <?= $form->field($guarantor, "[{$i}]name")->textInput(['maxlength' => true]) ?>

                                                <?= $form->field($guarantor, "[{$i}]parentage")->textInput(['maxlength' => true]) ?>

                                               <?= $form->field($guarantor,"[{$i}]cnic")->widget(\yii\widgets\MaskedInput::className(), [
                                                            'mask' => '99999-9999999-9',
                                                        ])->textInput(['maxlength' => true, 'placeholder' => 'CNIC']) ?>
                                                <?php if(in_array( $model->applications[0]->project_id,\common\components\Helpers\StructureHelper::trancheProjects())){?>
                                                    <?= $form->field($guarantor, "[{$i}]marital_status",($i==0)?['enableClientValidation' => true]:['enableClientValidation' => false])->dropDownList(\common\components\Helpers\MemberHelper::getMaritalStatus(), ['prompt' => 'Select Marital Status','required']) ?>
                                                    <?= $form->field($guarantor, "[{$i}]source_of_income",($i==0)?['enableClientValidation' => true]:['enableClientValidation' => false])->dropDownList(\common\components\Helpers\MemberHelper::getSourceOfIncome(), ['prompt' => 'Select Source of Income','required'])  ?>
                                                    <?= $form->field($guarantor, "[{$i}]monthly_income",($i==0)?['enableClientValidation' => true]:['enableClientValidation' => false])->textInput(['maxlength' => true,'type'=>'number','min'=>'0','required']) ?>
                                                    <?= $form->field($guarantor, "[{$i}]guarantor_relation",($i==0)?['enableClientValidation' => true]:['enableClientValidation' => false])->dropDownList(\common\components\Helpers\MemberHelper::getGuarantorRelation(),['prompt'=>'Select Realtionship','required']) ?>
                                                <?php }?>
                                                <?= $form->field($guarantor, "[{$i}]phone")->widget(\borales\extensions\phoneInput\PhoneInput::className(), [
                                                        'jsOptions' => [
                                                                'preferredCountries' => ['pk'],
                                                            ]])->widget(\yii\widgets\MaskedInput::className(), [
                                                        'mask' => '999999999999',
                                                    ])->textInput(['maxlength' => true, 'placeholder' => '923011234567'])->label('Phone'); ?>
                                                <?= $form->field($guarantor, "[{$i}]address")->textArea() ?>
                                            </div>
                                    <?php $i++;} ?>
                            </div>

                        <?php if (!Yii::$app->request->isAjax) { ?>
                                <div class="form-group">
                                        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'guarantor-save']) ?>
                                    </div>
                            <?php } ?>

                        <?php ActiveForm::end(); ?>

                    </div>
            </div>
    </div>
