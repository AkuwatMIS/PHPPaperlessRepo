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
        'attribute'=>'cnic',
        'value'=>'loan.application.member.cnic',
    ],
    [
      //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'parentage',
        'value'=>'loan.application.member.parentage',
    ],
     [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranche_amount',
        'value'=> function ($model, $key, $index, $column){
            return number_format($model->tranch_amount);
        },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
        'value'=> 'loan.sanction_no'
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'grp_no',
        'value'=> 'loan.group.grp_no'
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'grp_no',
        'value'=> 'loan.group.grp_no'
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'tranch_no',
        'value'=>'tranch_no'
    ],*/
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project',
        'value'=>'loan.project.name'
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'label'=>'Bank Name',
        'value'=>'loan.application.member.memberAccount.bank_name'
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'label'=>'Account Title',
        'value'=>'loan.application.member.memberAccount.title'
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'label'=>'Account No',
        'value'=>'loan.application.member.memberAccount.account_no'
    ],
];   