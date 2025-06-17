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
         /*[
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'id',
     ],*/
     [
         'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_id',
     ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_no',
        'value'=>'application.application_no',
        'label'=>'Application No'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fixed_business_assets',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fixed_business_assets_amount',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'running_capital',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'running_capital_amount',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'business_expenses',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'business_expenses_amount',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'new_required_assets',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'new_required_assets_amount',
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'business_name',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'place_of_business',
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'business_details',
    ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'business_income',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'job_income',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'house_rent_income',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'other_income',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'business_capital',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'business_expenses',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'income_before_business',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'total_business_income',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'latitude',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'longitude',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'status',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'approved_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'approved_on',
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