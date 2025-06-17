<?php
use yii\helpers\Url;
use common\components\Helpers\StructureHelper;
use dimmitri\grid\ExpandRowColumn;
return [
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
     /*[
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'id',
     ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region',
        'value'=>'region.name',
        'label'=>'Region',
        'filter'=> $regions
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area',
        'value'=>'area.name',
        'label'=>'Area',
        'filter'=> $areas
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch',
        'value'=>'branch.name',
        'label'=>'Branch',
        'filter'=> $branches
    ],
    [
        'class' => ExpandRowColumn::class,
        'attribute' => 'date_disbursed',
        'value'=>function ($model, $key, $index) {
            return date('d M Y',$model->date_disbursed);
        },
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/disbursements/disbursement-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->id];
        },
        'enableCache' => false,
        'format' => 'raw',
        'expandableOptions' => [
            'title' => 'Click me!',
            'class' => 'my-expand',
        ],
        /*'contentOptions' => [
            'style' => 'display: flex; justify-content: space-between;',
        ],*/
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'date_disbursed',
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'venue',
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'assigned_to',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_by',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'updated_by',
    ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'deleted',
    // ],
    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions' => ['style' => 'width:70px;'],
        'template' => '{view} ',
    ],
    /*[
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
    ],*/

];   