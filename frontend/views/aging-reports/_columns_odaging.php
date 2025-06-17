<?php
use yii\helpers\Url;

return [
   /* [
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
        'attribute'=>'start_month',
        'label'=>'Month',
        'value'=>function($model){
            return date('d M Y',strtotime($model->start_month));
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'one_month',
        'label'=>'one_two',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'next_three_months',
        'label'=>'two_three',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'next_six_months',
        'label'=>'three_six',
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'next_one_year',
         'label'=>'six_twelve',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'next_two_year',
         'label'=>'greater_twelve',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'total',
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'status',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],
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