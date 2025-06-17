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
        'attribute'=>'batch_no',
        'label'=>'Batch No',

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'name',
        'label'=>'Funding Line',
        'value'=> function ($model){
            return $model->fund->name;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'disbursement_source',
        'label'=>'Disbursement Source',
        'value'=> function ($model){
            return $model->disbursement_source;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'label'=>'Project',
        'value'=> function ($model){
            return $model->project->name;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'no_of_loans',
        'label'=>'No of Loans',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fund_batch_amount',
        'label'=>'Amount (Rs.)',
        'value'=> function ($model){
            return number_format($model->fund_batch_amount);
        },
    ],
    [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'allocation_date',
         'label'=>'Batch Creation Date',
         'value'=>function ($model, $key, $index) {
             return \common\components\Helpers\StringHelper::dateFormatter($model->allocation_date);
         },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'txn_mode',
        'label'=>'Transaction Mode',
        'value'=>function ($model, $key, $index) {
            return $model->transaction->txn_mode;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'txn_no',
        'label'=>'Transaction No',
        'value'=>function ($model, $key, $index) {
            return $model->transaction->txn_no;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'received_at',
        'label'=>'Receive Date',
        'value'=>function ($model, $key, $index) {
            if(isset($model->transaction->received_at)) {
                return \common\components\Helpers\StringHelper::dateFormatter($model->transaction->received_at);
            }
            return null;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
        'value'=> function ($model){
           return \common\components\Helpers\StatusHelper::projectFundDetailStatus($model->status);
        },
    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'status',
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
        'visibleButtons' => [
            'update' => function ($model) {
//                if (($model->status == 1)) {
                    return true;
//                }
            },
            'status' => function ($model) {
                $userDesignation = Yii::$app->user->identity['designation_id'];
                $dArray = array("18","1","37");
                if (($model->status == 0) && (in_array("$userDesignation", $dArray) )) {
                    return true;
                }
            }
        ],
        'buttons'=>[
            'status' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-ok"></span>', ['approve-batch', 'id' => $model->id], ['role'=>'modal-remote','data-toggle'=>'tooltip','title' => 'Approve']);
            },
            'view' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->id], ['data-toggle'=>'tooltip','title' => 'View']);
            },
            'export' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-export"></span>', ['index','export' =>'export', 'id' => $model->id], ['title' => 'Export']);
            },
        ],
        'template'=>'{status} {update} {export} {view}',
        //'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
      /*  'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete',
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'], */
    ],

];   