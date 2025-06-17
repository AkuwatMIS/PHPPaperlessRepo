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
        'attribute' => 'member_name',
        'label' => 'Member Name',
        'value' => function ($data) {
            return isset($data->application->member->full_name) ? $data->application->member->full_name : 'N/A';

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
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'house_ownership',
        'filter'=>\common\components\Helpers\ListHelper::getLists('house_ownership')

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'no_of_earning_hands',
        'filter'=>\common\components\Helpers\ListHelper::getLists('no_of_earning_hands')

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'source_of_income',
        'filter'=>\common\components\Helpers\ListHelper::getLists('source_of_income')
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'poverty_index',
    ],*/
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'date_of_maturity',
    ],*/
    [
             'class'=>'\yii\grid\DataColumn',
             'attribute'=>'monthly_savings',
             'filter'=>\common\components\Helpers\ListHelper::getLists('monthly_savings')
       ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'ladies',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'gents',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'source_of_income',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'total_household_income',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'utility_bills',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'educational_expenses',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'medical_expenses',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'kitchen_expenses',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'monthly_savings',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'amount',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'date_of_committee',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'bank_name',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'other_expenses',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'total_expenses',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'loan_amount',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'economic_dealings',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'social_behaviour',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'fatal_disease',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'child',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'disease_type',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'latitude',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'longitude',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'status',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'approved_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'approved_on',
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
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'deleted',
    // ],
    ['class' => 'yii\grid\ActionColumn',  /// here I need to edit or remove delete button
        'contentOptions' => ['style' => 'width:60px;'],

        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'buttons' => [

        ],
        'buttons'=>[
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
    ]
];   