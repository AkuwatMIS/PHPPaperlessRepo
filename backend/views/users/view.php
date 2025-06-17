<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UsersCopy */
?>
<div class="users-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'city_id',
            ['attribute'=>'city_id',
                'label'=>'City',
                'value'=>function($data){return isset($data->city->name)?$data->city->name:'';}
             ],
            'username',
            'fullname',
            'father_name',
            'email:email',
            'alternate_email:email',
           /* 'password',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'last_login_at',
            'last_login_token',
            'latitude',
            'longitude',*/
            //'image',
            //'mobile',
            //'joining_date',
            'emp_code',
            'reason',
           ['attribute'=>'is_block',
           'value'=>function($data){
                if($data->is_block==0){return 'No';}
                else{return 'Yes';}
           }
           ],
             'status',
           /* 'is_block',
            'block_date',
            'team_name',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',*/
        ],
    ]) ?>

</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <h4>Projects</h4>

        <tr>
            <th>#</th>
            <th>Projects</th>
        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($array['model_userwithproject']->project_ids as $id){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $array['projects'][$id] ?></td>
            </tr>
            <?php $count++; } ?>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <h4>Branches</h4>
        <tr>
            <th>#</th>
            <th>Branches</th>
        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($array['model_userwithbranches']->branch_ids as $id){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $array['branches'][$id] ?></td>
            </tr>
            <?php $count++; } ?>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <h4>Areas</h4>
        <tr>
            <th>#</th>
            <th>Areas</th>
        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($array['model_userwithareas']->area_ids as $id){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $array['areas'][$id] ?></td>
            </tr>
            <?php $count++; } ?>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <h4>Regions</h4>
        <tr>
            <th>#</th>
            <th>Regions</th>
        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($array['model_userwithregions']->region_ids as $id){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $array['regions'][$id] ?></td>
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