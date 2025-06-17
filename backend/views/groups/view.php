<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Groups */
?>
<div class="groups-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
         //   'id',
           [
               'attribute'=>'region_id',
               'label'=>'Region',
               'value' => function($data){
                return $data->region->name;
               }
           ],
            [
                'attribute'=>'area_id',
                'label'=>'Area',
                'value' => function($data){
                    return isset($data->area->name)?$data->area->name:'';
                }
            ],[
                'attribute'=>'branch_id',
                'label'=>'Branch',
                'value' => function($data){
                    return isset($data->branch->name)?$data->branch->name:'';
                }
            ],
            [
                'attribute'=>'team_id',
                'label'=>'Team',
                'value' => function($data){
                    return isset($data->team->name)?$data->team->name:'';
                }
            ],
            //'area_id',
            //'branch_id',
            //'team_id',
            //'field_id',
            //'is_locked',
            //'br_serial',
            'grp_no',
            'group_name',
            'grp_type',
            //'status',
            //'reject_reason:ntext',
            //'assigned_to',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',
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