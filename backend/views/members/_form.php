<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use common\components\Helpers\MemberHelper;
use borales\extensions\phoneInput\PhoneInput;
/* @var $this yii\web\View */
/* @var $model common\models\Members */
/* @var $form yii\widgets\ActiveForm */
$js = '

$(document).ready(function(){
    $(\'#membersphone-0-phone_type\').parent(\'.field-membersphone-0-phone_type\').hide();
});
$(document).ready(function(){
    $(\'#membersphone-1-phone_type\').parent(\'.field-membersphone-1-phone_type\').hide();
});
/*$(document).ready(function(){
    $(".field-members-disability").hide();
    //$(".field-members-nature").hide();
    $(".field-members-disability_type").hide();
    $("#members-is_disable").change(function(){
        var selected_value =  $("#members-is_disable").val();
        if(selected_value == 1){
            $(".field-members-disability").show();
            //$(".field-members-nature").show();
            $(".field-members-disability_type").show();
        }else{
            $(".field-members-disability").hide();
            //$(".field-members-nature").hide();
            $(".field-members-disability_type").hide();
        }
    });
});*/


';
$js.="
$(document).ready(function(){
    var form = document.getElementById(\"w0\");
    document.getElementById(\"member-submit\").addEventListener(\"click\", function (event) {
        if($('#members-cnic').val()==$('#members-family_member_cnic').val()){
              alert(\"Member and family member CNIC is same\");
              event.preventDefault();
        }
    });

$('#members-cnic').change(function(){
var cnic=$('#members-cnic').val();
$.ajax({

        type: \"POST\",
        url: '/members/cnic-check?cnic='+cnic,
        success: function(data){
            var obj = $.parseJSON(data);
            if(obj.status_type=='success'){
            
             $('.exist').text('Member Already Exists against this CNIC.Update Member Information'); 
             $('#member-submit').removeAttr('disabled');
             $('.alert-success').show();
             $(\".alert-success\").delay(2500).slideUp(1000);
             $('#w0').attr('action', '/members/update?id='+obj.data.id);
                       $('#member-submit').html('Update');
                       $('#member-submit').removeClass('btn btn-success').addClass('btn btn-primary')
                       $('#members-parentage').prop(\"value\",obj.data.parentage);
                       $('#members-full_name').prop(\"value\",obj.data.full_name);
                       $('#members-parentage_type').prop(\"value\",obj.data.parentage_type);
                        
                        var field = $('<option value=\"'+obj.data.field_id+'\">Field</option>');
                        $(\"#members-field_id\").html('');
                        $('#members-field_id').append(field);
                        
                        var team = $('<option value=\"'+obj.data.team_id+'\">Team</option>');
                        $(\"#members-team_id\").html('');
                        $('#members-team_id').append(team);

                       $('#members-branch_id').prop(\"value\",obj.data.branch_id);
                       $('#members-team_id').prop(\"value\",obj.data.team_id);
                       $('#members-field_id').prop(\"value\",obj.data.field_id);
                       $('#members-gender').prop(\"value\",obj.data.gender);
                       $('#members-education').prop(\"value\",obj.data.education);
                       $('#members-marital_status').prop(\"value\",obj.data.marital_status);
                       $('#members-family_no').prop(\"value\",obj.data.family_no);
                       $('#members-family_member_name').prop(\"value\",obj.data.family_member_name);
                       $('#members-family_member_cnic').prop(\"value\",obj.data.family_member_cnic);
                       $('#members-religion').prop(\"value\",obj.data.religion);
                       $('#members-current_address').prop(\"value\",obj.data.current_address);
                       $('#members-is_disable').prop(\"value\",obj.data.is_disable);
                       $('#membersaddress-0-address').prop(\"value\",obj.address.business.address);
                       $('#membersaddress-1-address').prop(\"value\",obj.address.home.address);
                       $('#membersaddress-0-address_type').prop(\"value\",obj.address.business.address_type);
                       $('#membersaddress-1-address_type').prop(\"value\",obj.address.home.address_type);
                       $('#members-dob').prop(\"value\",obj.data.dob);
                       $('#members-region_id').prop(\"value\",obj.data.region_id);
                       $('#members-area_id').prop(\"value\",obj.data.area_id);
                       $('#members-id').prop(\"value\",obj.data.id);
                       $('#membersphone-0-phone').prop(\"value\",obj.phone.mobile.phone);
                       $('#membersphone-1-phone').prop(\"value\",obj.phone.landline.phone);
                       $('#membersphone-0-phone_type').prop(\"value\",obj.phone.mobile.phone_type);
                       $('#membersphone-1-phone_type').prop(\"value\",obj.phone.landline.phone_type);
                       
                       
            }else if(obj.status_type=='error'){
            $('.blacklist').text('Member Exists in Blacklist. You cannot create member against this CNIC'); 
            $('#member-submit').prop(\"disabled\",\"true\");
             $('.alert-danger').show();
             $(\".alert-danger\").delay(2500).slideUp(1000);
                       $('#w0').attr('action', '/members/create');
                       $('#member-submit').html('Create');
                       $('#member-submit').removeClass('btn btn-primary').addClass('btn btn-success')
                       $('#members-parentage').prop(\"value\",\"\");
                       $('#members-full_name').prop(\"value\",\"\");
                       $('#members-parentage_type').prop(\"value\",\"\");
                       $(\"#members-field_id\").html('');
                       $(\"#members-team_id\").html('');
                       $('#members-branch_id').prop(\"value\",\"\");
                       $('#members-team_id').prop(\"value\",\"\");
                       $('#members-field_id').prop(\"value\",\"\");
                       $('#members-gender').prop(\"value\",\"\");
                       $('#members-education').prop(\"value\",\"\");
                       $('#members-marital_status').prop(\"value\",\"\");
                       $('#members-family_no').prop(\"value\",\"\");
                       $('#members-family_member_name').prop(\"value\",\"\");
                       $('#members-family_member_cnic').prop(\"value\",\"\");
                       $('#members-religion').prop(\"value\",\"\");
                       $('#members-current_address').prop(\"value\",\"\");
                       $('#members-is_disable').prop(\"value\",\"\");
                       $('#membersaddress-0-address').prop(\"value\",\"\");
                       $('#membersaddress-1-address').prop(\"value\",\"\");
                       $('#membersaddress-0-address_type').prop(\"value\",\"home\");
                       $('#membersaddress-1-address_type').prop(\"value\",\"business\");
                       $('#members-dob').prop(\"value\",\"\");
                       $('#members-region_id').prop(\"value\",\"\");
                       $('#members-area_id').prop(\"value\",\"\");
                       $('#members-id').prop(\"value\",\"\");
                       $('#membersphone-0-phone').prop(\"value\",\"\");
                       $('#membersphone-1-phone').prop(\"value\",\"\");
                       $('#membersphone-0-phone_type').prop(\"value\",\"mobile\");
                       $('#membersphone-1-phone_type').prop(\"value\",\"phone\");
                      
            }
            else{
            $('.alert').hide();
                       $('#member-submit').removeAttr('disabled');
                       $('#w0').attr('action', '/members/create');
                       $('#member-submit').html('Create');
                       $('#member-submit').removeClass('btn btn-primary').addClass('btn btn-success')
                       $('#members-parentage').prop(\"value\",\"\");
                       $('#members-full_name').prop(\"value\",\"\");
                       $('#members-parentage_type').prop(\"value\",\"\");
                       $(\"#members-field_id\").html('');
                       $(\"#members-team_id\").html('');
                       $('#members-branch_id').prop(\"value\",\"\");
                       $('#members-team_id').prop(\"value\",\"\");
                       $('#members-field_id').prop(\"value\",\"\");
                       $('#members-gender').prop(\"value\",\"\");
                       $('#members-education').prop(\"value\",\"\");
                       $('#members-marital_status').prop(\"value\",\"\");
                       $('#members-family_no').prop(\"value\",\"\");
                       $('#members-family_member_name').prop(\"value\",\"\");
                       $('#members-family_member_cnic').prop(\"value\",\"\");
                       $('#members-religion').prop(\"value\",\"\");
                       $('#members-current_address').prop(\"value\",\"\");
                       $('#members-is_disable').prop(\"value\",\"\");
                       $('#membersaddress-0-address').prop(\"value\",\"\");
                       $('#membersaddress-1-address').prop(\"value\",\"\");
                       $('#membersaddress-0-address_type').prop(\"value\",\"home\");
                       $('#membersaddress-1-address_type').prop(\"value\",\"business\");
                       $('#members-dob').prop(\"value\",\"\");
                       $('#members-region_id').prop(\"value\",\"\");
                       $('#members-area_id').prop(\"value\",\"\");
                       $('#members-id').prop(\"value\",\"\");
                       $('#membersphone-0-phone').prop(\"value\",\"\");
                       $('#membersphone-1-phone').prop(\"value\",\"\");
                       $('#membersphone-0-phone_type').prop(\"value\",\"mobile\");
                       $('#membersphone-1-phone_type').prop(\"value\",\"phone\");
                       

            }
        }
    });
    });
});
";
$this->registerJs($js);
?>

<div class="members-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'branch_id')->dropDownList($branches, ['prompt' => 'Select Branch'])->label('Branch');?>
    <?php
    $value = !empty($model->team_id) ? $model->team->name : null;
    echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
    'pluginOptions' => [
    'depends' => ['members-branch_id'],
    'initialize' => true,
    'initDepends' => ['members-branch_id'],
    'placeholder' => 'Select Team',
    'url' => Url::to(['/structure/fetch-team-by-branch'])
    ],
    'data' => $value ? [$model->team_id => $value] : []
    ])->label('Team');
    ?>
    <?php
    $value = !empty($model->field_id) ? $model->field->name : null;
    echo $form->field($model, 'field_id')->widget(DepDrop::classname(), [
        'pluginOptions' => [
            'depends' => ['members-team_id'],
            //'initialize' => true,
            //'initDepends'=>['progressreportdetailssearch-area_id'],
            'placeholder' => 'Select Field',
            'url' => Url::to(['/structure/fetch-field-by-team'])
        ],
        'data' => $value ? [$model->field_id => $value] : []
    ])->label('Field');
    ?>
    <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parentage_type')->dropDownList(\common\components\Helpers\MemberHelper::getParentageType(),['prompt'=>'Select Parentage']) ?>
    <?= $form->field($model, 'parentage')->textInput(['maxlength' => true, 'placeholder' => 'Parentage', 'class' => 'form-control form-control-sm'])->label('Parentage Name') ?>

    <?= $form->field($model, 'cnic')->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '99999-9999999-9',
    ])->textInput(['maxlength' => true, 'placeholder' => 'CNIC', 'class' => 'form-control form-control-sm']) ?>

    <?= $form->field($model, 'gender')->dropDownList(\common\components\Helpers\MemberHelper::getGender(),['prompt'=>'Select Gender']) ?>

    <?= $form->field($model, 'dob')->widget(\yii\jui\DatePicker::className(),[
        'dateFormat'=>'yyyy-MM-dd',
        // 'value' => date('d-M-Y', strtotime('+2 days')),
        'options'=>['class'=>'form-control','placeholder'=>'Date Of Birth']
    ])->label('Date Of Birth')  ?>
    <?= $form->field($model, 'education')->textInput(['maxlength' => true])->dropDownList(\common\components\Helpers\MemberHelper::getEducation(),['prompt'=>'Select Education']) ?>

    <?= $form->field($model, 'marital_status')->textInput(['maxlength' => true])->dropDownList(\common\components\Helpers\MemberHelper::getMaritalStatus(),['prompt'=>'Select Marital Status']) ?>

    <?= $form->field($model, 'family_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'family_member_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'family_member_cnic')->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '99999-9999999-9',
    ])->textInput(['placeholder' => 'Family Member CNC']) ?>

    <?= $form->field($model, 'religion')->textInput(['maxlength' => true])->dropDownList(\common\components\Helpers\MemberHelper::getReligions(),['prompt'=>'Select Religion']) ?>
    <?php $one = 0;
    $two = 1; ?>
    <?= $form->field($membersPhone[0], "[{$one}]phone_type", ['enableClientValidation' => false])->hiddenInput(['value' => 'mobile'])->label(false); ?>
    <?= $form->field($membersPhone[0], "[{$one}]phone" /*['enableClientValidation' => false]*/)->widget(PhoneInput::className(), [
        'jsOptions' => [
            'preferredCountries' => ['pk'],
        ]])->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '999999999999',
    ])->textInput(['maxlength' => true, 'placeholder' => '923011234567', 'class' => 'form-control form-control-sm'])->label('Mobile'); ?>
    <?= $form->field($membersPhone[1], "[{$two}]phone_type", ['enableClientValidation' => false])->hiddenInput(['value' => 'phone'])->label(false); ?>

    <?= $form->field($membersPhone[1], "[{$two}]phone" /*['enableClientValidation' => false]*/)->widget(PhoneInput::className(), [
        'jsOptions' => [
            'preferredCountries' => ['pk'],
        ]])->widget(\yii\widgets\MaskedInput::className(), [
        'mask' => '999999999999',
    ])->textInput(['maxlength' => true, 'placeholder' => '924231234567', 'class' => 'form-control form-control-sm'])->label('Phone'); ?>
    <?= $form->field($membersAddress[0], "[{$one}]address_type")->hiddenInput(['value' => 'home'])->label(false); ?>
    <?= $form->field($membersAddress[0], "[{$one}]address")->textInput(['maxlength' => true, 'placeholder' => 'Home Address', 'class' => 'form-control form-control-sm'])->label('Home Address'); ?>

    <?= $form->field($membersAddress[1], "[{$two}]address_type")->hiddenInput(['value' => 'business'])->label(false); ?>

    <?= $form->field($membersAddress[1], "[{$two}]address")->textInput(['maxlength' => true, 'placeholder' => 'Business Address', 'class' => 'form-control form-control-sm'])->label('Business Address'); ?>
    <?php if($membersAccount->status != "1") { ?>
    <div class="row">
    <div class="col-lg-3">
        <?= $form->field($membersAccount, "account_type")->dropDownList([
            'bank_accounts' => 'Bank',
            'coc_accounts' => 'COC',
            'cheque_accounts' => 'Cheque'
        ], ['prompt' => 'Select Account Type'])->label('Account Type'); ?>
    </div>
        <div class="col-lg-3">
        <?= $form->field($membersAccount, "bank_name")->dropDownList(\common\components\Helpers\MemberHelper::getBankAccountsAll()/*['hbl'=>'HBL','mcb'=>'MCB','national_bank'=>'National Bank']*/,['prompt' => 'Select Bank Name', 'class' => 'form-control form-control-sm'])->label('Bank Name'); ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($membersAccount, "title")->textInput(['maxlength' => true, 'placeholder' => 'Account Title', 'class' => 'form-control form-control-sm'])->label('Account Title'); ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($membersAccount, "account_no")->textInput(["title"=>"Account No should be in numbers",'maxlength.' => 16,/*'min'=>"-999" ,'max'=>"9999",*/ 'pattern'=>"\d*", 'placeholder' => 'Account No',/*'type'=>'number',*/ 'class' => 'form-control form-control-sm'])->label('Account No'); ?>
    </div>
    </div>
    <?php } ?>
    <?= $form->field($model, 'status')->textInput()->dropDownList(\common\components\Helpers\MemberHelper::getMemberStatus(),['prompt'=>'Select Status']) ?>
    <?= $form->field($model, 'deleted')->textInput()->dropDownList(['No','Yes']) ?>

    <!--
    <?/*= $form->field($model, 'deleted')->textInput() */?>

-->
<!--
    <?/*= $form->field($model, 'created_at')->textInput() */?>

    <?/*= $form->field($model, 'updated_at')->textInput() */?>

  -->
	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
