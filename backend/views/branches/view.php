<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Branches */
?>
<div class="branches-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'region_id',
            [
                'attribute'=>'region_id',
                'label'=>'Region',
                'value'=>function($data){return $data->region->name;},
            ],
            //'area_id',
            [
                'attribute'=>'area_id',
                'label'=>'Area',
                'value'=>function($data){return $data->area->name;},
            ],
            'type',
            'name',
            'short_name',
            'code',
            'uc',
            'village',
            'address',
            'mobile',
            //'city_id',
            [
                'attribute'=>'city_id',
                'label'=>'City',
                'value'=>function($data){return isset($data->city->name)?$data->city->name:'';},
            ],
            'tehsil_id',
            //'district_id',
            [
                'attribute'=>'district_id',
                'label'=>'District',
                'value'=>function($data){return isset($data->district->name)?$data->district->name:'';},
            ],
            //'division_id',
            [
                'attribute'=>'division_id',
                'label'=>'Division',
                'value'=>function($data){return isset($data->division->name)?$data->division->name:'';},
            ],
            //'province_id',
            [
                'attribute'=>'province_id',
                'label'=>'Province',
                'value'=>function($data){return isset($data->province->name)?$data->province->name:'';},
            ],
            //'country_id',
            [
                'attribute'=>'country_id',
                'label'=>'Country',
                'value'=>function($data){return isset($data->country->name)?$data->country->name:'';},
            ],
            'latitude',
            'longitude',
            'description:ntext',
            'opening_date',
            'status',
            'cr_division_id',
          /*  'assigned_to',
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
        <tr>
            <th>#</th>
            <th>Projects</th>
        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($array['model_branchwitproject']->project_ids as $id){ ?>
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
        <tr>
            <th>#</th>
            <th>Accounts</th>
        </tr>
        </thead>
        <tbody>
        <?php /*$count=1; foreach($array['model_branchwitaccount']->account_ids as $id){ */?><!--
            <tr>
                <td><?/*= $count */?></td>
                <td><?/*= $array['accounts'][$id] */?></td>
            </tr>
            --><?php /*$count++; } */?>
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
        <?php /*$count=1; foreach($array['configurations'] as $configs){ */?><!--
            <tr>
                <td><?/*= $count */?></td>
                <td><?/*= $configs['group'] */?></td>
                <td><?/*= $configs['priority'] */?></td>
                <td><?/*= $configs['key'] */?></td>
                <td><?/*= $configs['value'] */?></td>
                <td><?/*= $configs['parent_type'] */?></td>
                <td><?/*= $configs['parent_id'] */?></td>
                <td><?/*= $configs['project_id'] */?></td>

            </tr>
            --><?php /*$count++; } */?>
        </tbody>
    </table>
</div>