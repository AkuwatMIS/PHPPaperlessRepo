<?php

use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\helpers\Html;

CrudAsset::register($this);

$permissions = Yii::$app->session->get('permissions');
return [
    [
        'class' => 'yii\grid\SerialColumn',
    ],
    [
        'attribute' => 'member_name',
        'value' => 'application.member.full_name',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'is_pledged',
        'label' => 'Pledge Status',
        'value' => function ($model, $key, $index) {
            if($model->application->is_pledged == null){
                return 'Pending';
            }
        },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'member_cnic',
        'value' => 'application.member.cnic',
    ],
//    [
//        //'class'=>'\kartik\grid\DataColumn',
//        'attribute' => 'application.application_no',
//    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'sanction_no',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'loan_amount',
        'value' => function ($model, $key, $index) {
            return number_format($model->loan_amount);
        },
    ],
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
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'group_no',
        'value' => 'group.grp_no'
    ],

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
            'pledge' => function ($url, $model, $key) {
                if ($model->disbursement_id == 0 && $model->fund_request_id == 0) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update-pledge-status', 'id' => $model->application->id],
                        [
                            'role' => 'modal-remote', 'title' => 'Pledge', 'data-toggle' => 'tooltip',
                            'data-confirm'=>true,
                            'data-confirm-title'=>'Pledge Loan',
                            'data-confirm'=>'Are you sure want to perform Loan Pledge action against Sanction No:'.$model->sanction_no.'',
                        ]
                    );

                    //return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update-pledge-status', 'id'=>$model->application->id], ['target'=>'_blank'],['title'=>'Pledge']);
                }
            }
        ],
        'template' => '{pledge}'
    ]
];   