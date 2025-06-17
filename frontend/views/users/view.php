<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UsersCopy */
$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script type = "text/javascript" src = "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" ></script>
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
        <!--<h4><b>User Details<?/*= '(Name : '.$model->fullname.' - S/O : '.$model->father_name.')'*/?></b></h4>-->
        <div class="row">
            <div class="col-md-4">
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
            <div class="col-md-4">
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
                            : <?php $i=1; foreach ($array['model_userwithregions']->region_ids as $id) { ?>
                             <li style="margin-left: 10%" class="nowrap"><?= $array['regions'][$id] ?></li>
                             <?php $i++; } ?>
                        </p>
                        <p>
                            <b>Area</b>
                            : <?php $i=1;?>
                            <?php foreach ($array['model_userwithareas']->area_ids as $id) { ?>
                             <li style="margin-left: 10%"  class="nowrap"><?= $array['areas'][$id] ?></li>
                            <?php $i++; } ?>
                        </p>
                        <p>
                            <b>Branch</b>
                            :<?php $i=1;?>
                            <?php foreach ($array['model_userwithbranches']->branch_ids as $id) { ?>
                             <li style="margin-left: 10%" class="nowrap"><?= $array['branches'][$id] ?></li>
                            <?php $i++; } ?>
                        </p>
                    </div>
                </article><!--.profile-info-item-->
            </div>
            <!--<div class="col-md-9">
                <?/*= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        ['attribute' => 'city_id',
                            'label' => 'City',
                            'value' => function ($data) {
                                return isset($data->city->name) ? $data->city->name : '';
                            }
                        ],
                        'username',
                        'fullname',
                        'father_name',
                        'cnic',
                        'emp_code',
                        'email:email',
                        'alternate_email:email',
                        //'image',
                        'mobile',
                        ['attribute' => 'joining_date',
                            'label' => 'Joining Date',
                            'value' => function ($data) {
                                return isset($data->joining_date) ? date('Y-M-d', $data->joining_date) : '--';
                            }
                        ],
                    ],
                ]) */?>-->
            </div>
            <!--<div class="col-lg-3">
                <section class="box-typical">
                    <header class="box-typical-header-sm bordered">Regions</header>
                    <div class="box-typical-inner">
                        <ul class="profile-links-list">
                            <?php /*$i=1;*/?>
                            <?php /*foreach ($array['model_userwithregions']->region_ids as $id) { */?>
                                <li class="nowrap"><?/*= '<b>'.$i.' </b>- '.$array['regions'][$id] */?></li>
                            <?php /*$i++; } */?>
                        </ul>
                    </div>
                </section>

                <section class="box-typical">
                    <header class="box-typical-header-sm bordered">Areas</header>
                    <div class="box-typical-inner">
                        <ul class="profile-links-list">
                            <?php /*$i=1;*/?>
                            <?php /*foreach ($array['model_userwithareas']->area_ids as $id) { */?>
                                <li class="nowrap"><?/*= '<b>'.$i.' </b>- '.$array['areas'][$id] */?></li>
                            <?php /*$i++; } */?>
                        </ul>
                    </div>
                </section>
                <section class="box-typical">
                    <header class="box-typical-header-sm bordered">Branches</header>
                    <div class="box-typical-inner">
                        <ul class="profile-links-list">
                            <?php /*$i=1;*/?>
                            <?php /*foreach ($array['model_userwithbranches']->branch_ids as $id) { */?>
                                <li class="nowrap"><?/*= '<b>'.$i.' </b>- '.$array['branches'][$id] */?></li>
                            <?php /*$i++; } */?>
                        </ul>
                    </div>
                </section>
                <section class="box-typical">
                    <header class="box-typical-header-sm bordered">Projects</header>
                    <div class="box-typical-inner">
                        <ul class="profile-links-list">
                            <?php /*$i=1;*/?>
                            <?php /*foreach ($array['model_userwithproject']->project_ids as $id) { */?>
                                <li class="nowrap"><?/*= '<b>'.$i.' </b>- '.$array['projects'][$id] */?></li>
                                <?php /*$i++; } */?>
                        </ul>
                    </div>
                </section>
            </div>
        </div>-->
        <br>


        <div class="table-responsive">
            <table class="table table-bordered">
                <h4>Configuration</h4>

                <thead>
                <tr>
                    <th>#</th>
                    <th>Group</th>
                    <th>Priority</th>
                    <th>Key</th>
                    <th>Value</th>
                    <th>Parent Type</th>
                    <th>Parent_Id</th>
                    <th>Project Id</th>

                </tr>
                </thead>
                <tbody>
                <?php $count = 1;
                foreach ($array['configurations'] as $configs) { ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td><?= $configs['group'] ?></td>
                        <td><?= $configs['priority'] ?></td>
                        <td><?= $configs['key'] ?></td>
                        <td><?= $configs['value'] ?></td>
                        <td><?= $configs['parent_type'] ?></td>
                        <td><?= $configs['parent_id'] ?></td>
                        <td><?= $configs['project_id'] ?></td>

                    </tr>
                    <?php $count++;
                } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
