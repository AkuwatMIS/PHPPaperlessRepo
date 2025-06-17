<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_id',
        'value'=>'member.full_name',
        'label'=>'Member name'

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_id',
        'value'=>'application.application_no',
        'label'=>'Application no'

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'document_type'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'document_name'
    ],


    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'status',
        'value'=> function ($model, $key, $index, $column){
        if($model->status == 1){
            return 'Nadra Verisys Completed';
        }else{
            return 'Nadra Verisys pending';
        }

        }
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