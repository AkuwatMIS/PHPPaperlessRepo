<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use common\components\Helpers\MemberHelper;
use borales\extensions\phoneInput\PhoneInput;
use common\widgets\ImageWidget;

/* @var $this yii\web\View */
/* @var $members common\models\Members */
/* @var $form yii\widgets\ActiveForm */

$js = '

$(document).ready(function(){
    $(\'#membersphone-0-phone_type\').parent(\'.field-membersphone-0-phone_type\').hide();
});
$(document).ready(function(){
$(".field-memberinfo-disability_nature").hide();
$(".field-memberinfo-disability_details").hide();
$("#memberinfo-cnic_expiry_date").attr(\'required\', "true");
$(".life_time").change(function() {
    var csc=this.className;
    if($(this).not(\':checked\')){
    $("#memberinfo-cnic_expiry_date").attr(\'required\', "true");
    document.getElementsByClassName("expiry")[0].disabled = false;
    }
    if($(this).is(\':checked\')){
    $("#memberinfo-cnic_expiry_date").val("");
    $("#memberinfo-cnic_expiry_date").removeAttr(\'required\');
    document.getElementsByClassName("expiry")[0].disabled = true;
    }
});

    $("#members-is_disable").change(function() {
        var is_disable=$(\'#members-is_disable\').val();
        if(is_disable == 1){
            $(".field-memberinfo-disability_nature").show();
        }else{
            $(".field-memberinfo-disability_nature").hide();
            $(".field-memberinfo-disability_details").hide();
        }
    });
    
    
    $("#memberinfo-disability_nature").change(function() {
        var is_disable=$(\'#memberinfo-disability_nature\').val();
        if(is_disable == "others"){
            $(".field-memberinfo-disability_details").show();
        }else{
            $(".field-memberinfo-disability_details").hide();
        }
    });

    ////on load
    var value_bank=$("#membersaccount-bank_name").val();
           if(value_bank!=""){
              $("#membersaccount-account_no").attr(\'required\', "true");
              $("#membersaccount-title").attr(\'required\', "true");
           }else{
              $("#membersaccount-account_no").removeAttr(\'required\');
              $("#membersaccount-title").removeAttr(\'required\');
           }
           if(value_bank=="HBL" || value_bank=="Meezan"){
              $("#membersaccount-account_no").attr(\'maxlength\', 14);
           }else if(value_bank=="ABL" || value_bank=="MCB"){
             $("#membersaccount-account_no").attr(\'maxlength\', 16);
           }else{
             $("#membersaccount-account_no").attr(\'maxlength\', 12);
           }
    ////
    $(\'#membersphone-1-phone_type\').parent(\'.field-membersphone-1-phone_type\').hide();
    $("#membersaccount-bank_name").change(function(){
       var value_bank=$("#membersaccount-bank_name").val();
       
       if(value_bank!=""){
            if(value_bank =="cheque"){
             $("#membersaccount-account_no").removeAttr(\'required\');
             $(".account_no").hide();
            } else {
          $("#membersaccount-account_no").val("");
          $("#membersaccount-account_no").attr(\'required\', "true");
          $("#membersaccount-title").attr(\'required\', "true");
          }
       }else{
          $("#membersaccount-account_no").removeAttr(\'required\');
          $("#membersaccount-title").removeAttr(\'required\');
       }
       if(value_bank=="HBL" || value_bank=="Meezan"){
          $("#membersaccount-account_no").attr(\'maxlength\', 14);
          $("#membersaccount-account_no").attr(\'minlength\', 14);
       }else if(value_bank=="ABL"){
         $("#membersaccount-account_no").attr(\'maxlength\', 20);
         $("#membersaccount-account_no").attr(\'minlength\', 20);
       }else if(value_bank=="MCB"){
         $("#membersaccount-account_no").attr(\'maxlength\', 16);
         $("#membersaccount-account_no").attr(\'minlength\', 16);
       }else if(value_bank=="AKB"){
         $("#membersaccount-account_no").attr(\'maxlength\', 14);
         $("#membersaccount-account_no").attr(\'minlength\', 14);
       }else{
         $("#membersaccount-account_no").attr(\'maxlength\', 12);
         $("#membersaccount-account_no").attr(\'minlength\', 12);
       }
    
    });
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
function isOdd(num) { return num % 2;}
$(document).ready(function(){
    /*$(\"#membersphone-0-phone\").change(function(){
    var mobile=$('#membersphone-0-phone').val();
        var mobile=mobile.replace('_','');
        var len=mobile.length;
        if(len>0 && len!=12){
            $('.field-membersphone-0-phone').removeClass('has-success').addClass('has-error');
            $('.field-membersphone-0-phone').find('.help-block').text('Mobile No Format is not correct');
        }else{
            $('.field-membersphone-0-phone').removeClass('has-error').addClass('has-success');
            $('.field-membersphone-0-phone').find('.help-block').text('');
        }
    });*/
    var form = document.getElementById(\"w0\");
    document.getElementById(\"member-submit\").addEventListener(\"click\", function (event) {
        if($('#members-cnic').val()==$('#members-family_member_cnic').val()){
              alert(\"Member and family member CNIC is same\");
              event.preventDefault();
        }
        if($('#membersphone-0-phone').val()==''){
              alert(\"Mobile is mandatory\");
              event.preventDefault();
        }
        var mobile=$('#membersphone-0-phone').val();
        var mobile=mobile.replace('_','');
        var len=mobile.length;
        if(len>0 && len!=12){
            $('.field-membersphone-0-phone').removeClass('has-success').addClass('has-error');
            $('.field-membersphone-0-phone').find('.help-block').text('Mobile No Format is not correct');
            event.preventDefault();
        }
        
    });

$('#members-cnic').change(function(){
var cnic=$('#members-cnic').val();
var lastChar = cnic.substr(cnic.length - 1);
var genderByCnic = isOdd(lastChar);
if(genderByCnic === 1){
   cnicGender='m';
}else{
   cnicGender='f';
}

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

                       $('.field-members-branch_id').hide();
                       $('.field-members-team_id').hide();
                       $('.field-members-field_id').hide();

                       $('#members-gender').prop(\"value\",cnicGender);
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
         
                       $('#membersaccount-bank_name').prop(\"value\",obj.account.account.bank_name);
                       $('#membersaccount-account_no').prop(\"value\",obj.account.account.account_no);
                       $('#membersaccount-title').prop(\"value\",obj.account.account.title);
                       
                       $('#memberinfo-cnic_issue_date').prop(\"value\",obj.info.info.cnic_issue_date);
                       $('#memberinfo-cnic_expiry_date').prop(\"value\",obj.info.info.cnic_expiry_date);
                       $('#memberinfo-mother_name').prop(\"value\",obj.info.info.mother_name);
   
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

                       $('.field-members-branch_id').show();
                       $('.field-members-team_id').show();
                       $('.field-members-field_id').show();

                       $('#members-gender').prop(\"value\",cnicGender);
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
                       
                       $('#membersaccount-bank_name').prop(\"value\",\"\");
                       $('#membersaccount-account_no').prop(\"value\",\"\");
                       
                       $('#memberinfo-cnic_issue_date').prop(\"value\",\"\");
                       $('#memberinfo-cnic_expiry_date').prop(\"value\",\"\");
                       $('#memberinfo-mother_name').prop(\"value\",\"\");
                      
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

                       $('.field-members-branch_id').show();
                       $('.field-members-team_id').show();
                       $('.field-members-field_id').show();

                       $('#members-gender').prop(\"value\",cnicGender);
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
                       
                       $('#membersaccount-bank_name').prop(\"value\",\"\");
                       $('#membersaccount-account_no').prop(\"value\",\"\");
                       
                       $('#memberinfo-cnic_issue_date').prop(\"value\",\"\");
                       $('#memberinfo-cnic_expiry_date').prop(\"value\",\"\");
                       $('#memberinfo-mother_name').prop(\"value\",\"\");
                       

            }
        }
    });
    });
});
";
$this->registerJs($js);
//   $('#members-gender').prop("value",obj.data.gender);
//$('#members-gender').prop("value","");
/*echo '<pre>';
print_r($membersAddress);
die();*/
?>

<?php $form = ActiveForm::begin();?>
<?= $form->errorSummary($members) ?>
<!--<?/*= $form->errorSummary($membersPhone) */?>
<?/*= $form->errorSummary($membersAddress) */?>-->
<style>
    .label-class{
        margin-left: 40%;
    }
</style>
<!--<div class="row">
    <div class="col-sm-4" style="">
        <?php /*echo $form->field($members, 'profile_pic')->widget(ImageWidget::className(), [
            'uploadUrl' => Url::to(['/members/upload-photo' , 'type' => "profile"]),
        ])->label('Profile Pic',['class'=>'label-class']) */?>
    </div>
    <div class="col-sm-4">
        <?php /*echo $form->field($members, 'cnic_front')->widget(ImageWidget::className(), [
            'uploadUrl' => Url::to(['/members/upload-photo','type' => "cnic_front"]),
        ])->label('Cnic Front',['class'=>'label-class']) */?>
    </div>
    <div class="col-sm-4">
        <?php /*echo $form->field($members, 'cnic_back')->widget(ImageWidget::className(), [
            'uploadUrl' => Url::to(['/members/upload-photo', 'type' => "cnic_back"]),
        ])->label('Cnic Back',['class'=>'label-class']) */?>
    </div>
</div>-->
<div class="row">
    <?= $form->field($members, 'id')->hiddenInput()->label(false) ?>

    <div class="col-lg-4">
        <?= $form->field($members, 'cnic')->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '99999-9999999-9',
        ])->textInput(['maxlength' => true,'readonly'=>$members->isNewRecord ? false:true, 'placeholder' => 'CNIC', 'class' => 'form-control form-control-sm']) ?>
    </div>
    <div class="col-lg-2">
        <?= $form->field($member_info, "cnic_issue_date")->widget(\yii\jui\DatePicker::className(),[
            'dateFormat' => 'yyyy-MM-dd',
            'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'CNIC Issue Date',
                'readonly' => 'readonly'
            ],
            'clientOptions'=>[
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '' . date('Y',strtotime('-15 year')) . ':' . date('Y')
            ]
        ]); ?>
    </div>
    <div class="col-lg-2">
        <?= $form->field($member_info, "cnic_expiry_date")->widget(\yii\jui\DatePicker::className(),[
            'dateFormat' => 'yyyy-MM-dd',
            'options' => ['class' => 'form-control form-control-sm expiry', 'placeholder' => 'CNIC Expiry Date',
                //'readonly' => 'readonly'
            ],
            'clientOptions'=>[
                'changeMonth' => true,
                'changeYear' => true,
                'yearRange' => '' . date('Y',strtotime('0 year')) . ':' . date('Y',strtotime('+10 year'))

            ]
        ]);  ?>    </div>
    <div class="2">
        <label for="memberinfo-is_life_time">/Expiry For Life</label>
        <input name="MemberInfo[is_life_time]" class="life_time" type="checkbox" id="memberinfo-is_life_time">
    </div>
    <div class="col-lg-2">
        <?= $form->field($member_info, "mother_name")->textInput(['placeholder'=>'Mother Name'])->label('Mother Name');  ?>
    </div>
</div>
<div class="alert alert-success alert-dismissable" style="display:none">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <h4><i class="icon fa fa-check exist"></i></h4>
</div>
<div class="alert alert-danger alert-dismissable " style="display:none">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
    <h4><i class="icon fa fa-ban blacklist"></i></h4>
</div>
<div class="row">
    <?php if($members->isNewRecord){?>
        <div class="col-sm-4">
            <?php
            //$regions = ArrayHelper::map(Regions::find()->asArray()->all(), 'id', 'name');
            echo $form->field($members, 'branch_id')->dropDownList($branches, ['prompt' => 'Select Branch'])->label('Branch');?>
        </div>
        <div class="col-sm-4">
            <?php
            $value = !empty($members->team_id) ? $members->team->name : null;
            echo $form->field($members, 'team_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['members-branch_id'],
                    'initialize' => true,
                    'initDepends' => ['members-branch_id'],
                    'placeholder' => 'Select Team',
                    'url' => Url::to(['/structure/fetch-team-by-branch'])
                ],
                'data' => $value ? [$members->team_id => $value] : []
            ])->label('Team');
            ?>
        </div>
        <div class="col-sm-4">
            <?php
            $value = !empty($members->field_id) ? $members->field->name : null;
            echo $form->field($members, 'field_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['members-team_id'],
                    //'initialize' => true,
                    //'initDepends'=>['progressreportdetailssearch-area_id'],
                    'placeholder' => 'Select Field',
                    'url' => Url::to(['/structure/fetch-field-by-team'])
                ],
                'data' => $value ? [$members->field_id => $value] : []
            ])->label('Field');
            ?>
        </div>
    <?php }?>
</div>
<div class="row">
    <div class="col-lg-3">
        <?= $form->field($members, 'full_name')->textInput(['maxlength' => true, 'placeholder' => 'Full Name', 'class' => 'form-control form-control-sm']) ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($members, 'parentage_type')->dropDownList(MemberHelper::getParentageType(), ['prompt' => 'Select Type', 'class' => 'form-control form-control-sm'])->label('Parentage Type') ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($members, 'parentage')->textInput(['maxlength' => true, 'placeholder' => 'Parentage', 'class' => 'form-control form-control-sm'])->label('Parentage Name') ?>
    </div>
</div>
<?php
$a= date('Y',strtotime('-70 year'));
$b=date('Y',strtotime('-15 year'));

?>
<?php
if($members->isNewRecord){
    $members->dob=strtotime('1980-01-01');
}
?>
<div class="row">
    <div class="col-lg-3">
        <?= $form->field($members, 'gender')->dropDownList(\common\components\Helpers\MemberHelper::getGender(), ['prompt' => 'Select Gender', 'class' => 'form-control form-control-sm'])->label('Gender') ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($members, "dob")->widget(\yii\jui\DatePicker::className(),[
            'dateFormat' => 'yyyy-MM-dd',
            'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Date of Birth',
                'readonly' => 'readonly'

            ],
            'clientOptions'=>[
                'changeMonth' => true,
                'changeYear' => true,
                'startDate' => $a,
                'endDate' => $b,
                'yearRange' => '' . $a . ':' . $b
            ]
        ])->label('Date of Birth');  ?>
    </div>
    <!--<div class="col-lg-3">
        <?/*= $form->field($members, "dob")->textInput(['maxlength' => true, 'placeholder' => 'Date of Birth', 'class' => 'form-control form-control-sm'])->label('Date of Birth'); */?>
    </div>-->
    <div class="col-lg-3">
        <?php $one = 0;
        $two = 1; ?>
        <?= $form->field($membersPhone[0], "[{$one}]phone_type", ['enableClientValidation' => false])->hiddenInput(['value' => 'mobile'])->label(false); ?>
        <?= $form->field($membersPhone[0], "[{$one}]phone" /*['enableClientValidation' => false]*/)->widget(PhoneInput::className(), [
            'jsOptions' => [
                'preferredCountries' => ['pk'],
            ]])->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '999999999999',
        ])->textInput(['required'=>'required','maxlength' => true, 'placeholder' => '923011234567', 'class' => 'form-control form-control-sm'])->label('Mobile'); ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($membersPhone[1], "[{$two}]phone_type", ['enableClientValidation' => false])->hiddenInput(['value' => 'phone'])->label(false); ?>
        <?= $form->field($membersPhone[1], "[{$two}]phone" /*['enableClientValidation' => false]*/)->widget(PhoneInput::className(), [
            'jsOptions' => [
                'preferredCountries' => ['pk'],
            ]])->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '999999999999',
        ])->textInput(['maxlength' => true, 'placeholder' => '924231234567', 'class' => 'form-control form-control-sm'])->label('Phone'); ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <?= $form->field($members, 'education')->dropDownList(\common\components\Helpers\MemberHelper::getEducation(), ['prompt' => 'Select Education', 'class' => 'form-control form-control-sm'])->label('Education') ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($members, 'marital_status')->dropDownList(\common\components\Helpers\MemberHelper::getMaritalStatus(), ['prompt' => 'Select Marital Status', 'class' => 'form-control form-control-sm'])->label('Marital Status') ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($members, 'family_no')->textInput(['maxlength' => true, 'placeholder' => 'Family No', 'class' => 'form-control form-control-sm']) ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($members, 'family_member_name')->textInput(['maxlength' => true, 'placeholder' => 'Family Member Name', 'class' => 'form-control form-control-sm','required'=>'required']) ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <?= $form->field($members, 'family_member_cnic')->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '99999-9999999-9',
        ])->textInput(['maxlength' => true, 'placeholder' => 'Family Member CNIC', 'class' => 'form-control form-control-sm']) ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($members, 'religion')->dropDownList(\common\components\Helpers\MemberHelper::getReligions(), ['prompt' => 'Select Religion', 'class' => 'form-control form-control-sm'])->label('Religion') ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($membersAddress[0], "[{$one}]address_type")->hiddenInput(['value' => 'home'])->label(false); ?>
        <?= $form->field($membersAddress[0], "[{$one}]address")->textInput(['maxlength' => true, 'placeholder' => 'Current Address', 'class' => 'form-control form-control-sm'])->label('Current Address'); ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($membersAddress[1], "[{$two}]address_type")->hiddenInput(['value' => 'business'])->label(false); ?>
        <?= $form->field($membersAddress[1], "[{$two}]address")->textInput(['maxlength' => true, 'placeholder' => 'Permanent Address', 'class' => 'form-control form-control-sm'])->label('Permanent Address'); ?>
    </div>
    <div class="col-sm-3">
        <?php
        $value = !empty($membersAccount->account_type) ? $membersAccount->account_type : null;
        echo $form->field($membersAccount, 'account_type')->dropDownList([
            'bank_accounts' => 'Bank',
            'coc_accounts' => 'COC',
            'cheque_accounts' => 'Cheque'
        ], ['prompt' => 'Select Account Type'])->label('Account Type');?>
    </div>
    <div class="col-sm-3">
        <?php
        $value = !empty($membersAccount->bank_name) ? $membersAccount->bank_name : null;
        echo $form->field($membersAccount, 'bank_name')->widget(DepDrop::classname(), [
            'pluginOptions' => [
                'depends' => ['membersaccount-account_type'],
                'initialize' => true,
                'initDepends' => ['membersaccount-account_type'],
                'placeholder' => 'Select Bank Name',
                'url' => Url::to(['/structure/fetch-bank-by-type'])
            ],
            'data' => $value ? [$membersAccount->bank_name => $value] : []
        ])->label('Bank Name');
        ?>
    </div>
    <!--<div class="col-lg-3">
        <?/*= $form->field($membersAccount, "bank_name")->dropDownList(\common\components\Helpers\MemberHelper::getBankAccounts()/*['hbl'=>'HBL','mcb'=>'MCB','national_bank'=>'National Bank'],['prompt' => 'Select Bank Name', 'class' => 'form-control form-control-sm'])->label('Bank Name'); */?>
    </div>-->
    <div class="col-lg-3">
        <?= $form->field($membersAccount, "title")->textInput(['maxlength' => true, 'placeholder' => 'Account Title', 'class' => 'form-control form-control-sm'])->label('Account Title'); ?>
    </div>
    <div class="col-lg-3 account_no">
        <?= $form->field($membersAccount, "account_no")->textInput(["title"=>"Account No should be in numbers",'maxlength' => 20,/*'min'=>"-999" ,'max'=>"9999",*/ 'pattern'=>"\d*", 'placeholder' => 'Account No',/*'type'=>'number',*/ 'class' => 'form-control form-control-sm'])->label('Account No'); ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <?= $form->field($members, 'is_disable')->dropDownList(\common\components\Helpers\MemberHelper::getIsDisable(), ['prompt' => 'Select Disable', 'class' => 'form-control form-control-sm'])->label('Disable') ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($member_info, "disability_nature")->dropDownList(\common\components\Helpers\MemberHelper::getDisabilityNaturePmyp(), ['prompt' => 'Select Disability Nature', 'class' => 'form-control form-control-sm']) ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($member_info, "disability_details")->textInput(['placeholder'=>'Disability Details'])->label('Disability Details');  ?>
    </div>
    <!--<div class="col-lg-3">
        <?/*= $form->field($members, 'disability_type')->dropDownList(\common\components\Helpers\ListHelper::getLists('disability_types'), ['prompt' => 'Select Disability Type', 'class' => 'form-control form-control-sm'])->label('Disability Type') */?>
    </div>-->
</div>

<!--<div class="row">

    <div class="col-lg-3">
        <?/*= $form->field($members, 'nature')->dropDownList(\common\components\Helpers\ListHelper::getLists('disability_types'), ['prompt' => 'Select Disability Type', 'class' => 'form-control form-control-sm'])->label('Disability Type') */?>
    </div>
</div>-->
<?php if (!Yii::$app->request->isAjax) { ?>
    <div class="form-group">
        <?= Html::submitButton($members->isNewRecord ? 'Create' : 'Update', ['class' => $members->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'member-submit']) ?>
    </div>
<?php } ?>

<?php ActiveForm::end(); ?>


