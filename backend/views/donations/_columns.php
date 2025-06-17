<?php
use yii\helpers\Url;

return [
   
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
        'value'=>'region.name',
        'label'=>'Region',
        'filter'=>$array['regions']
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'value'=>'area.name',
        'label'=>'Area',
        'filter'=>$array['areas']
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'label'=>'Branch',
        'value'=>'branch.name',
        'filter'=>$array['branches'],

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'label'=>'Project',
        'value'=>'project.name',
        'filter'=>$array['projects'],
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
        'value'=>'loan.sanction_no'
    ],
    [
        // 'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_name',
        'value'=>'application.member.full_name',
        'label'=>'Name',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_cnic',
        'value'=>'application.member.cnic',
        'label'=>'Cnic',

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'receive_date',
        'value'=>function($data){return date('d-M-Y', $data->receive_date);},

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'amount',
        //'pageSummary' => true,
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'receipt_no',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete', 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'], 
    ],

];   