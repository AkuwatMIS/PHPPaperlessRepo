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
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
        'value'=>'tranch.loan.sanction_no',
        'label' => 'Sanction No'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
        'value'=>'tranch.loan.application.member.cnic',
        'label' => 'CNIC'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'value'=>'tranch.loan.branch.name',
        'label' => 'Branch',
        'filter' => $branches_names,
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranche_id',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranch_id',
        'label'=>'Tranche No',
        'value'=>'tranch.tranch_no',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'bank_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'account_no',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'transferred_amount',
        'label'=>'Tranche Amount',
        'value' => function ($data, $key, $index) {
            return isset($data->transferred_amount)?number_format($data->transferred_amount):'';
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'tranch.loan.date_disbursed',
        'attribute'=>'date_disbursed',
        'value' => function ($data, $key, $index) {
            return date("d M y", $data->tranch->date_disbursed);
        },
        'label'=>'Date Disbursed',
    ],
    /*[
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
         'value' => 'status'
        //'filter' => common\components\Helpers\ListHelper::getDisbursementDetailStatusView($data->status),
    ],*/
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'template' => '{view}',
        'visibleButtons' => [
            'view' => function ($model) {
                return true;
            },
        ],
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        //'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
       /*'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete',
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'],*/
    ],

];   