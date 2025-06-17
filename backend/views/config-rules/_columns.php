<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'group',
        'filter'=>$array['config_groups'],

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'priority',
        'filter'=>$array['config_priority'],
        'value'=>function($data){
            if($data->priority == 0){
                return 'Global';
            }
            else if($data->priority == 1) {
                return 'Project';

            }
            else if($data->priority == 2) {
                return 'Region';

            }

            else if($data->priority == 3) {
                return 'Area';

            }
            else if($data->priority == 4) {
                return 'Branch';

            }
            else if($data->priority == 5) {
                return 'Team';
            }
            else if($data->priority == 6) {
                return 'Field';
            }
            else if($data->priority == 7) {
                return 'User';

            }

        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'key',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'value',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'parent_type',
        'filter'=>$array['config_parent_type'],
    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'parent_id',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'project_id',
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