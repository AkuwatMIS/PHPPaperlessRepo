<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Areas */
?>
<div class="areas-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'code',
            'tags',
            'short_description:ntext',
            'mobile',
            'opening_date',
            'full_address:ntext',
            [
                    'attribute'=>'region_id',
                'value'=>function($data){return isset($data->region->name)?$data->region->name:'';},
                'label'=>'Region',
            ],
            //'region_id',
            'latitude',
            'longitude',
            'status',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
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