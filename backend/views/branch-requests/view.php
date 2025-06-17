<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */
?>
<div class="branch-requests-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
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
            'type',
            'name',
            'short_name',
            'code',
            'uc',
            'address',
            //'city_id',
            [
                'attribute'=>'city_id',
                'label'=>'City',
                'value'=>function($data){return isset($data->city->name)?$data->city->name:'';}
            ],
            'tehsil_id',
            //'district_id',
            [
                'attribute'=>'district_id',
                'label'=>'District',
                'value'=>function($data){return isset($data->district->name)?$data->district->name:'';}
            ],
            //'division_id',
            [
                'attribute'=>'division_id',
                'label'=>'Division',
                'value'=>function($data){return isset($data->division->name)?$data->division->name:'';}
            ],
            //'province_id',
            [
                'attribute'=>'province_id',
                'label'=>'Province',
                'value'=>function($data){return isset($data->province->name)?$data->province->name:'';}
            ],
            //'country_id',
            [
                'attribute'=>'country_id',
                'label'=>'Country',
                'value'=>function($data){return isset($data->country->name)?$data->country->name:'';}
            ],
            'latitude',
            'longitude',
            'description:ntext',
            'opening_date',
            'status',
            'cr_division_id',
            'remarks:ntext',
            'recommended_on',
            'recommended_by',
            'recommended_remarks:ntext',
            'approved_on',
            'approved_by',
            'approved_remarks:ntext',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
