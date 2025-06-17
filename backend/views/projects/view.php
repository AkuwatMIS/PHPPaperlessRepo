<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Projects */
?>
<div class="projects-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'project_table',
            'name',
            'code',
            'donor',
            'funding_line',
            'started_date',
            'logo',
            'description:ntext',
            'status',
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
        <h4>Products</h4>
        <thead>
        <tr>
            <th>#</th>
            <th>Products</th>
        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($array['model_projectwithproduct']->product_ids as $id){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $array['products'][$id] ?></td>
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