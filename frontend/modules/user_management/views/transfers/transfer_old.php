<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\DetailView;
use common\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'User Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = '';
$js = '
    $(function(){
        $(\'#modelButton\').click(function(){
            $(\'.modal\').modal(\'show\')
                .find(\'#modelContent\')
                .load($(this).attr(\'value\'));
        });
    });
    $(function(){
        $(\'#modalButton\').click(function(){
            $(\'.modal\').modal(\'show\')
                .find(\'#modelContent\')
                .load($(this).attr(\'value\'));
        });
    });
    $(document).ready(function () {
        $(\'#hierarchy_fields\').hide();
        $(\'#user_role\').hide();
        
        $("#usertransfers-type").change(function(){
            var a=$(\'#usertransfers-type\').val();
            if(a==\'transfer\'){
                $(\'#user_role\').hide();
                $(\'#hierarchy_fields\').show();

            } else if(a==\'promot/demot\'){
                $(\'#hierarchy_fields\').show();
                $(\'#user_role\').show();

            }
            else {
                $(\'#user_role\').hide();
                $(\'#hierarchy_fields\').hide();

            }
        });
    });';
$this->registerJs($js);
?>
<div class="container-fluid">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">

        <div class="col-md-7">

            <div class="box-typical">
                <header class="box-typical-header-sm">User Detail</header>
                <div class="row">
                    <div class="col-md-4">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="font-icon font-icon-notebook-bird"></i>
                                Basic Information
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
                                <p>
                                    <b>Email  Address</b> : <?= isset($model->email)?($model->email):'Not Set'; ?>
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
                                <i class="font-icon font-icon-case"></i>
                                Current Hierarchy
                            </header>
                            <div class="box-typical-inner">
                                <?php if (key($auth['roles']) == 'RC' || key($auth['roles']) == 'RM' || key($auth['roles']) == 'RA' || key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'AAA' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                                <p>
                                    <b>Designation</b>
                                    : <?= key($auth['roles']); ?>
                                </p>
                                <p>
                                    <b>Region</b> : <?= isset($array['region']->userRegion->name) ? $array['region']->userRegion->name : '-' ?>
                                </p>
                                <?php }?>
                                <?php if (key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'AAA' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                                <p>
                                    <b>Area</b> : <?= isset($array['area']->userArea->name) ? $array['area']->userArea->name : '-' ?>
                                </p>
                                <?php }?>
                                <?php if (key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                                <p>
                                    <b>Branch</b> : <?= isset($array['branch']->userBranch->name) ? $array['branch']->userBranch->name : '-' ?>
                                </p>
                                <?php }?>
                                <?php if (key($auth['roles']) == 'LO') {?>
                                <p>
                                    <b>Team</b> :  <?= isset($array['team']->userTeam->name) ? $array['team']->userTeam->name : '--' ?>
                                </p>
                                <p>
                                    <b>Field</b> : <?= isset($array['field']->userField->name) ? $array['field']->userField->name : '--' ?>
                                </p>
                                <?php }?>
                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                    <div class="col-md-4">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="font-icon font-icon-award"></i>
                                New Hierarchy
                            </header>
                            <div class="box-typical-inner">
                                <p>
                                    <b>Type</b>
                                    : <?= isset($transfer_model->type) ? $transfer_model->type : "" ?>
                                </p>
                                <?php if(!empty($transfer_model->role)) { ?>
                                <p>
                                    <b>Promotion/Demotion Role</b>
                                    : <?= isset($transfer_model->role) ? $transfer_model->role : '-' ?>
                                </p>
                                <?php } ?>
                                <p>
                                    <b>Region</b>
                                    : <?= isset($transfer_model->region->name) ? $transfer_model->region->name : '-' ?>
                                </p>
                                <p>
                                    <b>Area</b>
                                    : <?= isset($transfer_model->area->name) ? $transfer_model->area->name : '-' ?>
                                </p>
                                <p>
                                    <b>Branch</b>
                                    : <?= isset($transfer_model->branch->name) ? $transfer_model->branch->name : '-' ?>
                                </p>
                                <p>
                                    <b>Team</b>
                                    : <?= isset($transfer_model->team->name) ? $transfer_model->team->name : '-' ?>
                                </p>
                                <p>
                                    <b>Field</b>
                                    : <?= isset($transfer_model->field->name) ? $transfer_model->field->name : '-' ?>
                                </p>

                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                </div>
            </div>
            </section><!--.box-typical-->

        </div>
        <div class="col-md-5">
            <section class="box-typical">
                <header class="box-typical-header-sm">User Transfer Actions</header>
                <article class="profile-info-item">
                    <header class="profile-info-item-header">
                        <i class="font-icon font-icon-award"></i>
                        Actions Logs
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
                                foreach ($transfer_model->actions as $key => $action) {
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
            <?php
            $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
            $role = key($role);
            /*echo '<pre>';
            print_r(key($role));
            die();*/
            $action_recommended = \common\models\UserTransferActions::find()->where(['parent_id' => $transfer_model->id, 'action' => 'reviewed'])->one();
            $action_accepted = \common\models\UserTransferActions::find()->where(['parent_id' => $transfer_model->id, 'action' => 'accepted'])->one();
            $action_approved = \common\models\UserTransferActions::find()->where(['parent_id' => $transfer_model->id, 'action' => 'approved'])->one();
            $action_hr_acceptance = \common\models\UserTransferActions::find()->where(['parent_id' => $transfer_model->id, 'action' => 'hr_acceptance'])->one();

            if(isset($action_recommended) && $action_recommended->status == 0 && $action_approved->status == 0 && $action_recommended->user_id == Yii::$app->user->id)
            { ?>
                <?= Html::button('Recommend', ['value' => \yii\helpers\Url::to('/user-management/transfers/recommend?id=' . $transfer_model->id), 'id' => 'modelButton', 'style' => 'width:450px;', 'class' => 'btn btn-primary', 'title' => 'Recommend', 'data-toggle' => 'tooltip']); ?>
            <?php  } else if ((isset($action_recommended) && $action_recommended->status == 1 && isset($action_accepted) && $action_accepted->status == 0 && $action_accepted->user_id == Yii::$app->user->id) || (!isset($action_recommended) && $action_accepted->status == 0) && $action_accepted->user_id == Yii::$app->user->id) { ?>
                <?= Html::button('Accept', ['value' => \yii\helpers\Url::to('/user-management/transfers/accepted?id=' . $transfer_model->id), 'id' => 'modelButton', 'style' => 'width:430px;', 'class' => 'btn btn-primary', 'title' => 'Accepted', 'data-toggle' => 'tooltip']); ?>
            <?php  } else if ((isset($action_recommended) && $action_recommended->status == 1 && $action_approved->status == 0  && $action_approved->user_id == Yii::$app->user->id) || (!isset($action_recommended) && $action_approved->status == 0) && $action_approved->user_id == Yii::$app->user->id) { ?>
                <?= Html::button('Approved', ['value' => \yii\helpers\Url::to('/user-management/transfers/approved?id=' . $transfer_model->id), 'id' => 'modelButton', 'style' => 'width:430px;', 'class' => 'btn btn-primary', 'title' => 'Approved', 'data-toggle' => 'tooltip']); ?>
            <?php } else if (($action_approved->status == 1 && isset($action_hr_acceptance) && $action_hr_acceptance->status == 0) && $action_hr_acceptance->user_id == Yii::$app->user->id) { ?>
                <?= Html::button('Approved', ['value' => \yii\helpers\Url::to('/user-management/transfers/hracceptance?id=' . $transfer_model->id), 'id' => 'modelButton', 'style' => 'width:430px;', 'class' => 'btn btn-primary', 'title' => 'HR Acceptance', 'data-toggle' => 'tooltip']); ?>
            <?php }  ?>
        
    </div>

    </div>
</div>
<?php

Modal::begin([
    'header' => '<h4>User Management</h4>',
    'id' => 'model',
    'size' => 'model-lg',
]);

echo "<div id='modelContent'></div>";

Modal::end();

?>
