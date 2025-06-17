<?php
use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use common\components\Helpers\StructureHelper;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'contentOptions' => ['style' => 'width:50px'],
        'multiple'=>true,
        'checkboxOptions' => function($model, $key, $index, $widget) {
            return ['checked'=>true,'value'=> $model->id ];
            //  return [];


        },
    ],
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],

    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'id',
    // ],
    // advanced example
    [
        'class' => ExpandRowColumn::class,
        'attribute' => 'full_name',
        'value'=>'application.member.full_name',
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
        'attribute'=>'cnic',
        'value'=>'application.member.cnic',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'parentage',
        'value'=>'application.member.parentage',
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranche_amount',
        'value'=> function ($model, $key, $index, $column){
            return number_format($model->tranch_amount);
        },
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
        'value'=> 'sanction_no'
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'grp_no',
        'value'=> 'loan.group.grp_no'
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'grp_no',
        'value'=> 'group.grp_no'
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranch_no',
        'value'=>'tranch_no'
    ],*/
];   