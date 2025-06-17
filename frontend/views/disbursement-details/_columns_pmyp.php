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
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'payment_method_id',
        'value'=>'payment.name',
        'label' => 'Payment Method',
        //'filter' => $branches_names,
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranche_id',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranche_id',
        'label'=>'Tranche No',
        'value' => function ($data, $key, $index) {
            return $data->tranch->tranch_no;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'batch_id',
        'label'=>'Batch ID',
        'value' => function ($data, $key, $index) {
            return $data->tranch->batch->batch_no;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'bank_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'label' => 'Name',
        'attribute' => 'title',
        'value'=>'tranch.loan.application.member.full_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'label' => 'Account Title',
        'attribute' => 'title',
        'value'=>'tranch.loan.application.member.verifiedAccount1.title',
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
        'attribute'=>'activity_id',
        'value' => function ($data, $key, $index) {
            return $data->tranch->loan->activity->name;
        },
        'label'=>'Activity',
        'filter' => \common\components\Helpers\ListHelper::getActivities(),
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

    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_at',
        'value' => function ($data, $key, $index) {
            return date("d M y", $data->created_at);
        },
        'label'=>'Date Published',
    ],

    [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
         'value' => function ($data, $key, $index) {
             return \common\components\Helpers\ListHelper::getDisbursementDetailStatusView($data->status);
         },
        'filter' => \common\components\Helpers\ListHelper::getDisbursementDetailStatus(),
    ],
    [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'response_description',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'pmt',
        'label'=>'Poverty Score',
        'value' => function ($data, $key, $index) {
            return $data->tranch->loan->application->pmtStatus->poverty_score;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'province_id',
        'label'=>'Province',
        'value' => function ($data, $key, $index) {
            return $data->tranch->loan->application->branch->province->name;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'district_id',
        'label'=>'District',
        'value' => function ($data, $key, $index) {
            return $data->tranch->loan->application->branch->district->name;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'age',
        'label'=>'DOB',
        'value' => function ($data, $key, $index) {
            return date('Y-m-d',$data->tranch->loan->application->member->dob);
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'gender',
        'label'=>'Gender',
        'value' => function ($data, $key, $index) {
                 $gender = $data->tranch->loan->application->member->gender;
                 if($gender == 'f'){
                     $gender = 'Female';
                 }elseif ($gender == 'm'){
                     $gender = 'Male';
                 }else{
                     $gender = 'Others';
                 }
            return $gender;
        },
    ],
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