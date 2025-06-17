<?php

use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use common\components\Helpers\StructureHelper;

$permissions = Yii::$app->session->get('permissions');
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
        'value' => 'application.member.full_name',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/applications/member-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->application->member_id];
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
        'attribute' => 'member_cnic',
        'value' => 'application.member.cnic',
    ],
    [
        'class' => ExpandRowColumn::class,
        'attribute' => 'application_no',
        'value' => 'application.application_no',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/loans/application-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->application_id];
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
        'attribute' => 'sanction_no',
    ],

    /*[
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'project_table',
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'loan_amount',
        'value' => function ($model, $key, $index) {
            return number_format($model->loan_amount);
        },
    ],
    /* [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'cheque_no',
     ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'inst_amnt',
        'value' => function ($model, $key, $index) {
            return number_format($model->inst_amnt);
        },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'inst_months',
        'value' => function ($model, $key, $index) {
            return number_format($model->inst_months);
        },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'inst_type',
        'filter' => \common\components\Helpers\ListHelper::getLists('installments_types'),

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'date_disbursed',
        'value' => function ($model, $key, $index) {
            if ($model->date_disbursed != 0) {
                return \common\components\Helpers\StringHelper::dateFormatter($model->date_disbursed);
            }
        },
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
        'attribute' => 'group_no',
        'value' => 'group.grp_no'
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
     [
//     'class'=>'\kartik\grid\DataColumn',
     'attribute'=>'status'
     ],
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
        'attribute' => 'project_id',
        'value' => 'project.name',
        'label' => 'Project',
        'filter' => $data['projects']
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions' => ['style' => 'width:100px;'],
        //'dropdown' => false,
        //'vAlign'=>'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'buttons' => [
            'delete' => function ($url, $model, $key) {
                if ($model->status == 'pending' && $model->date_disbursed == 0) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['composite-updates/loan-delete', 'id' => $model->id],
                        [
                            'role' => 'modal-remote', 'title' => 'Delete', 'data-toggle' => 'tooltip',
                            'data-confirm' => true,
                            'data-confirm-title' => 'Delete Loan',
                            'data-confirm' => 'Are you sure want to delete Loan against Sanction No:' . $model->sanction_no . '',
                        ]
                    );
                }
            },
            'reject' => function ($url, $model, $key) {
                if ($model->date_disbursed == 0) {
                    return \yii\helpers\Html::a('<span class="btn btn-danger glyphicon glyphicon-edit"></span>', ['composite-updates/composite-reject-loan', 'id' => $model->id],
                        [
                            'role' => 'modal-remote', 'title' => 'Reject', 'data-toggle' => 'tooltip',
                            'data-confirm' => true,
                            'data-confirm-title' => 'Reject Loan',
                            'data-confirm' => 'Are you sure want to reject Loan against Sanction No:' . $model->sanction_no . '',
                        ]
                    );
                }
            }
        ],
        'template' => '{reject} {delete}'
    ],

];   