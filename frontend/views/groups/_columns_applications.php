<?php
use yii\helpers\Url;
use common\components\Helpers\StructureHelper;

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
        'attribute'=>'member_id',
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
         'label'=>'Region',
         'filter'=> \yii\helpers\ArrayHelper::map(StructureHelper::getRegions(),'id','name')
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'area_id',
         'label'=>'Area',
         'filter'=> \yii\helpers\ArrayHelper::map(StructureHelper::getAreas(),'id','name')
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'branch_id',
         'label'=>'Branch',
         'filter'=> \yii\helpers\ArrayHelper::map(StructureHelper::getBranches(),'id','name')
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
         'format'=>'decimal'
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
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
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'label'=>'Project',
        'filter'=> \yii\helpers\ArrayHelper::map(StructureHelper::getProjects(),'id','name')
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update} {delete} {logs}',
        'buttons' => [
            'logs' => function ($url, $model, $key) {

                if(isset($model->membersLogs) && !empty($model->applicationsLogs)){
                    return \yii\helpers\Html::a('Logs', ['/applications/logs', 'id'=>$model->id]);
                }

            },
        ],
        'contentOptions' => ['style' => 'width:70px;'],
    ],

];   