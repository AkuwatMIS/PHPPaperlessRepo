<?php
use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use common\components\Helpers\StructureHelper;
use \yii\helpers\Html;
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
        //'value'=>'application.member.full_name',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/applications/member-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model['member_id']];
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
       // 'value'=>'application.member.cnic',
    ],
    [
        'class' => ExpandRowColumn::class,
        'attribute' => 'application_no',
        //'value'=>'application.application_no',
        'value'=>function ($model, $key, $index) {
            return ($model['application_no']);
        },
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/loans/application-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model['application_id']];
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
        'attribute'=>'sanction_no',
    ],

    /*[
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'project_table',
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'loan_amount',
        'value'=>function ($model, $key, $index) {
            return number_format($model['loan_amount']);
        },
    ],
    /* [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'cheque_no',
     ],*/
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'inst_amnt',
         'value'=>function ($model, $key, $index) {
             return number_format($model['inst_amnt']);
         },
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'inst_months',
         'value'=>function ($model, $key, $index) {
             return number_format($model['inst_months']);
         },
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'inst_type',
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'date_disbursed',
         'value'=>function ($model, $key, $index) {
             return date('d M Y',$model['date_disbursed']);
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
         'attribute'=>'group_no',
         'value'=>function ($model, $key, $index) {
             return $model['grp_no'];
         },
         //'value'=>'group.grp_no'
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
        'attribute'=>'project',
        'value'=>'project.name',
        //'label'=>'Project',
        'value'=>function ($model, $key, $index) {
            return $model['project_name'];
        },
        //'filter'=> $projects
    ],
    /*[
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view}',
        'contentOptions' => ['style' => 'width:70px;'],
    ],*/
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'ledger' => function ($url, $model, $key) {
                $secretKey='123qwe';
                $encryptedData = Yii::$app->getSecurity()->encryptByPassword($model['id'], $secretKey);

                return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['loans/global-ledger', 'id'=>$encryptedData], ['target'=>'_blank'],['title'=>'Ledger']);
            },
            'audit' => function ($url, $model, $key) {
                return Html::a('logs', ['/loans/audit-log','id'=>$model['id']],['role'=>'modal-remote','title'=>'Audit','data-toggle'=>'tooltip']);
            },
        ],
        'template' => '{ledger}',

        /*'visibleButtons' =>
            [
                'audit' =>  function($model) use ($permissions) {
                    return in_array('audit-logloans',$permissions) && \common\components\AuditTrailHelper::getRecord($model);
                },
                'view' => in_array('viewloans',$permissions),
                'ledger' => in_array('ledgerloans',$permissions),
                //'update' => in_array('updateloans',$permissions),
                'delete' => in_array('deleteloans',$permissions)
            ],*/
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