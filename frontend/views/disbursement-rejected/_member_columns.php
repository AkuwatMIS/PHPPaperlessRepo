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
        'attribute'=>'sanction_no',
    ],
    [
        'attribute'=>'loan_amount',
        'value'=>function ($model, $key, $index) {
            return number_format($model['loan_amount']);
        },
    ],
     [
         'attribute'=>'inst_amnt',
         'value'=>function ($model, $key, $index) {
             return number_format($model['inst_amnt']);
         },
     ],
     [
         'attribute'=>'inst_months',
         'value'=>function ($model, $key, $index) {
             return number_format($model['inst_months']);
         },
     ],
     [
         'attribute'=>'inst_type',
     ],
     [
         'attribute'=>'date_disbursed',
         'value'=>function ($model, $key, $index) {
             return date('d M Y',$model['date_disbursed']);
         },
     ],

     [
         'attribute'=>'group_no',
         'value'=>function ($model, $key, $index) {
             return $model['grp_no'];
         },
     ],
    [
        'attribute'=>'project',
        'value'=>function ($model, $key, $index) {
            return $model['project_name'];
        },

    ],

];