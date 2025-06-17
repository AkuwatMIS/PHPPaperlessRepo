<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\components\Helpers\UsersHelper;
/* @var $this yii\web\View */
/* @var $model common\models\FundRequests */
/* @var $form yii\widgets\ActiveForm */
/*echo '<pre>';
print_r($fund_request_detail);
print_r($fund_requests_details);
die();*/
//$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fund Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Fund Request</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?php if(in_array('frontend_get-templatetemplates',$permissions))
        { ?>
            <?php $templates=\common\models\Templates::find()->where(['module'=>'fund_requests','deleted'=>0])->all();?>
            <div class="dropdown pull-right">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> Templates
                    <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <?php foreach ($templates as $temp) { ?>

                        <li><a  target="_blank"
                                href="/templates/get-template?module_id=<?= $model->id ?>&module=fund_requests&template_id=<?= $temp->id?>"><?= $temp->template_name?></a>
                        </li>
                    <?php } ?>

                </ul>
            </div>
        <?php }?>
        <div class="fund-requests-form">
            <h4><b>Branch Details (<?= $model->branch->name.'/ Code: '.$model->branch->code.')'?></b></h4>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Total Requested Amount</th>
                    <th>Approved Amount</th>

                </tr>
                </thead><tbody>
                <tr>
                    <td>
                        <?= number_format($model->requested_amount) ?>
                    </td>
                    <td>
                        <?= $model->approved_amount!=0?number_format($model->approved_amount):'not yet appproved'?>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
            <table id="table-edit" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="2">No.</th>
                    <th>Project</th>
                    <th>No. of Loans</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                $total_loans = 0;
                $total_amount = 0;
                $branch_id = 0;
                foreach ($model->fundRequestDetails as $f) { ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= \common\models\Projects::findOne($f['project_id'])->name ?></td>
                        <td><?= number_format($f['total_loans']) ?></td>
                        <td><?= number_format($f['total_requested_amount']) ?></td>
                        <td><?= !empty($f['status'])?$f['status']:'N/A' ?></td>
                    </tr>
                    <?php
                    $i++;
                    $total_loans += $f['total_loans'];
                    $total_amount += $f['total_requested_amount'];
                    $branch_id = $f['branch_id'];
                } ?>
                <th></th>
                <th>Total:</th>
                <th><?= number_format($total_loans) ?></th>
                <th><?= number_format($total_amount) ?></th>
                <th></th>
                </tbody>
            </table>
        </div>
        <br>
        <br>

        <section class="box-typical">
            <article class="profile-info-item">
                <header class="profile-info-item-header">
                    <i class="font-icon font-icon-award"></i>
                  Fund Request History
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
                                <th>User</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if($model->created_by!=0){?>
                            <tr>
                                <td>
                                    <?php echo ucfirst('Created') ?>
                                    <br>
                                    <p style="font-size: 10px;color: green;">
                                       <?= ($model->created_at != 0) ? date('d M Y H:i', $model->created_at) : '-' ?>
                                    </p>
                                </td>
                                <td><?php echo $model->createUser->fullname.' ('.UsersHelper::getRole($model->created_by).')' ?></td>
                                <td><?php echo ($model->created_by != 0) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                            </tr>
                            <?php }?>
                            <?php if($model->approved_by!=0){?>
                                <tr>
                                    <td>
                                        <?php echo ucfirst('Recommended') ?>
                                        <br>
                                        <p style="font-size: 10px;color: green;">
                                            <?= ($model->approved_on != 0) ? date('d M Y H:i', $model->approved_on) : '-' ?>
                                        </p>
                                    </td>
                                    <td><?php echo $model->recommendUser->fullname.' ('.UsersHelper::getRole($model->approved_by).')'; ?></td>
                                    <td><?php echo ($model->approved_by != 0) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                            <?php }?>
                            <?php if($model->processed_by!=0){?>
                                <tr>
                                    <td>
                                        <?php echo ucfirst('Processed') ?>
                                        <br>
                                        <p style="font-size: 10px;color: green;">
                                           <?= ($model->processed_on != 0) ? date('d M Y H:i', $model->processed_on) : '-' ?>
                                        </p>
                                    </td>
                                    <td><?php echo $model->processUser->fullname.' ('.UsersHelper::getRole($model->processed_by).')'; ?></td>
                                    <td><?php echo ($model->processed_by != 0) ? '<span class="glyphicon glyphicon-ok" style="color:green"></span>' : '<span class="glyphicon glyphicon-remove" style="color:red"></span>' ?></td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </article><!--.profile-info-item-->
        </section>
    </div>
</div>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <div class="fund-requests-form">
            <?= \yii\grid\GridView::widget([
                'id' => 'crud-datatable',
                'dataProvider' => $dataProviderLoans,
                'filterModel' => $searchModelLoans,
                'showFooter' => true,
                'columns' => require(__DIR__ . '/_columns_loans.php'),

            ]) ?></div>
    </div>
</div>