<?php

use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Groups */
$this->title = $model->grp_no;
$this->params['breadcrumbs'][] = ['label' => 'Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
/*echo '<pre>';
print_r($applications);
die();*/
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Group</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="row">

            <div class="col-md-6">
                <article class="profile-info-item">
                    <header class="profile-info-item-header">
                        <i class="glyphicon glyphicon-tasks"></i>
                        <b>Group Information</b>
                    </header>
                    <div class="box-typical-inner">
                        <p>
                            <b>Group N0</b> : <?= ucfirst($model->grp_no); ?>
                        </p>
                        <!--<p>
                            <b>Group No</b> : <?/*= $model->group_name */?>
                        </p>-->
                        <p>
                            <b>Group Type</b> : <?= $model->grp_type ?>
                        </p>
                        <p>
                            <b>Is Locked</b> : <?php echo ($model->is_locked == 1) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?>

                        </p>

                    </div>
                </article><!--.profile-info-item-->

            </div>
            <div class="col-md-6">
                <!--<article class="profile-info-item">-->
                    <header class="profile-info-item-header">
                        <i class="font-icon font-icon-view-rows"></i>
                        <b>Credit Structure Information</b>
                    </header>
                    <div class="box-typical-inner">
                        <p class="line-with-icon">
                            <b>Region</b>
                            : <?= isset($model->region->name) ? $model->region->name : 'Not Set'; ?>
                        </p>
                        <p class="line-with-icon">
                            <b>Area</b>
                            : <?= isset($model->area->name) ? $model->area->name : 'Not Set'; ?>
                        </p>
                        <p class="line-with-icon">
                            <b>Branch</b>
                            : <?= isset($model->branch->name) ? $model->branch->name : 'Not Set'; ?>
                        </p>
                        <!--<p class="line-with-icon">
                            <b>Team</b>
                            : <?/*= isset($model->team->name) ? $model->team->name : 'Not Set'; */?>
                        </p>
                        <p class="line-with-icon">
                            <b>Field</b>
                            : <?/*= isset($model->field->name) ? $model->field->name : 'Not Set'; */?>
                        </p>-->
                    </div>
                <!--</article>--><!--.profile-info-item-->
            </div>
        </div>
        <!--<div class="col-lg-12">
            <? /*= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'grp_no',
                    'group_name',
                    'grp_type',
                    [
                        'attribute'=>'region.name',
                        'label'=>'Region',
                    ],
                    [
                        'attribute'=>'area.name',
                        'label'=>'Area',
                    ],
                    [
                        'attribute'=>'branch.name',
                        'label'=>'Branch',
                    ],
                    ['attribute'=>'team_id',
                        'value'=>function($data){return isset($data->team->name)?$data->team->name:'Not Set';},
                        'label'=>'Team',
                    ],
                    ['attribute'=>'field_id',
                        'value'=>function($data){return isset($data->field->name)?$data->field->name:'Not Set';},
                        'label'=>'Field',
                    ],
                        'is_locked',

                    ],
            ]) */ ?>
        </div>-->

        <?php if($model->grp_type=='IND'){?>
            <hr>
        <header class="profile-info-item-header">
            <i class="font-icon font-icon-view-rows"></i>
            <b>Guarantors Information</b>
        </header>
            <div class="row">

            <?php foreach ($model->guarantors as $guarantor){
            ?>

        <div class="col-md-12">
            <div class="table-responsive">
                <p class="line-with-icon">
                    <b>Name</b>
                    : <?= isset($guarantor->name) ? $guarantor->name : 'Not Set'; ?>
                </p>
                <p class="line-with-icon">
                    <b>Parentage</b>
                    : <?= isset($guarantor->parentage) ? $guarantor->parentage : 'Not Set'; ?>
                </p>
                <p class="line-with-icon">
                    <b>CNIC</b>
                    : <?= isset($guarantor->cnic) ? $guarantor->cnic : 'Not Set'; ?>
                </p>
                <p class="line-with-icon">
                    <b>Phone No</b>
                    : <?= isset($guarantor->phone) ? $guarantor->phone : 'Not Set'; ?>
                </p>
                <p class="line-with-icon">
                    <b>Phone No</b>
                    : <?= isset($guarantor->address) ? $guarantor->address : 'Not Set'; ?>
                </p>
            </div>
                <div class="row">
                    <div class="col-sm-6">
                        <p class="line-with-icon">
                            <b>Font CNIC</b>
                        </p>

                        <?php
                        $image = \common\components\Helpers\GroupHelper::getFCnic($guarantor->id);

                        if (!empty($image)) {
                            $profile_image=\common\components\Helpers\ImageHelper::getImageFromDisk('guarantors',$guarantor->id,$image->image_name,false);
                            echo \yii\helpers\Html::img($profile_image, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                        }else{
                            $pic_url =  'noimage.png';
                            echo \yii\helpers\Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                        }
                        ?>


                    </div>
                    <div class="col-sm-6">
                        <p class="line-with-icon">
                            <b>Back CNIC</b>
                        </p>

                        <?php
                        $image = \common\components\Helpers\GroupHelper::getBCnic($guarantor->id);

                        if (!empty($image)) {
                            $profile_image=\common\components\Helpers\ImageHelper::getImageFromDisk('guarantors',$guarantor->id,$image->image_name,false);
                            echo \yii\helpers\Html::img($profile_image, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                        }else{
                            $pic_url =  'noimage.png';
                            echo \yii\helpers\Html::img('@web/uploads/' . $pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                        }
                        ?>
                    </div>

            </div>
            <br><br>
        </div>
        <?php }}?>
            </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <header class="box-typical-header">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-title">
                            <h6 class="address-heading"><span class="glyphicon glyphicon-tag"></span>
                                Applications</h6>
                        </div>
                    </div>
                </header>
                <?= GridView::widget([
                    'dataProvider' => $applications,
                    //'filterModel' => $searchModel,
                    'columns' => require(__DIR__ . '/_columns_application.php'),
                    'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{count}</strong> items.
                              ',
                    'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                ]); ?>

            </div>
        </div>
        <br>
<hr>
        <div class="col-md-12">
        <section class="box-typical">
            <header class="box-typical-header-sm">Group Actions</header>
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
            </article><!--.profile-info-item-->
        </section><!--.box-typical-->
        </div>
    </div>
</div>
