<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Applications */
$this->title = $model->application_no;
$this->params['breadcrumbs'][] = ['label' => 'Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = "
if(\"" . $model->status . "\"=='rejected'){
     swal({
		title: \"Rejected\",
		text: \"" . $model->member->full_name . '\r\n' . $model->member->cnic . '\r\n' . $model->reject_reason . "\",
        type: \"error\",
        confirmButtonClass: \"btn-danger\"
	 });
}else{
    if(\"" . $model->who_will_work . "\"=='self'){
           if(\"" . $model->member->family_member_cnic . "\"!=''){
            swal({
                title: \"NACTA/UNSCR and Blacklist Verification\",
                text:  \"" . 'The below CNICs are cleared and verified from the NACTA/UNSCR list provided by SECP' . '\r\n'
    . '\r\n'
    . 'Applicant CNIC (' . $model->member->cnic . ')' . '\r\n'
    . 'Family Member CNIC (' . $model->member->family_member_cnic . ')' . '\r\n'
    . "\",
                type: \"success\",
                confirmButtonClass: \"btn-success\",
                confirmButtonText: \"Ok\"
            });
            }else{
            swal({
                title: \"NACTA/UNSCR and Blacklist Verification\",
                text:  \"" . 'The below CNICs are cleared and verified from the NACTA/UNSCR list provided by SECP' . '\r\n'
    . '\r\n'
    . 'Applicant CNIC (' . $model->member->cnic . ')' . '\r\n'
    . "\",
                type: \"success\",
                confirmButtonClass: \"btn-success\",
                confirmButtonText: \"Ok\"
            });
            }
     }else{
            
        if(\"" . $model->member->family_member_cnic . "\"!=''){
            if(\"" . $model->other_cnic . "\"!=''){ 
                swal({
                    title: \"NACTA/UNSCR and Blacklist Verification\",
                    text:  \"" . 'The below CNICs are cleared and verified from the NACTA/UNSCR list provided by SECP' . '\r\n'
    . '\r\n'
    . 'Applicant CNIC (' . $model->member->cnic . ')' . '\r\n'
    . 'Family Member CNIC (' . $model->member->family_member_cnic . ')' . '\r\n'
    . 'Beneficiary CNIC (' . $model->other_cnic . ')'
    . "\",
                    type: \"success\",
                    confirmButtonClass: \"btn-success\",
                    confirmButtonText: \"Ok\"
                });
            }else{ 
                 swal({
                    title: \"NACTA/UNSCR and Blacklist Verification\",
                    text:  \"" . 'The below CNICs are cleared and verified from the NACTA/UNSCR list provided by SECP' . '\r\n'
    . '\r\n'
    . 'Applicant CNIC (' . $model->member->cnic . ')' . '\r\n'
    . 'Family Member CNIC (' . $model->member->family_member_cnic . ')' . '\r\n'
    . "\",
                    type: \"success\",
                    confirmButtonClass: \"btn-success\",
                    confirmButtonText: \"Ok\"
                });
             }
           
        }else{
                if(\"" . $model->other_cnic . "\"!=''){ 
                     swal({
                            title: \"NACTA/UNSCR and Blacklist Verification\",
                            text:  \"" . 'Applicant CNIC (' . $model->member->cnic . ')' . '\r\n'
    . 'Beneficiary CNIC (' . $model->other_cnic . ')'
    . "\",
                            type: \"success\",
                            confirmButtonClass: \"btn-success\",
                            confirmButtonText: \"Ok\"
                     });
                }else{ 
                      swal({
                            title: \"NACTA/UNSCR and Blacklist Verification\",
                            text:  \"" . 'Applicant CNIC (' . $model->member->cnic . ')' . '\r\n'
    . "\",
                            type: \"success\",
                            confirmButtonClass: \"btn-success\",
                            confirmButtonText: \"Ok\"
                      });  
                }
        
        }
      }
		
}";
$this->registerJs($js);

?>
<link rel="stylesheet" href="/css/sweetalert.css">
<link rel="stylesheet" href="/css/sweet-alert-animations.min.css">
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Application</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="row">
        <div class="col-lg-3">
            <section class="box-typical">
                <div class="profile-card">
                    <div class="profile-card-photo">
                        <?php
                        /*$image = \common\components\Helpers\MemberHelper::getProfileImage($model->member->id);
                        if (!empty($image)) {
                            $user_image = (!empty($image->image_name)) ? ($image->image_name) : 'noimage.png';
                            $pic_url = $image->parent_type . "/" . $model->member->id . "/" . $user_image;
                        } else {
                            $pic_url = 'noimage.png';
                        }*/


                        ?>
                        <?php $image = \common\components\Helpers\MemberHelper::getProfileImage($model->member->id);

                        if (!empty($image)) {
                            $f_cnic = \common\components\Helpers\ImageHelper::getImageFromDisk('members', $model->member->id, $image->image_name, false);
                            echo Html::img($f_cnic, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);


                        } else {
                            $pic_url = 'noimage.png';
                            echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                        }
                        ?>


                        <?php /*echo Html::img('@web/uploads/' . $pic_url, ['alt' => Yii::$app->name]); */ ?>
                    </div>
                    <div class="profile-card-name"><?= $model->member->full_name ?></div>
                    <div class="profile-card-status"><?= $model->member->parentage ?></div>
                    <div class="profile-card-location"><?= $model->member->cnic ?></div>
                </div><!--.profile-card-->
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b><?php echo $model->member->applicationsCount ?></b>
                            Applications
                        </div>
                        <div class="tbl-cell">
                            <b><?php echo $model->loansCount ?></b>
                            Loans
                        </div>
                    </div>
                </div>
                <ul class="profile-links-list">
                    <li class="nowrap">
                        <span><b>Gender: </b></span>
                        <?= \common\models\Lists::find()->where(['list_name' => 'gender', 'value' => $model->member->gender])->one()->label ?>
                    </li>
                    <li class="nowrap">
                        <span><b>Date of birth: </b></span>
                        <?= \common\components\Helpers\StringHelper::dateFormatter($model->member->dob) ?>
                    </li>
                    <li class="nowrap">
                        <span><b>Mobile: </b></span>
                        <?= isset($model->member->membersMobile->phone) ? $model->member->membersMobile->phone : '-' ?>
                    </li>
                    <li class="nowrap">
                        <span><b>Phone: </b></span>
                        <?= isset($model->member->membersPtcl->phone) ? $model->member->membersPtcl->phone : '-' ?>
                    </li>
                    <li class="nowrap">
                        <span><b>Status: </b></span>
                        <?php
                        if ($model->status == 'approved') {
                            echo ucfirst($model->status) . '  ' . '<span class="glyphicon glyphicon-ok" style="color:green"></span>';
                        } else {
                            echo ucfirst($model->status) . '  ' . '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
                        }
                        ?>
                    </li>
                    <?php
                    if ($model->status == 'rejected') { ?>
                        <li>
                            <span><b>Reject Reason: </b></span>
                            <?= isset($model->reject_reason) ? $model->reject_reason : '-' ?>
                        </li>
                    <?php } ?>
                </ul>
            </section>
            <!--.box-typical-->
        </div><!--.col- -->
        <div class="col-xl-9 col-lg-8">
            <section class="tabs-section">
                <div class="tabs-section-nav tabs-section-nav-left">
                    <ul class="nav" role="tablist">
                        <li class="nav-link active">
                            <a class="nav-item" href="#tabs-2-tab-1" role="tab" data-toggle="tab">
                                <span class="nav-link-in">Application Info</span>
                            </a>
                        </li>

                        <?php
                        $tab_id = 2;
                        foreach ($data as $key => $d) {

                            ?>
                            <li class="nav-link">
                                <a class="nav-item" href="#tabs-2-tab-<?php echo $key + $tab_id ?>" role="tab"
                                   data-toggle="tab">
                                    <?php $dex = explode('_', $d['type']); ?>
                                    <span class="nav-link-in"><?php echo ucwords($dex[1] . ' ' . $dex[0]); ?></span>
                                </a>
                            </li>
                            <?php
                            $tab_id++;
                        }

                        ?>

                        <li class="nav-link">
                            <a class="nav-item" href="#tabs-2-tab-41" role="tab" data-toggle="tab">
                                <span class="nav-link-in">Documents/Pictures</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <?php if (isset($model->applicationsLogs) && !empty($model->applicationsLogs)) {
                                echo Html::button('Logs', ['id' => 'modelButton', 'value' => \yii\helpers\Url::to(['applications/logs', 'id' => $model->id]), 'class' => 'nav-link nav-link-in']);
                            } ?>
                        </li>
                        <li class="nav-link">
                            <a class="nav-item" href="#tabs-2-tab-51" role="tab" data-toggle="tab">
                                <span class="nav-link-in">Nadra Verisys</span>
                            </a>
                        </li>
                    </ul>
                </div><!--.tabs-section-nav-->


                <div class="tab-content no-styled profile-tabs">
                    <div role="tabpanel" class="tab-pane active" id="tabs-2-tab-1">
                        <section class="box-typical box-typical-padding">
                            <div class="row">
                                <!--<section class="box-typical">-->
                                <div class="col-md-4">
                                    <article class="profile-info-item">
                                        <header class="profile-info-item-header">
                                            <i class="font-icon font-icon-doc"></i>
                                            <b>Application Information</b>
                                        </header>
                                        <div class="box-typical-inner">
                                            <p>
                                                <b>Fee</b>
                                                : <?= isset($model->fee) ? 'RS. ' . number_format($model->fee) : '0'; ?>
                                            </p>
                                            <p>
                                                <b>Application No</b> : <?= $model->application_no ?>
                                            </p>
                                            <p>
                                                <b>Application Date</b>
                                                : <?= \common\components\Helpers\StringHelper::dateFormatter($model->application_date) ?>
                                            </p>
                                            <p>
                                                <b>No Of Times</b> : <?= $model->no_of_times; ?>
                                            </p>
                                            <p>
                                                <b>Business Condition</b> : <?= $model->bzns_cond; ?>
                                            </p>
                                            <p>
                                                <b>Urban/Rural </b>
                                                : <?= ($model->is_urban == '1') ? 'Urban' : 'Rural'; ?>
                                            </p>
                                            <p>
                                                <b>Requested Amount </b> : <?= number_format($model->req_amount); ?>
                                            </p>
                                            <?php if ($model->recommended_amount > 0) { ?>
                                                <p>
                                                    <b>Recommended Amount </b>
                                                    : <?= number_format($model->recommended_amount); ?>
                                                </p>
                                            <?php } ?>
                                            <p>
                                                <b>CIB Receipt No </b>
                                                : <?= isset($model->cib->receipt_no) ? $model->cib->receipt_no : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>CIB Fee </b>
                                                : <?= isset($model->cib->fee) ? $model->cib->fee : 'Not Set'; ?>
                                            </p>
                                        </div>
                                    </article><!--.profile-info-item-->

                                </div>
                                <div class="col-md-4">
                                    <article class="profile-info-item">
                                        <header class="profile-info-item-header">
                                            <i class="font-icon font-icon-view-rows"></i>
                                            <b>Credit Structure Information</b>
                                        </header>
                                        <div class="box-typical-inner">
                                            <p>
                                                <b>Region</b>
                                                : <?= isset($model->region->name) ? $model->region->name : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>Area</b>
                                                : <?= isset($model->area->name) ? $model->area->name : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>Branch</b>
                                                : <?= isset($model->branch->name) ? $model->branch->name : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>Team</b>
                                                : <?= isset($model->team->name) ? $model->team->name : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>Field</b>
                                                : <?= isset($model->field->name) ? $model->field->name : 'Not Set'; ?>
                                                <?= isset($model->field->userStructureMapping->user->username) ? '(' . $model->field->userStructureMapping->user->username . ')' : '(--)'; ?>

                                            </p>
                                        </div>
                                    </article><!--.profile-info-item-->
                                </div>
                                <div class="col-md-4">
                                    <article class="profile-info-item">
                                        <header class="profile-info-item-header">
                                            <i class="glyphicon glyphicon-sound-dolby"></i>
                                            <b>Project Information</b>
                                        </header>
                                        <div class="box-typical-inner">
                                            <p>
                                                <b>Project</b>
                                                : <?= isset($model->project->name) ? $model->project->name : 'Not Set'; ?>
                                            </p>
                                            <?php if (!empty($model->project_table) && $model->project_table == 'projects_tevta') { ?>

                                                <p>
                                                    <b>Institute Name</b>
                                                    : <?= isset($model->projectsTevta[0]->institute_name) ? $model->projectsTevta[0]->institute_name : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Type Of Diploma</b>
                                                    : <?= isset($model->projectsTevta[0]->type_of_diploma) ? $model->projectsTevta[0]->type_of_diploma : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Duration Of Diploma</b>
                                                    : <?= isset($model->projectsTevta[0]->duration_of_diploma) ? $model->projectsTevta[0]->duration_of_diploma : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Pbte Or Ttb</b>
                                                    : <?= isset($model->projectsTevta[0]->pbte_or_ttb) ? $model->projectsTevta[0]->pbte_or_ttb : 'Not Set'; ?>
                                                </p>
                                            <?php } elseif (!empty($model->project_table) && $model->project_table == 'projects_disabled') {
                                                ?>
                                                <p>
                                                    <b>Disability</b>
                                                    : <?= isset($model->projectsDisabled[0]->disability) ? $model->projectsDisabled[0]->disability : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Nature</b>
                                                    : <?= isset($model->projectsDisabled[0]->nature) ? $model->projectsDisabled[0]->nature : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Physical Disability</b>
                                                    : <?= isset($model->projectsDisabled[0]->physical_disability) ? $model->projectsDisabled[0]->physical_disability : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Visual Disability</b>
                                                    : <?= isset($model->projectsDisabled[0]->visual_disability) ? $model->projectsDisabled[0]->visual_disability : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Communicative Disability</b>
                                                    : <?= isset($model->projectsDisabled[0]->communicative_disability) ? $model->projectsDisabled[0]->communicative_disability : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Disabilities Instrument</b>
                                                    : <?= isset($model->projectsDisabled[0]->disabilities_instruments) ? $model->projectsDisabled[0]->disabilities_instruments : 'Not Set'; ?>
                                                </p>
                                            <?php } elseif (!empty($model->project_table) && $model->project_table == 'projects_sidb' && isset($model->projectsSidb[0]->disability)) {
                                                ?>
                                                <p>
                                                    <b>Disability</b>
                                                    : <?= isset($model->projectsSidb[0]->disability) ? $model->projectsSidb[0]->disability : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Nature</b>
                                                    : <?= isset($model->projectsSidb[0]->nature) ? $model->projectsSidb[0]->nature : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Physical Disability</b>
                                                    : <?= isset($model->projectsSidb[0]->physical_disability) ? $model->projectsSidb[0]->physical_disability : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Visual Disability</b>
                                                    : <?= isset($model->projectsSidb[0]->visual_disability) ? $model->projectsSidb[0]->visual_disability : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Communicative Disability</b>
                                                    : <?= isset($model->projectsSidb[0]->communicative_disability) ? $model->projectsSidb[0]->communicative_disability : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Disabilities Instrument</b>
                                                    : <?= isset($model->projectsSidb[0]->disabilities_instruments) ? $model->projectsSidb[0]->disabilities_instruments : 'Not Set'; ?>
                                                </p>
                                            <?php } elseif (!empty($model->project_table) && $model->project_table == 'projects_agriculture') {
                                                ?>

                                                <p>
                                                    <b>Owner</b>
                                                    : <?= isset($model->projectsAgriculture[0]->owner) ? $model->projectsAgriculture[0]->owner : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Project Land Area Size</b>
                                                    : <?= isset($model->projectsAgriculture[0]->land_area_size) ? $model->projectsAgriculture[0]->land_area_size : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Land Area Type</b>
                                                    : <?= isset($model->projectsAgriculture[0]->land_area_type) ? $model->projectsAgriculture[0]->land_area_type : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Village Name</b>
                                                    : <?= isset($model->projectsAgriculture[0]->village_name) ? $model->projectsAgriculture[0]->village_name : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Crop tType</b>
                                                    : <?= isset($model->projectsAgriculture[0]->crop_type) ? $model->projectsAgriculture[0]->crop_type : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Crops</b>
                                                    : <?= isset($model->projectsAgriculture[0]->crops) ? $model->projectsAgriculture[0]->crops : 'Not Set'; ?>
                                                </p>
                                            <?php } ?>
                                        </div>
                                    </article><!--.profile-info-item-->
                                </div>
                                <!--</section>--><!--.box-typical-->
                            </div>
                            <div class="row">
                                <!--<section class="box-typical">-->
                                <div class="col-md-4">
                                    <article class="profile-info-item">
                                        <header class="profile-info-item-header">
                                            <i class="font-icon font-icon-users"></i>
                                            <b>Who Will Work</b>
                                        </header>
                                        <div class="box-typical-inner">
                                            <p>
                                                <b>Who Will Work</b>
                                                : <?= isset($model->who_will_work) ? ucfirst($model->who_will_work) : 'Not Set'; ?>
                                            </p>
                                            <?php if ($model->who_will_work != 'self') { ?>
                                                <p>
                                                    <b>Name Of Other</b>
                                                    : <?= isset($model->name_of_other) ? $model->name_of_other : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Other Cnic</b>
                                                    : <?= isset($model->other_cnic) ? $model->other_cnic : 'Not Set'; ?>
                                                </p>
                                            <?php } ?>
                                        </div>
                                    </article><!--.profile-info-item-->
                                </div>
                                <div class="col-md-8">
                                    <article class="profile-info-item">
                                        <header class="profile-info-item-header">
                                            <i class="font-icon font-icon-help"></i>
                                            <b>Loan Purpose Information</b>
                                        </header>
                                        <div class="box-typical-inner">
                                            <p>
                                                <b>Product</b>
                                                : <?= isset($model->product->name) ? $model->product->name : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>Activity</b>
                                                : <?= isset($model->activity->name) ? $model->activity->name : 'Not Set'; ?>
                                            </p>
                                            <?php if (!empty($model->sub_activity)) { ?>
                                                <p>
                                                    <b>Sub Activity</b>
                                                    : <?= !empty($model->sub_activity) ? $model->sub_activity : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Client Contribution</b>
                                                    : <?= !empty($model->client_contribution) ? number_format($model->client_contribution) : 'Not Set'; ?>
                                                </p>
                                            <?php } ?>
                                        </div>
                                    </article><!--.profile-info-item-->
                                </div>

                                <!--</section>--><!--.box-typical-->
                            </div>
                        </section>
                        <section class="box-typical">
                            <header class="box-typical-header-sm">Application Actions</header>
                            <article class="profile-info-item">
                                <header class="profile-info-item-header">
                                    <i class="font-icon font-icon-award"></i>
                                    Action Logs
                                </header>
                                <?php
                                if (!empty($model->actions)) {
                                    ?>

                                    <div class="box-typical-inner">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>Action</th>
                                                    <th>Assign to</th>
                                                    <th>Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                <?php
                                                foreach ($model->actions as $key => $action) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo ucfirst($action->action) ?>
                                                            <br>
                                                            <p style="font-size: 10px;color: green;">
                                                                Created
                                                                at: <?= ($action->created_at != 0) ? date('d M Y H:i', $action->created_at) : '-' ?>
                                                            </p>
                                                            <p style="font-size: 10px;color: green;">
                                                                Last Updated
                                                                at: <?= ($action->updated_at != 0) ? date('d M Y H:i', $action->updated_at) : '-' ?>
                                                            </p>
                                                        </td>
                                                        <td><?php echo $action->user->fullname; ?></td>
                                                        <td><?php echo ($action->status == 1) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>

                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <?php
                                }
                                ?>

                            </article><!--.profile-info-item-->
                        </section>
                    </div><!--.tab-pane-->

                    <?php $tab_id = 2;
                    foreach ($data as $key => $d) {
                        ?>
                        <div role="tabpanel" class="tab-pane" id="tabs-2-tab-<?php echo $key + $tab_id ?>">
                            <section class="box-typical box-typical-padding">
                                <header class="box-typical-header">
                                    <div class="tbl-row">
                                    </div>
                                </header>
                                <?php

                                $relation = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $d['type']))));
                                //                                echo $relation;
                                $model_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $d['type'])));
                                $class = '\common\models\\' . $model_name;

                                if (isset($model->$relation) && !empty($model->$relation)) {
                                if (empty($model->loan)) {
                                    $href = '/' . (str_replace('_', '-', $d['type'])) . '/update';
                                    echo \yii\helpers\Html::a('<span class="btn btn-primary pull-right" style="margin-bottom: 2px">Update</span>', [$href, 'id' => $model->$relation->id], ['role' => 'modal-remote', 'title' => 'Update']);
                                }
                                ?>
                                <?php echo $this->render('_result', [
                                    'model' => $model,
                                    'relation' => $relation,
                                    'd' => $d
                                ]); ?>
                                <!--<h6><b><?php echo ucwords(str_replace('_', ' ', $d['type'])); ?> Image</b></h6>

                                        <?php
                                $class = '\common\models\\' . $model_name;
                                //$model = $class::find()->where(['id' => $parent_id])->one();
                                $social_appr = $class::find()->where(['id' => $model->$relation->id])->one();
                                if ($model_name != 'AppraisalsAgriculture') {
                                    (isset($social_appr->latitude) && isset($social_appr->latitude)) ? $social_appr->coordinates = $social_appr->latitude . ',' . $social_appr->longitude : $social_appr->coordinates = '33.5753184,73.14307400000007';
                                }
                                $form = ActiveForm::begin();
                                if ($social_appr->latitude != 0 || $social_appr->longitude != 0) {
                                    echo $form->field($social_appr, 'coordinates')->widget('\pigolab\locationpicker\CoordinatesPicker', [
                                        'key' => 'AIzaSyD5vhqpbUx6YQTWXrnjt1MXwh7rkI27OkI',   // optional , Your can also put your google map api key
                                        'valueTemplate' => '{latitude},{longitude}', // Optional , this is default result format
                                        'options' => [
                                            'style' => 'width: 100%; height: 300px',  // map canvas width and height
                                        ],
                                        'enableSearchBox' => true, // Optional , default is true
                                        'searchBoxOptions' => [ // searchBox html attributes
                                            'style' => 'width: 500px;', // Optional , default width and height defined in css coordinates-picker.css
                                        ],
                                        'mapOptions' => [
                                            // set google map optinos
                                            'rotateControl' => true,
                                            'scaleControl' => false,
                                            'streetViewControl' => true,
                                            'mapTypeId' => new JsExpression('google.maps.MapTypeId.ROADMAP'),
                                            'heading' => 90,
                                            'tilt' => 45,

                                            'mapTypeControl' => true,
                                            'mapTypeControlOptions' => [
                                                'style' => new JsExpression('google.maps.MapTypeControlStyle.HORIZONTAL_BAR'),
                                                'position' => new JsExpression('google.maps.ControlPosition.TOP_CENTER'),
                                            ]
                                        ],
                                        'clientOptions' => [
                                            'radius' => 50,
                                            'addressFormat' => 'street_number',
                                            'inputBinding' => [
                                                'latitudeInput' => new JsExpression("$('#us2-lat')"),
                                                'longitudeInput' => new JsExpression("$('#us2-lon')"),
                                                'locationNameInput' => new JsExpression("$('#us2-address')")
                                            ],
                                            'autoComplete' => true,
                                        ]
                                    ])->label(false);

                                }

                                ?>
                                        <?php ActiveForm::end(); ?>
                                    <?php } else { ?>
                                        No record found!
                                    <?php } ?>

                                    <!--  </section>-->
                            </section>
                        </div><!--.tab-pane-->
                        <?php $tab_id++;
                    } ?>

                    <div role="tabpanel" class="tab-pane" id="tabs-2-tab-41">
                        <section class="box-typical box-typical-padding">

                            <?php echo \yii\helpers\Html::a('<span class="btn btn-primary pull-right" style="margin-bottom: 2px">Add Document/Pictures</span>', ['add-document', 'id' => $model->id], ['target' => 'blank'], ['role' => 'modal-remote', 'title' => 'Add Document']); ?>

                            <div class="row">


                                <?php
                                $images = \common\components\Helpers\ApplicationHelper::getDocument($model->id);
                                $doc_names = \common\components\Helpers\ApplicationHelper::getDocumentsTitle();


                                /*$image = \common\components\Helpers\MemberHelper::getProfileImage($model->member->id);

                                if (!empty($image)) {
                                    $f_cnic = \common\components\Helpers\ImageHelper::getImageFromDisk('member', $model->member->id, $image->image_name, false);
                                    echo Html::img($f_cnic, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);


                                } else {
                                    $pic_url = 'noimage.png';
                                    echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                                }*/

                                foreach ($images as $image) {
                                    if (!empty($image)) {
                                        //$f_cnic = \common\components\Helpers\ImageHelper::getImageFromDisk('applications', $model->id, $image->image_name, false);
                                        $f_cnic = \common\components\Helpers\ImageHelper::getImageFromDisk('applications', $model->id, $image->image_name, false);
                                        ?>
                                        <div class="col-sm-6">
                                            <h6><b><?= $doc_names[$image->image_type] ?></b></h6>
                                            <?php echo Html::img($f_cnic, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']); ?>
                                        </div>
                                        <?php


                                    } else {
                                        $pic_url = 'noimage.png'; ?>
                                        <div class="col-sm-6">
                                            <h6><b><?= $doc_names[$image->image_type] ?></b></h6>
                                            <?php echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']); ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                <?php } ?>


                            </div>
                        </section>
                    </div><!--.tab-pane-->

                    <div role="tabpanel" class="tab-pane" id="tabs-2-tab-51">
                        <section class="box-typical box-typical-padding">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="col-sm-6">
                                        <h6><b>Front CNIC</b></h6>
                                        <?php
                                        $image = \common\components\Helpers\MemberHelper::getFCnic($model->member_id);

                                        if (!empty($image)) {
                                            $f_cnic = \common\components\Helpers\ImageHelper::getImageFromDisk('members', $model->member_id, $image->image_name, false);
                                            echo Html::img($f_cnic, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);

                                        } else {
                                            $pic_url = 'noimage.png';
                                            echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                                        }
                                        ?>
                                    </div>

                                    <div class="col-sm-6">
                                        <h6><b>Back CNIC</b></h6>
                                        <?php
                                        $image = \common\components\Helpers\MemberHelper::getBCnic($model->member_id);

                                        if (!empty($image) && $image != null) {
                                            $f_cnic = \common\components\Helpers\ImageHelper::getImageFromDisk('members', $model->member_id, $image->image_name, false);
                                            echo Html::img($f_cnic, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);


                                        } else {
                                            $pic_url = 'noimage.png';
                                            echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h6><b>Nadra Document</b></h6>
                                    <?php
                                    $nadra_pdf_image = \common\models\NadraVerisys::find()
                                        ->where(['application_id' => $model->id])->andWhere(['status' => 1])->andWhere(['deleted' => 0])->one();
                                    $image = \common\models\NadraVerisys::find()
                                        ->where(['application_id' => $model->id])->andWhere(['status' => 0])->andWhere(['deleted' => 0])->one();
                                    if (!empty($nadra_pdf_image) && $nadra_pdf_image != null) {
                                        $attachment_path_app = \common\components\Helpers\ImageHelper::getAttachmentPath() . '/uploads/members/' . $model->id . '/' . $image->document_name;
                                        $f_cnic = \common\components\Helpers\ImageHelper::getImageFromDisk('members', $model->id, $image->document_name, false);
                                        echo Html::a('Download Nadra Document', [
                                            'applications/pdf',
                                            'id' => $model->id,
                                            'type' => 'app'
                                        ], [
                                            'class' => 'btn btn-primary',
                                            'target' => '_blank',
                                        ]);
                                        // echo Html::img($f_cnic, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                                    } elseif (!empty($image) && $image != null) {
                                        $attachment_path_app = \common\components\Helpers\ImageHelper::getImageFromDisk('members', $model->id, $image->document_name, false);
                                        if (!empty($attachment_path_app) && $attachment_path_app != null) {
                                            echo Html::img($attachment_path_app, ['alt' => 'No Image Found', 'class' => '', 'style' => 'width:70%']);
                                        } else {
                                            $pic_url = 'noimage.png';
                                            echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                                        }
                                    } else {
//                                        $imageMem = \common\models\Images::find()
//                                            ->where(['parent_id' => $model->member_id])
//                                            ->andWhere(['parent_type' => 'members'])
//                                            ->andWhere(['image_type' => 'nadra_document'])
//                                            ->one();
//                                        if (!empty($imageMem) && $imageMem != null) {
//                                            $attachment_path_mem = \common\components\Helpers\ImageHelper::getAttachmentPath() . '/uploads/members/' . $model->member_id . '/' . $image->image_name;
////                                                $f_cnic_mem=\common\components\Helpers\ImageHelper::getImageFromDisk('members',$model->member_id,$image->image_name,false);
//                                                echo Html::a('Download Nadra Document', [
//                                                    'applications/pdf',
//                                                    'id' => $model->member_id,
//                                                    'type' => 'member'
//                                                ], [
//                                                    'class' => 'btn btn-primary',
//                                                    'target' => '_blank',
//                                                ]);
//                                        }else{
                                        $pic_url = 'noimage.png';
                                        echo Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
//                                        }
                                    }
                                    ?>
                                    <?php $doc_3 = new \common\models\Images() ?>
                                    <?php $form = \yii\widgets\ActiveForm::begin(['action' => 'add-nadra-document?id=' . $model->id]); ?>
                                    <?= $form->field($doc_3, 'image_data', ['enableClientValidation' => false])->fileInput(['accept' => ".pdf"])->label(false) ?>
                                    <br>

                                    <?php if (!Yii::$app->request->isAjax) { ?>
                                        <div class="form-group">
                                            <?= Html::submitButton($doc_3->isNewRecord ? 'Upload Nadra Document' : 'Upload Nadra Document', ['class' => $doc_3->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                        </div>
                                    <?php } ?>
                                    <?php \yii\widgets\ActiveForm::end(); ?>
                                    <?php if ($modelNadraVarisys->status == 0) { ?>
                                        <?php $form = \yii\widgets\ActiveForm::begin(['action' => 'update-nadra-status?id=' . $model->id]); ?>
                                        <?php if (!Yii::$app->request->isAjax) { ?>
                                            <div class="form-group">
                                                              <?= Html::submitButton('Verify Nadra', ['class' => 'btn btn-primary pull-right']) ?>
                                            </div>
                                        <?php } ?>
                                        <?php \yii\widgets\ActiveForm::end(); ?>
                                    <?php } else { ?>
                                        <div class="form-group">
                                            <a href="javascript:void(0)" class="btn btn-primary">Verify Nadra
                                                Performed</a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </section>
                    </div>
                </div><!--.tab-content-->


            </section><!--.tabs-section-->
        </div>
    </div><!--.row-->
</div><!--.container-fluid-->
<?php

\yii\bootstrap\Modal::begin([
    'header' => '<h4 class="modal-title">Logs</h4>',
    'headerOptions' => ['style' => ['display' => 'block']],
    'id' => 'model',
    'size' => 'model-lg',
    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]),
]);

echo "<div id='modelContent'></div>";

\yii\bootstrap\Modal::end();

?>
<?php
$script = "$(function(){
    $('#modelButton').click(function(){
        $('.modal').modal('show')
            .find('#modelContent')
            .load($(this).attr('value'));
    });
});";
$this->registerJs($script);
?>

<script src="/js/sweetalert.min.js"></script>
