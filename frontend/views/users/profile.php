<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Members */
$this->title = $model->fullname;
$this->params['breadcrumbs'][] = $this->title;
?>
<script type = "text/javascript" src = "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" ></script>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Profile</h4>
                </div>
                <a  href="/users/update?id=<?php echo $model->id?>" class="btn btn-success pull-right" title="Update Profile">Update Profile</a>

            </div>
        </div>
    </header>
    <div class="row">
        <div class="col-lg-3">
            <section class="box-typical">
               <!-- <?/*= Html::a('<span class="glyphicon glyphicon-pencil pull-right"></span>', ['update', 'id'=>$model->id], ['target'=>'_blank', 'class'=>'btn btn-primary'],['title'=>'Update Profile']) */?>-->
                <div class="profile-card">

                    <div class="profile-card-photo">
                        <?php
                        $image = \common\components\Helpers\MemberHelper::getProfileImage($model->id);
                        if (!empty($image)) {
                            $user_image = (!empty($image->image_name)) ? ($image->image_name) : 'noimage.png';
                            $pic_url = $image->parent_type . "/" . $model->id . "/" . $user_image;
                        }else{
                            $pic_url =  'noimage.png';
                        }
                        ?>
                        <?php echo Html::img('@web/uploads/'.$pic_url, ['alt' => Yii::$app->name]); ?>
                    </div>
                    <div class="profile-card-name"><?= $model->fullname ?></div>
                    <div class="profile-card-status"><?= $model->father_name ?></div>
                    <div class="profile-card-location"><?= $model->email ?></div>
                </div><!--.profile-card-->
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b><?php echo count($model->applications)?></b>
                            Applications
                        </div>
                        <div class="tbl-cell">
                            <b><?php echo count($model->loans)?></b>
                            Loans
                        </div>
                    </div>
                </div>
                <ul class="profile-links-list">
                    <li class="nowrap">
                        <span><b>CNIC: </b></span>
                        <?= $model->cnic ?>
                    </li>
                    <li class="nowrap">
                        <span><b>Mobile: </b></span>
                        <?= $model->mobile ?>
                    </li>
                </ul>
            </section><!--.box-typical-->
        </div><!--.col- -->
        <div class="col-xl-9 col-lg-8">
            <section class="tabs-section">
                <div class="tabs-section-nav tabs-section-nav-left">
                    <ul class="nav" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#tabs-2-tab-1" role="tab" data-toggle="tab">
                                <span class="nav-link-in">Member Info</span>
                            </a>
                        </li>
                    </ul>

                </div><!--.tabs-section-nav-->

                <div class="tab-content no-styled profile-tabs">
                    <div role="tabpanel" class="tab-pane active" id="tabs-2-tab-1">
                        <section class="box-typical box-typical-padding">
                            <?= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                        'username',
                                    'father_name',
                                    'emp_code',
                                    'email',
                                    ['attribute' => 'branch',
                                        'value' => function ($data) {
                                            return isset($data->region->obj_id)? common\components\Helpers\StructureHelper::getBranches($data->branch->obj_id)->name :'--' ;

                                        }],
                                    ['attribute' => 'area',
                                        'value' => function ($data) {
                                            return isset($data->region->obj_id)? common\components\Helpers\StructureHelper::getBranches($data->area->obj_id)->name :'--' ;

                                        }],
                                    ['attribute' => 'region',
                                        'value' => function ($data) {
                                            return (isset($data->region->obj_id))?common\components\Helpers\StructureHelper::getBranches($data->region->obj_id)->name :'--' ;

                                        }],
                                    ['attribute' => 'joining_date',
                                        'value' => function ($data) {
                                            return ($data->joining_date != 0) ? date('Y-M-d', $data->joining_date) : '--';

                                        }],
                                ],
                            ]) ?>
                    </div><!--.tab-pane-->
                </div><!--.tab-content-->
            </section><!--.tabs-section-->
        </div>

    </div><!--.row-->
</div><!--.container-fluid-->

