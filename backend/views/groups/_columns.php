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

    //[
      //  'class'=>'\kartik\grid\DataColumn',
      //'attribute'=>'field_id',
    //],

    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'br_serial',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
        'label'=>'Region',
        'value' => 'region.name',
        'filter'=>$array['regions']


    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'label'=>'Area',
        'value' => 'area.name',
        'filter'=>$array['areas']



    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'label'=>'Branch',
        'value' => 'branch.name',
        'filter'=>$array['branches']


    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'grp_no',
         'label'=>'Group No.'
     ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'grp_type',
        'label'=>'Group Type'
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'group_name',
     ],

   /* [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'team_id',
        'label'=>'Team',
        'value' => 'team.name'


    ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'grp_type',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'co_code_count_temp',
    // ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
     ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'is_locked',
        'value'=>function($data){
            if($data->is_locked==0){
                return 'Un Locked';
            }
            else{
                return 'Locked';
            }
        },
        'filter'=>$array['is_lock']
    ],

    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'reject_reason',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'assigned_to',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],
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