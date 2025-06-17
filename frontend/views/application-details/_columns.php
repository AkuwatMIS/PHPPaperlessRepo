<?php

use yii\helpers\Url;

return [
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'id',
    // ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'branch',
        'value' => function ($data) {
            return $data->application->branch->name;
        },
//        'value'=>function($data){return $data->application->branch_id; },
    ],

    [
//        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'full_name',
        'value' => function ($data) {
            return $data->application->member->full_name;
        },

    ],

    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'cnic',
        'value' => function ($data) {
            return $data->application->member->cnic;
        },
        // 'value'=>$array['cnic'],

    ],
//    [
//        //'class'=>'\kartik\grid\DataColumn',
//        'value'=>function($data){return $data->application->region->name; },
//        'attribute'=>'region',
//    ],
//    [
//        //'class'=>'\kartik\grid\DataColumn',
//        'value'=>function($data){return $data->application->area->name; },
//        'attribute'=>'area',
//
//    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'poverty_score',
    ],
//    [
//        //'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'application_id',
//    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'project_id',
//        'filter' => array('77' => 'Kamyab Pakistan Low Cost Housing (KP-LCH)', '78' => 'Kamyab Jawan (Kamyab Karobar)', '79' => 'Kamyab Jawan (Kamyab Kissan)', '105' => 'Prime Minister Youth Business Loan Scheme (PM-YBLS)', '106' => 'Prime Minister  Agriculture Loan Scheme  (PM-ALS)'),
        'filter' => array('105' => 'Prime Minister Youth Business Loan Scheme (PM-YBLS)', '106' => 'Prime Minister  Agriculture Loan Scheme  (PM-ALS)'),
        'value' => function ($data) {
            return $data->application->project->name;
        }
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'status',
        'filter' => array('0' => 'Pending', '1' => 'Processed'),
        'value' => function ($model) {
              if($model->status==0){
                  $status = 'Pending';
              }else{
                  $status = 'Processed';
              }
            return $status;
        }
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'action_date',
        'value' => function ($model) {
            return (!empty($model->action_date) && $model->action_date!=null)?date('d M Y', $model->action_date):'NA';
        },
        'label'=>'PMT Date'
    ]


//    [
//        'class' => 'yii\grid\ActionColumn',
//        'buttons' => [
//
//            'update' => function ($url, $model) {
//                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['quick-update', 'id' => $model->id], [
//                    'data-method' => 'post', 'data-pjax' => '0',
//                ]);
//            }
//        ],
//
//        'template' => '{view}',
//        'contentOptions' => ['style' => 'width:70px;'],
//    ],

];
