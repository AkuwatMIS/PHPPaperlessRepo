<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UsersCopy */
$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View User</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <h4><b>User Details<?= '(Name : ' . $model->fullname . ' - S/O : ' . $model->father_name . ')' ?></b></h4>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="glyphicon glyphicon-user"></i>
                                <b>Personal Information</b>
                            </header>
                            <div class="box-typical-inner">
                                <p>
                                    <b>User Name</b> : <?= isset($model->username)?($model->username):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Full Name</b> : <?= isset($model->fullname)?($model->fullname):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Father Name</b> : <?= isset($model->father_name)?($model->father_name):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Cnic</b> : <?= isset($model->cnic)?($model->cnic):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Employee Code</b> : <?= isset($model->emp_code)?($model->emp_code):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Joining Date</b> : <?= isset($data->joining_date) ? date('Y-M-d', $data->joining_date) : 'Not Set'; ?>
                                </p>
                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                    <div class="col-md-6">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="glyphicon glyphicon-phone"></i>
                                <b>Contact Information</b>
                            </header>
                            <div class="box-typical-inner">
                                <p>
                                    <b>Email  Address</b> : <?= isset($model->email)?($model->email):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Alternate Email  Address</b> : <?= isset($model->alternate_email)?($model->alternate_email):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Mobile</b> : <?= isset($model->mobile)?($model->mobile):'Not Set'; ?>
                                </p>
                            </div>
                        <!--</article>--><!--.profile-info-item-->
                    </div>
                </div>
                <!--<?/*= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        ['attribute' => 'city_id',
                            'label' => 'City',
                            'value' => function ($data) {
                                return isset($data->city->name) ? $data->city->name : '';
                            }
                        ],
                        'username',
                        //'fullname',
                        //'father_name',
                        'cnic',
                        'emp_code',
                        //'email:email',
                        //'alternate_email:email',
                        //'image',
                        'mobile',
                    ],
                ]) */?>-->
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="box-typical box-typical-padding">
                <h4>Current Hierarchy</h4>
                <?php if (key($auth['roles']) == 'RC' || key($auth['roles']) == 'RM' || key($auth['roles']) == 'RA' || key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>

                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Region</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($array['region']->userRegion->name) ? $array['region']->userRegion->name : '-' ?>
                        </div>
                    </div>
                </div>
                <?php }?>
                <?php if (key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Area</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($array['area']->userArea->name) ? $array['area']->userArea->name : '-' ?>
                        </div>
                    </div>
                </div>
                <?php }?>
                <?php if (key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Branch</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($array['branch']->userBranch->name) ? $array['branch']->userBranch->name : '-' ?>
                        </div>
                    </div>
                </div>
                <?php }?>
                <?php if (key($auth['roles']) == 'LO') {?>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Team</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($array['team']->userTeam->name) ? $array['team']->userTeam->name : '--' ?>
                        </div>
                    </div>
                </div>
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b>Field</b>
                        </div>
                        <div class="tbl-cell">
                            <?= isset($array['field']->userField->name) ? $array['field']->userField->name : '--' ?>
                        </div>
                    </div>
                </div>
                <?php }?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="box-typical box-typical-padding">
                <h4>Transfer Deatils</h4>

                <?php $form = \yii\widgets\ActiveForm::begin([
                    'action' => ['user-hierarchy-change-request/create-request'],
                    'method' => 'post',
                ]); ?>
                <?php
                if (key($auth['roles']) == 'RC' || key($auth['roles']) == 'RM' || key($auth['roles']) == 'RA' || key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {

                    echo $form->field($change_model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region');
                } ?>
                <?php
                if (key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {
                    $value = !empty($change_model->area_id) ? $change_model->area->name : null;
                    echo $form->field($change_model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                        'pluginOptions' => [
                            'depends' => ['userhierarchychangerequest-region_id'],
                            'initialize' => true,
                            'initDepends' => ['userhierarchychangerequest-region_id'],
                            'placeholder' => 'Select Area',
                            'url' => \yii\helpers\Url::to(['/structure/fetch-area-by-region'])
                        ],
                        'data' => $value ? [$change_model->area_id => $value] : []
                    ])->label('Area');
                }
                ?>
                <?php
                if (key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {

                    $value = !empty($change_model->branch_id) ? $change_model->id : null;
                    echo $form->field($change_model, 'branch_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                        'pluginOptions' => [
                            'depends' => ['userhierarchychangerequest-area_id'],
                            'initialize' => true,
                            'initDepends' => ['userhierarchychangerequest-area_id'],
                            'placeholder' => 'Select Branch',
                            'url' => \yii\helpers\Url::to(['/structure/fetch-branch-by-area'])
                        ],
                        'data' => $value ? [$change_model->branch_id => $value] : []
                    ])->label('Branch');
                }
                ?>
                <?php
                if (key($auth['roles']) == 'LO') {
                    $value = !empty($change_model->team_id) ? $change_model->team_id : null;
                    echo $form->field($change_model, 'team_id')->widget(kartik\depdrop\DepDrop::classname(), [
                        'pluginOptions' => [
                            'depends' => ['userhierarchychangerequest-branch_id'],
                            'initialize' => true,
                            'initDepends' => ['userhierarchychangerequest-branch_id'],
                            'placeholder' => 'Select Team',
                            'url' => \yii\helpers\Url::to(['/structure/fetch-team-by-branch'])
                        ],
                        'data' => $value ? [$change_model->team_id => $value] : []
                    ])->label('Team');
                }
                ?>
                <?php
                if (key($auth['roles']) == 'LO') {
                    $value = !empty($change_model->field_id) ? $change_model->field_id : null;
                    echo $form->field($change_model, 'field_id')->widget(kartik\depdrop\DepDrop::classname(), [
                        'pluginOptions' => [
                            'depends' => ['userhierarchychangerequest-team_id'],
                            'initialize' => true,
                            'initDepends' => ['userhierarchychangerequest-team_id'],
                            'placeholder' => 'Select Field',
                            'url' => \yii\helpers\Url::to(['/structure/fetch-field-by-team'])
                        ],
                        'data' => $value ? [$change_model->field_id => $value] : []
                    ])->label('Field');
                }
                ?>
                <?= $form->field($change_model, "created_by")->hiddenInput(['value' => yii::$app->user->getId()])->label(false) ?>
                <?= $form->field($change_model, "user_id")->hiddenInput(['value' => $model->id])->label(false) ?>
                <?= $form->field($change_model, "assigned_to")->hiddenInput(['value' => isset($auth['rm_id']->user_id)?$auth['rm_id']->user_id:'0'])->label(false) ?>
                <?= $form->field($change_model, "recommended_by")->hiddenInput(['value' => '0'])->label(false) ?>
                <?= $form->field($change_model, "status")->hiddenInput(['value' => 'pending'])->label(false) ?>
                <?php   if (key($auth['roles']) == 'RC' || key($auth['roles']) == 'RM' || key($auth['roles']) == 'RA' || key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                <div class="form-group" style="margin-top: 20px">
                    <?= \yii\helpers\Html::submitButton('Transfer', ['class' => 'btn btn-primary pull-right']) ?>
                </div>
                <br>
                <?php }?>
            </div>

            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>