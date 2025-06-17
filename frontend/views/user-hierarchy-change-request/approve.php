<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserHierarchyChangeRequest */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Hierarchy Change Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$auth = Yii::$app->authManager;
$roles = $auth->getRolesByUser($model->user_id);
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Transfer Approval By DA</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="row">
        <div class="col-sm-9">
            <h4>Transfer Hierarchy</h4>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'id',
                [
                    'attribute'=>'user_id',
                    'value'=>function ($model) {
                        return isset($model->user->username)?$model->user->username:'--';
                    },
                ],
                [
                    'attribute'=>'region_id',
                    'value'=>function ($model) {
                        if($model->region_id!=0) {
                            return isset($model->region->name) ? $model->region->name : '--';
                        }
                        else{
                            return'N/A';
                        }
                    },
                ],
                [
                    'attribute'=>'area_id',
                    'value' => function ($model) {
                        if ($model->area_id != 0) {
                            return isset($model->area->name) ? $model->area->name : '--';
                        } else {
                            return 'N/A';
                        }
                    },

                ],
                [
                    'attribute' => 'branch_id',
                    'value' => function ($model) {
                        if ($model->branch_id != 0) {
                            return isset($model->branch->name) ? $model->branch->name : '--';
                        } else {
                            return 'N/A';
                        }
                    },
                ],
                [
                    'attribute' => 'team_id',
                    'value' => function ($model) {
                        if ($model->team_id != 0) {
                            return isset($model->team->name) ? $model->team->name : '--';
                        } else {
                            return 'N/A';
                        }
                    },
                ],
                [
                    'attribute' => 'field_id',
                    'value' => function ($model) {
                        if ($model->field_id != 0) {
                            return isset($model->field->name) ? $model->field->name : '--';
                        } else {
                            return 'N/A';
                        }
                    },
                ],
                'status',
                [
                    'attribute' => 'created_by',
                    'value' => function ($model) {
                        if ($model->created_by != 0) {
                            return \common\components\Helpers\UsersHelper::getUserName($model->created_by)->username;
                        } else {
                            return 'N/A';
                        }
                    },
                ],
                [
                    'attribute' => 'recommended_by',
                    'value' => function ($model) {
                        if ($model->recommended_by != 0) {
                            return \common\components\Helpers\UsersHelper::getUserName($model->recommended_by)->username;
                        } else {
                            return 'N/A';
                        }
                    },
                ],
            ],
        ]) ?>
        </div>
        <div class="col-sm-3">
            <h4>Current Hierarchy</h4>
            <?php if (key($roles) == 'RC' || key($roles) == 'RM' || key($roles) == 'RA' || key($roles) == 'AM' || key($roles) == 'AA' || key($roles) == 'DEO' || key($roles) == 'BM' || key($roles) == 'LO') {?>

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
            <?php if (key($roles) == 'AM' || key($roles) == 'AA' || key($roles) == 'DEO' || key($roles) == 'BM' || key($roles) == 'LO') {?>
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
            <?php if (key($roles) == 'BM' || key($roles) == 'LO') {?>
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
            <?php if (key($roles) == 'LO') {?>
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
        <?php $form = \yii\widgets\ActiveForm::begin([
            'action' => ['approve-by-da?id='.$model->id.''],
            'method' => 'post',
        ]); ?>
        <?php if ($model->status == 'recommended'){ ?>
        <div class="form-group" style="margin-top: 20px">
            <?= \yii\helpers\Html::submitButton('Approve', ['class' => 'btn btn-success pull-right']) ?>
            <?php }else{ ?>
            <div class="form-group" style="margin-top: 20px">
                <?= \yii\helpers\Html::submitButton('Approved',  ['disabled'=>true,'class' => 'btn btn-success pull-right glyphicon glyphicon-ok']) ?>
                <?php } ?>
            <br>

        </div>
<?php \yii\widgets\ActiveForm::end(); ?>

</div>
</div>

