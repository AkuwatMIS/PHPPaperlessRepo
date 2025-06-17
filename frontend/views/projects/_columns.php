<?php
use yii\helpers\Url;

return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
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
        'attribute'=>'name',
        'label'=>'Project',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'started_date',
        'value'=>function($model){
            return date('Y-m-d', $model->started_date);
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'total_fund',
        'label' => 'Total Fund Allocated'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fund_received',
        'label' => 'Total Fund Received'
    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'project_period',
//        'label' => 'Period'
//    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'ending_date',
//        'value'=>function($model){
//            return (($model->ending_date) > 0) ? date('Y-m-d', $model->ending_date) : "--";
//        },
//    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'pending_amount',
    ],*/
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
//        'buttons' => [
//            'create-charges' => function ($url, $model, $key) {
//                return \kartik\helpers\Html::a('<span class="glyphicon glyphicon-plus"></span>', Url::to(['create', 'id' => $model->id]),[/*'role'=>'modal-remote',*/'title'=>'Charges'/*,'data-toggle'=>'tooltip'*/]);
//
//            },
//        ],
        'visibleButtons' =>
            [
                'update' =>  function($model) {
//                    if(isset($model->serviceCharges)) {
                        return true;
//                    }
                }
            ],
//        "template" => '{view} {create-charges} {update}',
        "template" => '{view} {update}',
        'viewOptions'=>[/*'role'=>'modal-remote',*/'title'=>'View'/*,'data-toggle'=>'tooltip'*/],
        'updateOptions'=>[/*'role'=>'modal-remote',*/'title'=>'Update'/*, 'data-toggle'=>'tooltip'*/],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete', 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'],
        'contentOptions' => ['style' => 'width:190px;'],
    ],

];   