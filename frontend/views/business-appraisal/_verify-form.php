<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = $model->member->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Verifications', 'url' => ['verification']];
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
/* @var $model common\models\BusinessAppraisal */
/* @var $form yii\widgets\ActiveForm */
$js =
    '$(".checkbox").change(function() {
    if($(this).is(\':checked\')){
        $(\'#\'+this.id+\'-tick\').show();

        var disable="0";
        $("input[name="+this.id+"]").val("1");
          $(\'.checkbox\').each(function(i, obj) {
                var bb=$("input[name="+obj.id+"]").val();
                 if(bb==0){ 
                 disable=1;
          }
        });
        if(disable==1){
          document.getElementById("verify").disabled = true}
        else{
            document.getElementById("verify").disabled = false
            }  
    }
     else{
     $("input[name="+this.id+"]").val("0");
     document.getElementById("verify").disabled = true;
             $(\'#\'+this.id+\'-tick\').hide();
     }
});';

$this->registerJs($js);
?>
<head>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
</head>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4><b><?= isset($model->member->full_name)?$model->member->full_name:'' . ' / ' . isset($model->member->cnic)?$model->member->cnic:'' ?></b></h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?= Html::beginForm(["/business-appraisal/verify", 'id' => $model->id], 'POST'); ?>
        <section class="widget widget-accordion" id="accordion" aria-multiselectable="false">
            <article class="panel">
                <div class="panel-heading" role="tab" id="headingOne">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false"
                       aria-controls="collapseOne" class="collapsed">
                        Members
                        <span class="glyphicon glyphicon-ok pull-right" style="color:green;display: none;"
                              id= <?= "member_check-tick" ?>></span> <i class="font-icon font-icon-arrow-down"></i>
                    </a>
                </div>
                <div id="collapseOne" class="panel-collapse in collapse" role="tabpanel"
                     aria-labelledby="headingOne"
                     style="">
                    <div class="panel-collapse-in">
                        <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $model->member,
                                            'attributes' => [
                                                'full_name',
                                                'parentage',
                                                'parentage_type',
                                                'cnic',
                                                [
                                                    'attribute' => 'gender',
                                                    'value' => function ($data) {
                                                        if ($data->gender == 'm') {
                                                            return 'Male';
                                                        } else if ($data->gender == 'f') {
                                                            return 'Female';
                                                        } else {
                                                            return 'Transgender';
                                                        }
                                                    }
                                                ],
                                                [
                                                    'attribute' => 'dob',
                                                    'label' => 'Date of Birth',
                                                    'value' => function ($data) {
                                                        return date('Y-m-d', $data->dob);
                                                    }
                                                ],
                                                [
                                                    'attribute' => 'dob',
                                                    'label' => 'Mobile',
                                                    'value' => function ($data) {
                                                        foreach ($data->membersPhones as $phone)
                                                            if ($phone->is_current == 1) {
                                                                return $phone->phone;
                                                            }
                                                    }
                                                ],

                                            ]]); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $model->member,
                                            'attributes' => [

                                                [
                                                    'attribute' => 'dob',
                                                    'label' => 'Business Address',
                                                    'value' => function ($data) {
                                                        foreach ($data->membersAddresses as $address)
                                                            if ($address->is_current == 1 && $address->address_type == 'business') {
                                                                return $address->address;
                                                            }
                                                    }
                                                ],
                                                [
                                                    'attribute' => 'dob',
                                                    'label' => 'Email Address',
                                                    'value' => function ($data) {
                                                        foreach ($data->membersEmails as $email)
                                                            if ($email->is_current == 1) {
                                                                return $email->email;
                                                            }
                                                    }
                                                ],
                                                'marital_status', 'family_member_name', 'family_member_cnic', 'religion',

                                                'education',
                                                // getMembersEmails
                                            ]]); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?= Html::checkbox('member_check', false, ['uncheck' => 0, 'label' => ' Check Member Details', 'class' => 'checkbox', 'id' => 'member_check']) ?>
                    </div>
                </div>
            </article>
            <article class="panel">
                <div class="panel-heading" role="tab" id="headingTwo">
                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"
                       aria-expanded="false" aria-controls="collapseTwo">
                        Application
                        <span class="glyphicon glyphicon-ok pull-right" style="color:green;display: none;"
                              id= <?= "application_check-tick" ?>></span>
                        <i class="font-icon font-icon-arrow-down"></i>
                    </a>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                    <div class="panel-collapse-in">
                        <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [
                                                'fee',
                                                'application_no',

                                                [
                                                    'attribute' => 'project',
                                                    'value' => function ($data) {
                                                        return isset($data->project->name) ? $data->project->name : '';
                                                    }
                                                ],
                                                [
                                                    'attribute' => 'product',
                                                    'value' => function ($data) {
                                                        return isset($data->product->name) ? $data->product->name : '';
                                                    }
                                                ],
                                                [
                                                    'attribute' => 'activity',
                                                    'value' => function ($data) {
                                                        return isset($data->activity->name) ? $data->activity->name : '';
                                                    }
                                                ],
                                                [
                                                    'attribute' => 'requested_amount',
                                                    'value' => function ($data) {
                                                        return isset($data->requested_amount) ? number_format($data->requested_amount) : '';
                                                    }
                                                ],

                                            ]]); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget(['model' => $model,
                                            'attributes' => ['who_will_work',
                                                'name_of_other',
                                                'other_cnic',
                                                ['attribute' => 'is_urban',
                                                    'label' => 'Urban',
                                                    'value' => function ($data) {
                                                        if ($data->is_urban == 0) {
                                                            return 'No';
                                                        } else {
                                                            return 'Yes';
                                                        }
                                                    }],
                                                ['attribute' => 'is_lock',
                                                    'label' => 'Lock',
                                                    'value' => function ($data) {
                                                        if ($data->is_lock == 0) {
                                                            return 'No';
                                                        } else {
                                                            return 'Yes';
                                                        }
                                                    }],

                                            ]]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?= Html::checkbox('application_check', false, ['uncheck' => 0, 'label' => ' Check Applications Details', 'class' => 'checkbox', 'id' => 'application_check']); ?>
                        <br>
                    </div>
                </div>
            </article>
            <article class="panel">
                <div class="panel-heading" role="tab" id="headingThree">
                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree"
                       aria-expanded="false" aria-controls="collapseThree">
                        Social Appraisal
                        <span class="glyphicon glyphicon-ok pull-right" style="color:green;display: none;"
                              id= <?= "social_appraisal_check-tick" ?>></span>
                        <i class="font-icon font-icon-arrow-down"></i>
                    </a>
                </div>
                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headingThree">
                    <div class="panel-collapse-in">
                        <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $model->socialAppraisal,
                                            'attributes' => [
                                                'poverty_index',
                                                'house_ownership',
                                                'house_rent_amount',
                                                'land_size',
                                                'total_family_members',
                                                'ladies',
                                                'gents',
                                                'source_of_income',
                                                'total_household_income:decimal',
                                                'educational_expenses:decimal',
                                                'medical_expenses:decimal',
                                                'kitchen_expenses:decimal',

                                            ]]); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $model->socialAppraisal,
                                            'attributes' => [
                                                'monthly_savings',
                                                'amount:decimal',
                                                'other_expenses:decimal',
                                                'total_expenses:decimal',
                                                'other_loan',
                                                'loan_amount:decimal',
                                                'business_income:decimal',
                                                'job_income:decimal',
                                                'house_rent_income:decimal',
                                                'other_income:decimal',
                                                'economic_dealings',
                                                'social_behaviour'
                                            ]]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?= Html::checkbox('social_appraisal_check', false, ['uncheck' => 0, 'label' => ' Check Social Appraisal Details', 'class' => 'checkbox', 'id' => 'social_appraisal_check']); ?>
                        <br>
                    </div>
                </div>
            </article>
            <article class="panel">
                <div class="panel-heading" role="tab" id="headingFour">
                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour"
                       aria-expanded="false" aria-controls="collapseFour">
                        Business Appraisal
                        <span class="glyphicon glyphicon-ok pull-right" style="color:green;display: none;"
                              id= <?= "business_appraisal_check-tick" ?>></span>
                        <i class="font-icon font-icon-arrow-down"></i>
                    </a>
                </div>
                <div id="collapseFour" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headingFour">
                    <div class="panel-collapse-in">
                        <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $model->businessAppraisal,
                                            'attributes' => [
                                                //'business_type',
                                                'business',
                                                'place_of_business',
                                            ]]); ?>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <section class="box-typical">
                                            <header class="box-typical-header-sm bordered text-center"><h6><b>Business
                                                        Assets</b></h6>
                                            </header>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Name</b>
                                                    </div>

                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->baBusinessExpenses->assets_list) ? $model->businessAppraisal->baBusinessExpenses->assets_list : ''
                                                        ?>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Value</b>
                                                    </div>
                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->baBusinessExpenses->total_amount) ? $model->businessAppraisal->baBusinessExpenses->total_amount : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-md-3">
                                        <section class="box-typical">
                                            <header class="box-typical-header-sm bordered text-center"><h6><b>Fixed
                                                        Business
                                                        Assets</b></h6>
                                            </header>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Name</b>
                                                    </div>

                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->baFixedBusinessAssets->assets_list) ? $model->businessAppraisal->baFixedBusinessAssets->assets_list : '';
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Value</b>
                                                    </div>
                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->baFixedBusinessAssets->total_amount) ? $model->businessAppraisal->baFixedBusinessAssets->total_amount : '';
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-md-3">
                                        <section class="box-typical">
                                            <header class="box-typical-header-sm bordered text-center"><h6><b>New
                                                        Required
                                                        Assets</b></h6>
                                            </header>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Name</b>
                                                    </div>

                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->baNewRequiredAssets->assets_list) ? $model->businessAppraisal->baNewRequiredAssets->assets_list : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Value</b>
                                                    </div>
                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->baNewRequiredAssets->total_amount) ? $model->businessAppraisal->baNewRequiredAssets->total_amount : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-md-3">
                                        <section class="box-typical">
                                            <header class="box-typical-header-sm bordered text-center"><h6><b>Running
                                                        Capital</b></h6>
                                            </header>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Name</b>
                                                    </div>

                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->baRunningCapitals->assets_list) ? $model->businessAppraisal->baRunningCapitals->assets_list : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profile-statistic tbl">
                                                <div class="tbl-row">
                                                    <div class="tbl-cell">
                                                        <b>Value</b>
                                                    </div>
                                                    <div class="tbl-cell">
                                                        <?php
                                                        echo isset($model->businessAppraisal->baRunningCapitals->total_amount) ? $model->businessAppraisal->baRunningCapitals->total_amount : ''
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <?= Html::checkbox('business_appraisal_check', false, ['uncheck' => 0, 'label' => ' Check Business Appraisal Details', 'class' => 'checkbox', 'id' => 'business_appraisal_check']); ?>
                        <br>
                    </div>
                </div>
            </article>
            <article class="panel">
                <div class="panel-heading" role="tab" id="headingFive">
                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive"
                       aria-expanded="false" aria-controls="collapseFive">
                        Documents
                        <span class="glyphicon glyphicon-ok pull-right" style="color:green;display: none;"
                              id= <?= "documents_check-tick" ?> ?></span>
                        <i class="font-icon font-icon-arrow-down"></i>
                    </a>
                </div>
                <div id="collapseFive" class="panel-collapse collapse" role="tabpanel"
                     aria-labelledby="headingFive">
                    <div class="panel-collapse-in">
                        <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="line-with-icon">
                                            <b>Back CNIC</b>
                                        </p>
                                        <?php
                                        $image = \common\components\Helpers\MemberHelper::getFamilyMemberCNICBackImage($model->socialAppraisal->id);

                                        if (!empty($image)) {
                                            $user_image = (!empty($image->image_name)) ? ($image->image_name) : 'noimage.png';
                                            //$pic_url =  $model->id. "/" .$image->parent_type ."/" . $user_image;
                                            $pic_url = $image->parent_type . "/" . $model->socialAppraisal->id . "/" . $user_image;

                                        } else {
                                            $pic_url = 'noimage.png';
                                        }
                                        ?>
                                        <?php echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:50%;height:250px;']); ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="line-with-icon">
                                            <b>Font CNIC</b>
                                        </p>
                                        <?php
                                        $image = \common\components\Helpers\MemberHelper::getFamilyMemberCNICFrontImage($model->member->id);
                                        if (!empty($image)) {
                                            $user_image = (!empty($image->image_name)) ? ($image->image_name) : 'noimage.png';
                                            $pic_url = $image->parent_type . "/" . $model->member->id . "/" . $user_image;
                                        } else {
                                            $pic_url = 'noimage.png';
                                        }
                                        ?>
                                        <?php echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:50%;height:250px;']); ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="line-with-icon">
                                            <b>Utility Bill</b>
                                        </p>
                                        <?php
                                        $image = \common\components\Helpers\ApplicationHelper::getUtilityBill($model->id);
                                        if (!empty($image)) {
                                            $user_image = (!empty($image->image_name)) ? ($image->image_name) : 'noimage.png';
                                            $pic_url = $image->parent_type . "/" . $model->member->id . "/" . $user_image;
                                        } else {
                                            $pic_url = 'noimage.png';
                                        }
                                        ?>
                                        <?php echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:50%;height:250px;']); ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="line-with-icon">
                                            <b>Marriage Certificat</b>
                                        </p>
                                        <?php
                                        $image = \common\components\Helpers\ApplicationHelper::getMarraigeCertificate($model->id);
                                        if (!empty($image)) {
                                            $user_image = (!empty($image->image_name)) ? ($image->image_name) : 'noimage.png';
                                            $pic_url = $image->parent_type . "/" . $model->member->id . "/" . $user_image;
                                        } else {
                                            $pic_url = 'noimage.png';
                                        }
                                        ?>
                                        <?php echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:50%;height:250px;']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?= Html::checkbox('documents_check', false, ['uncheck' => 0, 'label' => ' Check Documents Details', 'class' => 'checkbox', 'id' => 'documents_check']); ?>
                        <br>
                    </div>
                </div>
            </article>
        </section>
        <?= Html::submitButton('Verify', ['class' => 'btn btn-primary', 'disabled' => 'disabled', 'id' => 'verify']) ?>
        <?= Html::endForm(); ?>
    </div>
</div>






