<?php
use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use common\components\Helpers\StructureHelper;
$permissions = Yii::$app->session->get('permissions');
$total=0;
foreach($dataProviderLoans->getModels() as $m){
    $total+=$m->tranch_amount;
}

return [
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
        'class' => ExpandRowColumn::class,
        'attribute' => 'member_name',
        'value'=>'loan.application.member.full_name',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/applications/member-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->loan->application->member_id];
        },
        'enableCache' => false,
        'format' => 'raw',
        'expandableOptions' => [
            'title' => 'Click me!',
            'class' => 'my-expand',
        ],
        /*'contentOptions' => [
            'style' => 'display: flex; justify-content: space-between;',
        ],*/
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_cnic',
        'value'=>'loan.application.member.cnic',
    ],
    [
        'class' => ExpandRowColumn::class,
        'attribute' => 'application_no',
        'value'=>'loan.application.application_no',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/loans/application-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->loan->application_id];
        },
        'enableCache' => false,
        'format' => 'raw',
        'expandableOptions' => [
            'title' => 'Click me!',
            'class' => 'my-expand',
        ],
        /*'contentOptions' => [
            'style' => 'display: flex; justify-content: space-between;',
        ],*/
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'loan.sanction_no',
        'footer' =>'<b>Total</b>',

    ],

    /*[
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'project_table',
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranch_no',
        'value'=>"tranch_no",
        //'footer' =>'<b>'.number_format($total).'</b>',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranch_amount',
        'value'=>function ($model, $key, $index) {
            return number_format($model->tranch_amount);
        },
        'footer' =>'<b>'.number_format($total).'</b>',
    ],
    /* [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'cheque_no',
     ],*/
     /*[
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'loan.inst_amnt',
         'value'=>function ($model, $key, $index) {
             return number_format($model->loan->inst_amnt);
         },
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'loan.inst_months',
         'value'=>function ($model, $key, $index) {
             return number_format($model->loan->inst_months);
         },
     ],*/
     /*[
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'loan.inst_type',
         'filter'=> \common\components\Helpers\ListHelper::getLists('installments_types'),

     ],*/
    /* [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'date_disbursed',
         'value'=>function ($model, $key, $index) {
             return date('d M Y',$model->date_disbursed);
         },
     ],*/
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
         'attribute'=>'loan.group_no',
         'value'=>'loan.group.grp_no'
     ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'loan.loan_amount',
        'value'=>'loan.loan_amount',
        'value'=>function ($model, $key, $index) {
            return isset($model->loan->loan_amount)?number_format($model->loan->loan_amount):'';
        },
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
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'loan.project',
        'value'=>'loan.project.name',
        'label'=>'Project',
        'filter'=> $projects
    ],
];   