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
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'loan_id',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
        'value' => function( $data) {
            return $data->loan->sanction_no;

        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'recovery_id',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'amount',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cheque_no',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'voucher_no',
    ],
    [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'bank_name',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'bank_account_no',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'type',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'reason',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'deposit_slip_no',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'borrower_name',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'borrower_cnic',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'who_will_work',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'other_name',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'other_cnic',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'write_off_date',
         'value' => function( $data) {
             return \common\components\Helpers\StringHelper::dateFormatter($data->write_off_date);

         }
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
     ],
     /*[
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'created_by',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'updated_by',
     ],*/
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'created_at',
         'value' => function( $data) {
             return \common\components\Helpers\StringHelper::dateFormatter($data->created_at);

         }
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'updated_at',
         'value' => function( $data) {
             return \common\components\Helpers\StringHelper::dateFormatter($data->updated_at);

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