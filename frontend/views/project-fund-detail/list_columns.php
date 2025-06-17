<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'batch_no',
        'label'=>'Batch No',
        'value'=> function ($model){
            return $model->batch->batch_no;
        },

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
        'label'=>'Sanction No',
        'value'=> function ($model){
            return $model->loan->sanction_no;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
        'label'=>'CNIC',
        'value'=> function ($model){
            return $model->loan->application->member->cnic;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'full_name',
        'label'=>'Full Name',
        'value'=> function ($model){
            return $model->loan->application->member->full_name;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'bank_name',
        'label'=>'Bank Name',
        'value'=> function ($model){
            return $model->publish->bank_name;
        },
    ],
    [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'account_no',
         'label'=>'Account No',
        'value'=> function ($model){
            return $model->publish->account_no;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranch_amount',
        'label'=>'Tranch Amount',
        'value'=>function ($model, $key, $index) {
            return $model->tranch_amount;
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'visibleButtons' => [
           /* 'delete' => function ($model) {
                if (($model->status == 1)) {
                    return true;
                }
            }*/
        ],
        'buttons'=>[
            'delete' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['remove-batch', 'id' => $model->id], ['data-toggle'=>'tooltip','title' => 'Remove From Batch']);
            },
            'status' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['edit-loan-batch-no', 'id' => $model->id], ['role'=>'modal-remote','data-toggle'=>'tooltip','title' => 'Update']);
            },
        ],
        'template'=>'{delete} {status}',
    ],



];   