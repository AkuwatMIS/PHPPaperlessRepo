<?php
use yii\helpers\Url;


return [
    [
        'class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function ($model) {
        return ['value' => $model->id];
    }
    ],
    [
        'class' => 'yii\grid\SerialColumn',
    ],

    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'id',
    // ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'application_id',
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'project_id',
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'project_table',
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'date_approved',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'loan_amount',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'cheque_no',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'inst_amnt',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'inst_months',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'inst_type',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'date_disbursed',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'cheque_dt',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'disbursement_id',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'activity_id',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'product_id',
    // ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'group_id',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'region_id',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'area_id',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'branch_id',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'team_id',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'field_id',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'loan_expiry',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'loan_completed_date',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'old_sanc_no',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'remarks',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'br_serial',
    // ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'sanction_no',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'due',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'overdue',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'balance',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'status',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'reject_reason',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'is_lock',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'deleted',
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
        'class' => 'yii\grid\ActionColumn',
        'contentOptions' => ['style' => 'width:70px;'],
    ],

];
