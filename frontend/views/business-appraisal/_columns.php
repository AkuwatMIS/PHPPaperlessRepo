<?php
use yii\helpers\Url;

return [

    [
        'class' => 'yii\grid\SerialColumn',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_name',
        'label'=>'Member Name',
        'value'=>function($data){
            return isset($data->application->member->full_name)?$data->application->member->full_name:'N/A';

        },
        'class' => \dimmitri\grid\ExpandRowColumn::class,
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/applications/member-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->application->member->id];
        },
        'enableCache' => false,
        'format' => 'raw',
        'expandableOptions' => [
            'title' => 'Click me!',
            'class' => 'my-expand',
        ],
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_cnic',
        'label'=>'Member CNIC',
        'value'=>function($data){
            return isset($data->application->member->cnic)?$data->application->member->cnic:'N/A';

        }
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_no',
        'label'=>'Application No',
        'value'=>function($data){
            return isset($data->application->application_no)?$data->application->application_no:'N/A';

        },
        'class' => \dimmitri\grid\ExpandRowColumn::class,
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/loans/application-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->application->id];
        },
        'enableCache' => false,
        'format' => 'raw',
        'expandableOptions' => [
            'title' => 'Click me!',
            'class' => 'my-expand',
        ],
    ],
    [
        'attribute'=>'business',
        'filter'=>\common\components\Helpers\ListHelper::getLists('business'),
    ],
    [
        'attribute'=>'business_type',
        'value'=>'application.activity.name'
    ],
    [
        'attribute'=>'place_of_business',
        'filter'=>\common\components\Helpers\ListHelper::getLists('place_of_business'),
    ],
    /*[
        'attribute'=>'business_details',
    ],*/
     /*[
         'attribute'=>'business_income',
     ],*/
     /*[
         'attribute'=>'job_income',
     ],*/
     /*[
         'attribute'=>'house_rent_income',
     ],*/
     /*[
         'attribute'=>'other_income',
     ],*/
     /*[
         'attribute'=>'estimated_business_capital',
     ],*/
     /*[
         'attribute'=>'business_expenses',
     ],*/
     /*[
         'attribute'=>'income_before_business',
     ],*/
     /*[
         'attribute'=>'total_business_income',
     ],*/
    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions' => ['style' => 'width:60px;'],

        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'buttons' => [
            'show' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['applications/view', 'id' => $model->application_id], ['title' => 'Ledger']);
            },
            'update' => function ($url, $model, $key) {
                if (!isset($model->application->loans)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id], ['role' => 'modal-remote', 'title' => 'Update', 'data-toggle' => 'tooltip']);
                }
            },
        ],
        'template' => '{show} {update} {delete}',

        'contentOptions' => ['style' => 'width:70px;'],
    ],

];   