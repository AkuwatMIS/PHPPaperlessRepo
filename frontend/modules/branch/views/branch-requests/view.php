<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\DetailView;
use common\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branch Requests', 'url' => ['index']];
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
';
$this->registerJs($js);
?>
<div class="container-fluid">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-7 pull-left">
            <?php
            //if(\Yii::$app->user->can(AuthItem::$PERM_BRQ_UPDATE, ['post'=>$model])) {
            echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
            //}
            ?>
            <?php
            //if(\Yii::$app->user->can(AuthItem::$PERM_BRQ_DELETE, ['post'=>$model])) {
            echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]);
            //}
            ?>

            <?= Html::a('Back', ['index'], ['class' => 'btn btn-primary']) ?>
        </div>

    </div>
    <div style="height: 10px;"></div>
    <div class="row">

        <div class="col-md-7">

            <div class="box-typical">
                <header class="box-typical-header-sm">Branch Request Detail</header>
                <div class="row">
                    <div class="col-md-4">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="font-icon font-icon-notebook-bird"></i>
                                Basic Information
                            </header>
                            <div class="box-typical-inner">
                                <p>
                                    <b>Type</b> : <?= $model->type; ?>
                                </p>
                                <p>
                                    <b>Name</b> : <?= $model->name; ?>
                                </p>
                                <p>
                                    <b>UC</b> : <?= $model->uc; ?>
                                </p>
                                <p>
                                    <b>Opening Date</b> : <?= date('d M Y', $model->opening_date); ?>
                                </p>
                                <p>
                                    <b>Effective Date</b> : <?= date('d M Y', $model->effective_date); ?>
                                </p>
                                <p>
                                    <b>Address</b> : <?= $model->address; ?>
                                </p>
                                <p>
                                    <b>Remarks</b> : <?= $model->remarks; ?>
                                </p>
                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                    <div class="col-md-4">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="font-icon font-icon-case"></i>
                                Organization Hierarchy
                            </header>
                            <div class="box-typical-inner">
                                <p>
                                    <b>Credit Division</b> : <?= $model->type; ?>
                                </p>
                                <p>
                                    <b>Region</b> : <?= $model->region->name; ?>
                                </p>
                                <p>
                                    <b>Area</b> : <?= $model->area->name; ?>
                                </p>
                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                    <div class="col-md-4">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="font-icon font-icon-award"></i>
                                Demographic Hierarchy
                            </header>
                            <div class="box-typical-inner">
                                <p>
                                    <b>Province</b> : <?= $model->province->name; ?>
                                </p>
                                <p>
                                    <b>City</b> : <?= $model->city->name; ?>
                                </p>
                                <p>
                                    <b>Division</b> : <?= $model->division->name; ?>
                                </p>
                                <p>
                                    <b>District</b> : <?= $model->district->name; ?>
                                </p>
                                <p>
                                    <b>Tehsil</b> : <?= $model->tehsil->name; ?>
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
                <header class="box-typical-header-sm">Branch Request Actions</header>
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

            <?php
            $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
            $role = key($role);
            /*echo '<pre>';
            print_r(key($role));
            die();*/
            $action_reviewed = \common\models\BranchRequestActions::find()->where(['parent_id' => $model->id, 'action' => 'reviewed'])->one();
            $action_recommended = \common\models\BranchRequestActions::find()->where(['parent_id' => $model->id, 'action' => 'awaiting-recommended'])->one();
            $action_approved = \common\models\BranchRequestActions::find()->where(['parent_id' => $model->id, 'action' => 'approved'])->one();

            if ($role == 'RM') {
                if ($action_reviewed->status == 0 && $action_recommended->status == 0 && $action_approved->status == 0) {
                    ?>
                    <?= Html::button('Reviewed', ['value' => \yii\helpers\Url::to('/branch/branch-requests/reviewed?id=' . $model->id), 'id' => 'modelButton', 'style' => 'width:470px;', 'class' => 'btn btn-primary', 'title' => 'Reviewed', 'data-toggle' => 'tooltip']); ?>
                    <?php
                } else {

                    ?>
                    <?= Html::button('Reviewed', ['value' => \yii\helpers\Url::to('/branch/branch-requests/reviewed?id=' . $model->id), 'id' => 'modelButton', 'class' => 'btn btn-primary', 'style' => 'width:470px;', 'disabled' => 'disabled', 'title' => 'Reviewed', 'data-toggle' => 'tooltip']); ?>
                    <?php
                }
            }

            if ($role == 'DM') {
                if ($action_reviewed->status == 1 && $action_recommended->status == 0 && $action_approved->status == 0) {
                    ?>
                    <?= Html::button('Recommend', ['value' => \yii\helpers\Url::to('/branch/branch-requests/recommend?id=' . $model->id), 'id' => 'modelButton', 'style' => 'width:470px;', 'class' => 'btn btn-primary', 'title' => 'Recommend', 'data-toggle' => 'tooltip']); ?>
                    <?php
                } else {

                    ?>
                    <?= Html::button('Recommend', ['value' => \yii\helpers\Url::to('/branch/branch-requests/recommend?id=' . $model->id), 'id' => 'modelButton', 'class' => 'btn btn-primary', 'style' => 'width:470px;', 'disabled' => 'disabled', 'title' => 'Recommend', 'data-toggle' => 'tooltip']); ?>
                    <?php
                }
            }


            if ($role == 'CCO') {
                if ($action_reviewed->status == 1 && $action_recommended->status == 1 && $action_approved->status == 0) {
                    ?>
                    <?= Html::button('Approved', ['value' => \yii\helpers\Url::to('/branch/branch-requests/approve?id=' . $model->id), 'id' => 'modalButton', 'class' => 'btn btn-primary', 'style' => 'width:470px;', 'title' => 'Approve', 'data-toggle' => 'tooltip']); ?>
                    <?php
                } else {

                    ?>
                    <?= Html::button('Approved', ['value' => \yii\helpers\Url::to('/branch/branch-requests/approve?id=' . $model->id), 'id' => 'modalButton', 'class' => 'btn btn-primary', 'style' => 'width:470px;', 'disabled' => 'disabled', 'title' => 'Approve', 'data-toggle' => 'tooltip']); ?>
                    <?= Html::a('View Branch', ['/branches/view', 'id' => $model->branch_id], ['target' => '_blank']); ?>
                    <?php
                }
            }
            ?>
        </div>

    </div>
</div>
<?php

Modal::begin([
    'header' => '<h4>Branch Request</h4>',
    'id' => 'model',
    'size' => 'model-lg',
]);

echo "<div id='modelContent'></div>";

Modal::end();

?>
