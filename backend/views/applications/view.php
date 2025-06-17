<?php

use yii\widgets\DetailView;
use common\models\User;
/* @var $this yii\web\View */
/* @var $model common\models\Applications */
?>
<div class="applications-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'member_id',
            ['attribute'=>'fee',
            //'format'=>'integer',
            'value'=>function($data){return number_format($data->fee);}
            ],
            //'project_id',
            [
                'attribute'=>'project_id',
                'label'=>'Project',
                'value'=>function($data){return isset($data->project->name)?$data->project->name:'';}
            ],
            'project_table',
            //'activity_id',
            [
                'attribute'=>'activity_id',
                'label'=>'Activity',
                'value'=>function($data){return isset($data->activity->name)?$data->activity->name:'';}
            ],
            //'product_id',
            [
                'attribute'=>'product_id',
                'label'=>'Product',
                'value'=>function($data){return isset($data->product->name)?$data->product->name:'';}
            ],
            //'region_id',
            [
                'attribute'=>'region_id',
                'label'=>'Region',
                'value'=>function($data){return isset($data->region->name)?$data->region->name:'';}
            ],
            //'area_id',
            [
                'attribute'=>'area_id',
                'label'=>'Area',
                'value'=>function($data){return isset($data->area->name)?$data->area->name:'';}
            ],
            //'branch_id',
            [
                'attribute'=>'branch_id',
                'label'=>'Branch',
                'value'=>function($data){return isset($data->branch->name)?$data->branch->name:'';}
            ],
            //'team_id',
            [
                'attribute'=>'team_id',
                'label'=>'Team',
                'value'=>function($data){return isset($data->team->name)?($data->team->name):'';}
            ],
            'field_id',
            'no_of_times:datetime',
            'bzns_cond',
            'who_will_work',
            'name_of_other',
            'other_cnic',
            //'req_amount',
             ['attribute'=>'req_amount',
                        'value'=>function($data){return number_format($data->req_amount);}
                        ],
            'status',
            'is_urban',
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
            'deleted',
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
