<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],

//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'region_id',
//        'label'=>'Region',
//        'value'=>'region.name',
//    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'area_id',
//        'label'=>'Area',
//        'value'=>'area.name',
//    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'label'=>'Branch',
        'value'=>'branch.name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'full_name',
        'label'=>'Name',
        'value'=>'info.member.full_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'parentage',
        'label'=>'Parentage',
        'value'=>'info.member.parentage',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
        'label'=>'CNIC',
        'value'=>'info.member.cnic',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic_issue_date',
        'label'=>'CNIC Issue Date',
        'value'=>'info.cnic_issue_date',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic_expiry_date',
        'label'=>'CNIC Expiry Date',
        'value'=>'info.cnic_expiry_date',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'reject_reason',
        'value'=>'reject_reason'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'remarks',
        'value'=>'remarks'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'rejected_date',
        'label'=>'Rejected Date',
        'value'=>function ($model) {
            if(isset($model->rejected_date)) {
                $nadra =  \common\components\Helpers\StringHelper::dateFormatter($model->rejected_date);
            } else {
                $nadra =  'NULL';
            }
            return  $nadra;
        },
    ],

    [
        'class' => 'kartik\grid\ActionColumn',
        'buttons'=>[
            'submit' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['add-remarks-nadra-verisys','id'=>$model->id],['role'=>'modal-remote','title'=>'Submit','data-toggle'=>'tooltip']);
            }
        ],
        'template'=>'{submit}',
        'contentOptions' => ['style' => 'width:70px;'],
    ],
];   