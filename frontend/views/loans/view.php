<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */
$this->title = $model->sanction_no;
$this->params['breadcrumbs'][] = ['label' => 'Loans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
?>
    <div class="container-fluid">
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h4>View Loan</h4>
                    </div>
                    <?php if(in_array('frontend_get-templatetemplates',$permissions))
                    { ?>
                        <?php $templates=\common\models\Templates::find()->where(['module'=>'loans','deleted'=>0])->all();?>
                        <div class="dropdown pull-right">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> Templates
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <?php foreach ($templates as $temp) { ?>

                                    <li><a  target="_blank"
                                            href="/templates/get-template-view?module_id=<?= $model->id ?>&module=loans&template_id=<?= $temp->id?>"><?= $temp->template_name?></a>
                                    </li>
                                <?php } ?>

                            </ul>
                        </div>
                    <?php }?>
                </div>
            </div>
        </header>
        <div class="row">
            <div class="col-lg-3">
                <section class="box-typical">
                    <div class="profile-card">
                        <div class="profile-card-photo">
                            <?php
                            $image = \common\components\Helpers\MemberHelper::getProfileImage($model->application->member->id);
                            if (!empty($image)) {
                                //$user_image = (!empty($image->image_name)) ? ($image->image_name) : 'noimage.png';
                                $user_image =\common\components\Helpers\ImageHelper::getImageFromDisk('members',$model->application->member->id,$image->image_name,false);
                                //$pic_url = $image->parent_type . "/" . $model->application->member->id . "/" . $user_image;
                                echo Html::img($user_image, ['alt' => Yii::$app->name]);
                            } else {
                                $pic_url = 'noimage.png';
                                echo Html::img('@web/uploads/' . $pic_url, ['alt' => Yii::$app->name]);
                            }
                            ?>
                            <?php /*echo Html::img('@web/uploads/' . $pic_url, ['alt' => Yii::$app->name]); */?>
                        </div>
                        <div class="profile-card-name"><?= $model->application->member->full_name ?></div>
                        <div class="profile-card-status"><?= $model->application->member->parentage ?></div>
                        <div class="profile-card-location"><?= $model->application->member->cnic ?></div>
                    </div><!--.profile-card-->
                    <div class="profile-statistic tbl">
                        <div class="tbl-row">
                            <div class="tbl-cell">
                                <b>1</b>
                                Applications
                            </div>
                            <div class="tbl-cell">
                                <b>1</b>
                                Loans
                            </div>
                        </div>
                    </div>
                    <ul class="profile-links-list">
                        <li class="nowrap">
                            <span><b>Gender: </b></span>
                            <?= \common\models\Lists::find()->where(['list_name' => 'gender', 'value' => $model->application->member->gender])->one()->label ?>
                        </li>
                        <li class="nowrap">
                            <span><b>Date of birth: </b></span>
                            <?= date('d M Y', $model->application->member->dob) ?>
                        </li>
                        <li class="nowrap">
                            <span><b>Mobile: </b></span>
                            <?= isset($model->application->member->membersMobile->phone) ? $model->application->member->membersMobile->phone : '-' ?>
                        </li>
                        <li class="nowrap">
                            <span><b>Phone: </b></span>
                            <?= isset($model->application->member->membersPtcl->phone) ? $model->application->member->membersPtcl->phone : '-' ?>
                        </li>
                        <li class="nowrap">
                            <span><b>Status: </b></span>
                            <?= $model->status ?>
                        </li>
                    </ul>
                </section>

            </div><!--.col- -->

            <div class="col-xl-9 col-lg-8">
                <section class="tabs-section">
                    <div class="tabs-section-nav tabs-section-nav-left">
                        <ul class="nav" role="tablist">
                            <li class="nav-link active">
                                <a class="nav-item" href="#tabs-2-tab-1" role="tab" data-toggle="tab">
                                    <span class="nav-link-in">Loan Info</span>
                                </a>
                            </li>
                            <li class="nav-link">
                                <a class="nav-item" href="#tabs-2-tab-2" role="tab" data-toggle="tab">
                                    <span class="nav-link-in">Recovery Info</span>
                                </a>
                            </li>
                            <li class="nav-link">
                                <a class="nav-item" href="#tabs-2-tab-3" role="tab" data-toggle="tab">
                                    <span class="nav-link-in">MDP Info</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <?php if (isset($model->applicationsLogs) && !empty($model->applicationsLogs)) {
                                    echo Html::button('Logs', ['id' => 'modelButton', 'value' => \yii\helpers\Url::to(['applications/logs', 'id' => $model->id]), 'class' => 'nav-link nav-link-in']);
                                } ?>
                            </li>
                        </ul>
                    </div><!--.tabs-section-nav-->

                    <div class="tab-content no-styled profile-tabs">
                        <div role="tabpanel" class="tab-pane active" id="tabs-2-tab-1">
                            <section class="box-typical box-typical-padding">
                                <div class="row">
                                    <!--<section class="box-typical">-->
                                    <div class="col-md-6">
                                        <article class="profile-info-item">
                                            <header class="profile-info-item-header">
                                                <i class="font-icon font-icon-notebook-bird"></i>
                                                <b>Loan Information</b>
                                            </header>
                                            <div class="box-typical-inner">
                                                <p>
                                                    <b>Sanction No</b> : <?= ucfirst($model->sanction_no); ?>
                                                </p>
                                                <p>
                                                    <b>Application No</b> : <?= $model->application->application_no ?>
                                                </p>
                                                <p>
                                                    <b>Loan Amount</b>
                                                    : <?= 'RS. ' . number_format($model->loan_amount); ?>
                                                </p>
                                                <p>
                                                    <b>Group No</b>
                                                    : <?= isset($model->group->grp_no) ? $model->group->grp_no : 'Not Set' ?>
                                                </p>
                                                <p>
                                                    <b>Cheque No</b>
                                                    : <?= ($model->cheque_no); ?>
                                                </p>
                                                <p>
                                                    <b>Installment Type</b>
                                                    : <?= ucfirst($model->inst_type); ?>
                                                </p>
                                                <p>
                                                    <b>Installment Amount</b>
                                                    : <?= 'RS. ' . number_format($model->inst_amnt); ?>
                                                </p>
                                                <p>
                                                    <b>Installment Months</b>
                                                    : <?= number_format($model->inst_months); ?>
                                                </p>
                                                <p>
                                                    <b>Approved Date</b>
                                                    : <?= date('d M Y', $model->date_approved) ?>
                                                </p>
                                                <p>
                                                    <b>Disbursement Date</b>
                                                    : <?= date('d M Y', $model->date_disbursed) ?>
                                                </p>
                                                <p>
                                                    <b>Loan Compelted Date</b>
                                                    : <?= date('d M Y', $model->loan_completed_date) ?>
                                                </p>
                                            </div>
                                        </article><!--.profile-info-item-->

                                    </div>
                                    <div class="col-md-6">
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
                                                    <?= isset($model->field->userStructureMapping->user->username) ? '('.$model->field->userStructureMapping->user->username.')' : '(--)'; ?>

                                                </p>
                                            </div>
                                        <!--</article>--><!--.profile-info-item-->
                                        <!--<article class="profile-info-item">-->
                                            <header class="profile-info-item-header">
                                                <i class="glyphicon glyphicon-sound-dolby"></i>
                                                <b>Project Information</b>
                                            </header>
                                            <div class="box-typical-inner">
                                                <p>
                                                    <b>Project</b>
                                                    : <?= isset($model->project->name) ? $model->project->name : 'Not Set'; ?>
                                                </p>
                                                <?php if (!empty($model->application->project_table) && $model->application->project_table == 'project_details_tevta') { ?>

                                                    <p>
                                                        <b>Institute Name</b>
                                                        : <?= isset($model->application->ProjectsTevta[0]->institute_name) ? $model->application->ProjectsTevta[0]->institute_name : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Type Of Diploma</b>
                                                        : <?= isset($model->application->ProjectsTevta[0]->type_of_diploma) ? $model->application->ProjectsTevta[0]->type_of_diploma : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Duration Of Diploma</b>
                                                        : <?= isset($model->application->ProjectsTevta[0]->duration_of_diploma) ? $model->application->ProjectsTevta[0]->duration_of_diploma : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Pbte Or Ttb</b>
                                                        : <?= isset($model->application->ProjectsTevta[0]->pbte_or_ttb) ? $model->application->ProjectsTevta[0]->pbte_or_ttb : 'Not Set'; ?>
                                                    </p>
                                                <?php } elseif (!empty($model->application->project_table) && $model->application->project_table == 'project_details_disabled') {
                                                    ?>
                                                    <p>
                                                        <b>Disability</b>
                                                        : <?= isset($model->application->ProjectsDisabled[0]->disability) ? $model->application->ProjectsDisabled[0]->disability : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Nature</b>
                                                        : <?= isset($model->application->ProjectsDisabled[0]->nature) ? $model->application->ProjectsDisabled[0]->nature : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Physical Disability</b>
                                                        : <?= isset($model->application->ProjectsDisabled[0]->physical_disability) ? $model->application->ProjectsDisabled[0]->physical_disability : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Visual Disability</b>
                                                        : <?= isset($model->application->ProjectsDisabled[0]->visual_disability) ? $model->application->ProjectsDisabled[0]->visual_disability : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Communicative Disability</b>
                                                        : <?= isset($model->application->ProjectsDisabled[0]->communicative_disability) ? $model->application->ProjectsDisabled[0]->communicative_disability : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Disabilities Instrument</b>
                                                        : <?= isset($model->application->ProjectsDisabled[0]->disabilities_instruments) ? $model->application->ProjectsDisabled[0]->disabilities_instruments : 'Not Set'; ?>
                                                    </p>
                                                <?php } elseif (!empty($model->application->project_table) && $model->application->project_table == 'project_details_agriculture') {
                                                    ?>
                                                    <p>
                                                        <b>Owner</b>
                                                        : <?= isset($model->application->ProjectsAgriculture->owner) ? $model->application->ProjectsAgriculture->owner : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>ProjecLand Area Sizet</b>
                                                        : <?= isset($model->application->ProjectsAgriculture[0]->land_area_size) ? $model->application->ProjectsAgriculture[0]->land_area_size : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Land Area Type</b>
                                                        : <?= isset($model->application->ProjectsAgriculture[0]->land_area_type) ? $model->application->ProjectsAgriculture[0]->land_area_type : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Village Name</b>
                                                        : <?= isset($model->application->ProjectsAgriculture[0]->village_name) ? $model->application->ProjectsAgriculture[0]->village_name : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Crop tType</b>
                                                        : <?= isset($model->application->ProjectsAgriculture[0]->crope_type) ? $model->application->ProjectsAgriculture[0]->crope_type : 'Not Set'; ?>
                                                    </p>
                                                    <p>
                                                        <b>Crops</b>
                                                        : <?= isset($model->application->ProjectsAgriculture[0]->crops) ? $model->application->ProjectsAgriculture[0]->crops : 'Not Set'; ?>
                                                    </p>
                                                <?php } ?>
                                            </div>
                                        </article><!--.profile-info-item-->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <article class="profile-info-item">
                                            <header class="profile-info-item-header">
                                                <i class="font-icon font-icon-help"></i>
                                                <b>Loan Purpose Information</b>
                                            </header>
                                            <div class="box-typical-inner">
                                                <p>
                                                    <b>Product</b>
                                                    : <?= isset($model->application->product->name) ? $model->application->product->name : 'Not Set'; ?>
                                                </p>
                                                <p>
                                                    <b>Activity</b>
                                                    : <?= isset($model->application->activity->name) ? $model->application->activity->name : 'Not Set'; ?>
                                                </p>
                                            </div>
                                        </article><!--.profile-info-item-->

                                    </div>
                                    <div class="col-md-6">

                                    </div>
                                </div>
                                <!--<div class="row">
                                    <div class="col-md-4 padding-0">
                                        <h5>Basic Information</h5>
                                        <? /*= DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [
                                                'sanction_no',

                                                [
                                                    'attribute' => 'application.application_no',
                                                    'label' => 'Application No'
                                                ],
                                                ['attribute' => 'loan_amount',
                                                    'value' => function ($data) {
                                                        return number_format($data->loan_amount);
                                                    }
                                                ],
                                                ['attribute' => 'project_id',
                                                    'value' => function ($data) {
                                                        return $data->project->name;
                                                    },
                                                    'label' => 'Project',

                                                ],
                                                ['attribute' => 'activity_id',
                                                    'value' => function ($data) {
                                                        return isset($data->activity->name) ? $data->activity->name : 'Not Set';
                                                    },
                                                    'label' => 'Activity',
                                                ],
                                                ['attribute' => 'product_id',
                                                    'value' => function ($data) {
                                                        return isset($data->product->name) ? $data->product->name : 'Not Set';
                                                    },
                                                    'label' => 'Product',
                                                ],
                                                ['attribute' => 'group_id',
                                                    'value' => function ($data) {
                                                        return isset($data->group->grp_no) ? $data->group->grp_no : 'Not Set';
                                                    },
                                                    'label' => 'Group No',
                                                ],
                                            ]]); */ ?>
                                    </div>
                                    <div class="col-md-4 padding-0">
                                        <h5>Credit Structure Information</h5>
                                        <? /*= DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [
                                                ['attribute' => 'region_id',
                                                    'value' => function ($data) {
                                                        return isset($data->region->name) ? $data->region->name : 'Not Set';
                                                    },
                                                    'label' => 'Region',
                                                ],
                                                ['attribute' => 'area_id',
                                                    'value' => function ($data) {
                                                        return isset($data->area->name) ? $data->area->name : 'Not Set';
                                                    },
                                                    'label' => 'Area',
                                                ],
                                                ['attribute' => 'branch_id',
                                                    'value' => function ($data) {
                                                        return isset($data->branch->name) ? $data->branch->name : 'Not Set';
                                                    },
                                                    'label' => 'Branch',
                                                ],
                                                ['attribute' => 'team_id',
                                                    'value' => function ($data) {
                                                        return isset($data->team->name) ? $data->team->name : 'Not Set';
                                                    },
                                                    'label' => 'Team',
                                                ],
                                                ['attribute' => 'field_id',
                                                    'value' => function ($data) {
                                                        return isset($data->field->name) ? $data->field->name : 'Not Set';
                                                    },
                                                    'label' => 'Field',
                                                ],
                                            ]]); */ ?>
                                    </div>
                                    <div class="col-md-4 padding-0">
                                        <h5>Other Information</h5>
                                        <? /*= DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [
                                                'cheque_no',
                                                'inst_type',
                                                ['attribute' => 'inst_amnt',
                                                    'value' => function ($data) {
                                                        return number_format($data->inst_amnt);
                                                    }
                                                ],
                                                ['attribute' => 'inst_months',
                                                    'value' => function ($data) {
                                                        return number_format($data->inst_months);
                                                    }
                                                ],

                                                ['attribute' => 'date_approved',
                                                    'value' => function ($data) {
                                                        if ($data->date_approved != 0) {
                                                            return date('Y-m-d', $data->date_approved);
                                                        } else {
                                                            return '--';
                                                        }
                                                    }
                                                ],
                                                ['attribute' => 'date_disbursed',
                                                    'value' => function ($data) {
                                                        if ($data->date_disbursed != 0) {
                                                            return date('Y-m-d', $data->date_disbursed);
                                                        } else {
                                                            return '--';
                                                        }
                                                    }
                                                ],
                                                ['attribute' => 'loan_completed_date',
                                                    'value' => function ($data) {
                                                        if ($data->loan_completed_date != 0) {
                                                            return date('Y-m-d', $data->loan_completed_date);
                                                        } else {
                                                            return '--';
                                                        }
                                                    }
                                                ],

                                            ]]); */ ?>
                                    </div>
                                </div>-->
                            </section>
                            <section class="box-typical">
                                <header class="box-typical-header-sm">Loans Actions</header>
                                <article class="profile-info-item">
                                    <header class="profile-info-item-header">
                                        <i class="font-icon font-icon-award"></i>
                                        Action Logs
                                    </header>
                                    <?php

                                    //die();
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
                                                foreach ($model->loanactions as $key => $action) {
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
                                </article><!--.profile-info-item-->
                            </section><!--.box-typical-->
                        </div>

                        <div role="tabpanel" class="tab-pane" id="tabs-2-tab-2">
                            <section class="box-typical box-typical-padding">
                                <!--    <section class="box-typical box-typical-padding">-->
                                <header class="box-typical-header">
                                    <div class="tbl-row">
                                    </div>
                                </header>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [
                                                'sanction_no',
                                                ['attribute'=>'group.grp_no',
                                                    'label'=>'Group No',
                                                ]
                                            ]]); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [
                                                ['attribute' => 'loan_amount',
                                                    'value' => function ($data) {
                                                        return 'RS. ' . number_format($data->loan_amount);
                                                    }

                                                ],
                                                ['attribute' => 'date_disbursed',
                                                    'value' => function ($data) {
                                                        return \common\components\Helpers\StringHelper::dateFormatter($data->date_disbursed);
                                                    }

                                                ],
                                            ]]); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [

                                                ['attribute' => 'inst_months',
                                                    'value' => function ($data) {
                                                        return number_format($data->inst_months);
                                                    },
                                                    'label'=>'Installment Months',

                                                ],
                                                ['attribute' => 'inst_amnt',
                                                    'value' => function ($data) {
                                                        return number_format($data->inst_amnt);
                                                    },
                                                    'label'=>'Installment Amount',

                                                ],
                                            ]]); ?>
                                    </div>
                                </div>
                                <br>
                                <h6 class="address-heading"><span class="glyphicon glyphicon-list-alt"></span>
                                    Recoveries Listing</h6>
                                <?= \yii\grid\GridView::widget([
                                    'dataProvider' => new \yii\data\ArrayDataProvider([
                                        'allModels' => $model->recoveries,
                                        'pagination' => [
                                            'pageSize' => 50,
                                        ],
                                    ]),
                                    //'filterModel' => $searchModel,
                                    'columns' => [
                                        [
                                            'class' => 'yii\grid\SerialColumn',
                                        ],
                                        ['attribute' => 'receipt_no',
                                        ],
                                        ['attribute' => 'receive_date',
                                            'value' => function ($data) {
                                                return date('d M Y', $data->receive_date);
                                            },
                                            'label' => 'Receive Date'
                                        ],
                                        ['attribute' => 'amount',
                                            'value' => function ($data) {
                                                return 'RS. ' . number_format($data->amount);
                                            },
                                            'label' => 'Recovery Amount'
                                        ],
                                        'source'
                                    ],
                                ]); ?>

                            </section>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="tabs-2-tab-3">
                            <section class="box-typical box-typical-padding">
                                <!--    <section class="box-typical box-typical-padding">-->
                                <header class="box-typical-header">
                                    <div class="tbl-row">
                                    </div>
                                </header>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [
                                                'sanction_no',
                                                ['attribute'=>'group.grp_no',
                                                    'label'=>'Group No',
                                                ]
                                            ]]); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [
                                                ['attribute' => 'loan_amount',
                                                    'value' => function ($data) {
                                                        return 'RS. ' . number_format($data->loan_amount);
                                                    }

                                                ],
                                                ['attribute' => 'date_disbursed',
                                                    'value' => function ($data) {
                                                        return date('d M Y', $data->date_disbursed);
                                                    }

                                                ],
                                            ]]); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= DetailView::widget([
                                            'model' => $model,
                                            'attributes' => [

                                                ['attribute' => 'inst_months',
                                                    'value' => function ($data) {
                                                        return number_format($data->inst_months);
                                                    },
                                                    'label'=>'Installment Months'


                                                ],
                                                ['attribute' => 'inst_amnt',
                                                    'value' => function ($data) {
                                                        return number_format($data->inst_amnt);
                                                    },
                                                    'label'=>'Installment Amount'

                                                ],
                                            ]]); ?>
                                    </div>
                                </div>
                                <br>
                                <h6 class="address-heading"><span class="glyphicon glyphicon-gift"></span>
                                    Donations Listing</h6>
                                <?= \yii\grid\GridView::widget([
                                    'dataProvider' => new \yii\data\ArrayDataProvider([
                                        'allModels' => $model->donations,
                                        'pagination' => [
                                            'pageSize' => 50,
                                        ],
                                    ]),
                                    //'filterModel' => $searchModel,
                                    'columns' => [
                                        [
                                            'class' => 'yii\grid\SerialColumn',
                                        ],
                                        ['attribute' => 'receipt_no',
                                        ],
                                        ['attribute' => 'receive_date',
                                            'value' => function ($data) {
                                                return date('Y M d', $data->receive_date);
                                            },
                                            'label' => 'Receive Date'
                                        ],
                                        ['attribute' => 'amount',
                                            'value' => function ($data) {
                                                return 'RS. ' . number_format($data->amount);
                                            },
                                            'label' => 'Recovery Amount'
                                        ],
                                    ],
                                ]); ?>

                            </section>
                        </div>
                </section>
            </div>
        </div>
    </div>
    <!--end-->
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