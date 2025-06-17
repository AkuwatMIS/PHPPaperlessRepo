<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */
?>
<div class="loans-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'application_id',
            [
                'attribute'=>'project_id_id',
                'label'=>'Project',
                'value' => function($data){
                  return isset($data->project->name)?$data->project->name:'';
                }

            ],
            //'project_id',
            'project_table',
            //'date_approved',
            ['attribute'=>'dob',
                'value'=>date('d-M-Y', $model->date_approved)],
            //'loan_amount',
            ['attribute'=>'loan_amount',
                'format'=>'integer'],
            'cheque_no',
            ['attribute'=>'inst_amnt',
                'format'=>'integer'],
            //'inst_amnt',
            //'inst_months',
            ['attribute'=>'inst_months',
                'format'=>'integer'],
            'inst_type',
           // 'date_disbursed',
            ['attribute'=>'date_disbursed',
                'value'=>date('d-M-Y', $model->date_disbursed)],
            ['attribute'=>'cheque_dt',
                'value'=>date('d-M-Y', $model->cheque_dt)],
            //'cheque_dt',
            //'disbursement_id',
            [
                'attribute'=>'activity_id',
                'label'=>'Activity',
                'value' => function($data){
                    return isset($data->activity->name)?$data->activity->name:'';
                }

            ],
           // 'activity_id',
            [
                'attribute'=>'product_id',
                'label'=>'Product',
                'value' => function($data){
                    return isset($data->product->name)?$data->product->name:'';
                }

            ],
            //'product_id',
            [
                'attribute'=>'group_id',
                'label'=>'Group No.',
                'value' => function($data){
                    return isset($data->group->grp_no)?$data->group->grp_no:'';
                }

            ],
            //'group_id',
            [
                'attribute'=>'region_id',
                'label'=>'Region',
                'value' => function($data){
                    return isset($data->region->name)?$data->region->name:'';
                }

            ],
            //'region_id',
            [
                'attribute'=>'area_id',
                'label'=>'Area',
                'value' => function($data){
                    return isset($data->area->name)?$data->area->name:'';
                }

            ],
            //'area_id',
            [
                'attribute'=>'branch_id',
                'label'=>'Branch',
                'value' => function($data){
                    return isset($data->branch->name)?$data->branch->name:'';
                }

            ],
            //'branch_id',
            [
                'attribute'=>'team_id',
                'label'=>'Team',
                'value' => function($data){
                    return isset($data->team->name)?$data->team->name:'';
                }

            ],
            //'team_id',
            'field_id',
            //'loan_expiry',
            ['attribute'=>'loan_expiry',
                'value'=>date('d-M-Y', $model->loan_expiry)],
           // 'loan_completed_date',
            ['attribute'=>'loan_completed_date',
                'value'=>date('d-M-Y', $model->loan_completed_date)],
            'old_sanc_no',
            'remarks:ntext',
            'br_serial',
            'sanction_no',
            ['attribute'=>'due',
                'format'=>'integer'],
           // 'due',
            //'overdue',
            ['attribute'=>'overdue',
                'format'=>'integer'],
            //'balance',
            ['attribute'=>'balance',
                'format'=>'integer'],
            'status',
            'reject_reason:ntext',
            //'is_lock',
            [
                'attribute'=>'is_lock',
                'value'=>function($data){
                    if($data->is_lock==0){
                        return 'Un Locked';
                    }
                    else{
                        return 'Locked';
                    }
                },
            ],
            //'deleted',
            ['attribute'=>'assigned_to',
                'value'=>isset($model->user->username)?$model->user->username:''],
            ['attribute'=>'created_by',
                'value'=>isset($model->user->username)?$model->user->username:''],
            ['attribute'=>'update_by',
                'value'=>isset($model->user->username)?$model->user->username:''],
        ],
    ]) ?>

</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <h4>Tranches</h4>

        <thead>
        <tr>
            <th>#</th>
            <th>ID</th>
            <th>Disbursement Date</th>
            <th>Tranch No</th>
            <th>Tranch Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($model->tranches as $tranch){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $tranch->id ?></td>
                <td><?= date('Y-m-d',$tranch->date_disbursed) ?></td>
                <td><?= $tranch->tranch_no ?></td>
                <td><?= $tranch->tranch_amount ?></td>
                <td><?= $tranch->status ?></td>
                <td><?= \yii\helpers\Html::a('Update', ['loan-tranches/update', 'id'=>$tranch->id ],
                        ['target'=>'blank','title'=> 'Upload File','class'=>'btn btn-primary pull-right', 'id' => 'add_file']) ?>
                </td>


            </tr>
            <?php $count++; } ?>
        </tbody>
    </table>
</div>
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
        <?php $count=1; foreach($array['configurations'] as $configs){ ?>
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
            <?php $count++; } ?>
        </tbody>
    </table>
</div>