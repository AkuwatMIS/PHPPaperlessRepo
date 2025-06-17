<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],

    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'application_id',
        'value' => 'application.application_no',
        'label' => 'Application No',

    ],
    [
        'attribute'=>'member_cnic',
        'value'=>'application.member.cnic',
        'label'=>'CNIC',
    ],
    [
        'attribute'=>'project_id',
        'value'=>'application.project.name',
        'label'=>'Project',
    ],
    [
        'attribute'=>'branch_id',
        'value'=>'application.branch.name',
        'label'=>'Branch',
    ],
    [
        'attribute'=>'cib_type_id',
        'value'=> function ($data, $key, $index) {
            if($data->cib_type_id == 0 || $data->cib_type_id == 1){
                return 'Tasdeeq';
            }else{
                return 'DataCheck';
            }
        },
        'label'=>'Provider',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'type',
        'value'=> function ($data, $key, $index) {
            if($data->type == 0){
                if($data->response == "" || $data->response == null){
                    return 'Pending Response';
                }else{
                    return 'Json Data';
                }
            }else{
                return 'Pdf FIle';
            }
        },
        'label' => 'Response Type',

    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'fee',
        'value' => 'fee',
        'label' => 'Fee',

    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'receipt_no',
        'value' => 'receipt_no',
        'label' => 'Receipt No',

    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'value' => 'status',
        'label' => 'Status',

    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'type',
        'value' => 'type',
        'label' => 'Type',

    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'updated_at',
        'value'=>function ($data, $key, $index) {
            return date('d M Y',$data->updated_at);
        },
        'label' => 'Updated At',

    ],

];   