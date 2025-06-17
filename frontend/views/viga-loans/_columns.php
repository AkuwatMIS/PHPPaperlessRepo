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
        'attribute'=>'member_name',
        'label'=>'Name',
        'value'=>'loan.application.member.full_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_parentage',
        'label'=>'Parentage',
        'value'=>'loan.application.member.parentage',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_cnic',
        'label'=>'CNIC',
        'value'=>'loan.application.member.cnic',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
        'label'=>'Sanction No',
        'value'=>'loan.sanction_no',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'group_no',
        'label'=>'Group No',
        'value'=>'loan.group.grp_no',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'date_disbursed',
        'label'=>'Date Disbursed',
        'value'=>function ($model, $key, $index) {
            if($model->loan->date_disbursed != 0) {
                return \common\components\Helpers\StringHelper::dateFormatter($model->loan->date_disbursed);
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
        'value'=> function ($data, $key, $index) {
            return \common\components\Helpers\StructureHelper::getFilesaccountsstatus($data->status);
        },
        'filter'=>\common\components\Helpers\ListHelper::getBankfileStatus(),
        'label'=>'Status',
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'created_by',
        'value'=>'users.username',
        'label'=>'Created By',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
       // 'attribute'=>'updated_by',
        'value'=>'users.username',
        'label'=>'Updated By',
    ],*/
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