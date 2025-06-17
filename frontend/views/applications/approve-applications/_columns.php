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
        'value'=>'member.full_name',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/applications/member-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->member_id];
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
        'value'=>'member.cnic',
    ],
    //[
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'fee',
   // ],
    [
      //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_no',
    ],
    /*[
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_table',
    ],*/
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
         'attribute'=>'region_id',
         'value'=>'region.name',
         'label'=>'Region',
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'area_id',
         'value'=>'area.name',
         'label'=>'Area',
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'branch_id',
         'value'=>'branch.name',
         'label'=>'Branch',
     ],
     /*[
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'team_id',
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'field_id',
     ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'no_of_times',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'bzns_cond',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'who_will_work',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'name_of_other',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'other_cnic',
    // ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'req_amount',
         'value'=> function ($model, $key, $index, $column){
             return number_format($model->req_amount);
         },
     ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_date',
        'value'=>function ($data, $key, $index) {
            return \common\components\Helpers\StringHelper::dateFormatter($data->application_date);
        },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'value'=>'project.name',
        'label'=>'Project',
    ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
         'filter'=> common\components\Helpers\ApplicationHelper::getAppStatus(),
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'is_urban',
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
];   