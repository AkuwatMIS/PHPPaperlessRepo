<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Members */
?>
<div class="members-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            'full_name',
            'parentage_type',
            'parentage',
            'cnic',
            'gender',
            ['attribute'=>'dob',
                'value'=>date('d-M-Y', $model->dob)],
            'education',
            'marital_status',
            'family_no',
            'family_member_name',
            'family_member_cnic',
            'religion',
            'profile_pic',
            'status',
            //'deleted',
            //'assigned_to',
            ['attribute'=>'assigned_to',
                'value'=>isset($model->user->username)?$model->user->username:''],
            ['attribute'=>'created_by',
                'value'=>isset($model->user->username)?$model->user->username:''],
            ['attribute'=>'update_by',
                'value'=>isset($model->user->username)?$model->user->username:''],
            ['attribute'=>'created_at',
                'value'=>date('d-M-Y', $model->created_at)], ['attribute'=>'updated_at',
                'value'=>date('d-M-Y', $model->updated_at)],
        ],
    ]) ?>

</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <h4>Addresses</h4>
        <thead>
        <tr>
            <th>#</th>
            <th>Address</th>
            <th>Address Type</th>
            <th>Status</th>

        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($model->membersAddresses as $address){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $address->address ?></td>
                <td><?= $address->address_type ?></td>
                <td><?php if ($address->is_current==1){echo 'Active';}else{echo'In Active';} ?></td>

            </tr>
            <?php $count++; } ?>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <h4>Emails</h4>
        <thead>
        <tr>
            <th>#</th>
            <th>Email</th>
            <th>Status</th>

        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($model->membersEmails as $emails){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $emails->email ?></td>
                <td><?php if ($address->is_current==1){echo 'Active';}else{echo'In Active';} ?></td>


            </tr>
            <?php $count++; } ?>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <h4>Phones</h4>
        <thead>
        <tr>
            <th>#</th>
            <th>Phone</th>
            <th>Type</th>
            <th>Status</th>

        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($model->membersPhones as $phones){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $phones->phone ?></td>
                <td><?= $phones->phone_type ?></td>
                <td><?php if ($address->is_current==1){echo 'Active';}else{echo'In Active';} ?></td>
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