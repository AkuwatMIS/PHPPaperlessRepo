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
        'value'=>'member.full_name',
        'label'=>'Member Name',
        'class' => \dimmitri\grid\ExpandRowColumn::class,
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/applications/member-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->member->id];
        },
        'enableCache' => false,
        'format' => 'raw',
        'expandableOptions' => [
            'title' => 'Click me!',
            'class' => 'my-expand',
        ],

    ],
    //[
        //'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'fee',
   // ],
    [
      //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_no',
        'class' => \dimmitri\grid\ExpandRowColumn::class,
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/loans/application-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->id];
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
        'attribute'=>'loan.sanction_no',
        'label'=>'Sanction No',
        'class' => \dimmitri\grid\ExpandRowColumn::class,
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/loans/loan-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->loan->id];
        },
        'enableCache' => false,
        'format' => 'raw',
        'expandableOptions' => [
            'title' => 'Click me!',
            'class' => 'my-expand',
        ],


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
         'filter'=> $regions
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'area_id',
         'value'=>'area.name',
         'label'=>'Area',
         'filter'=> $areas
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'branch_id',
         'value'=>'branch.name',
         'label'=>'Branch',
         'filter'=> $branches
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
         'value'=>function($data){return isset($data->req_amount)?number_format($data->req_amount):'Not Set';},
     ],
     /*[
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
     ],*/
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
        'value'=>'project.name',
        'label'=>'Project',
        'filter'=> \yii\helpers\ArrayHelper::map(StructureHelper::getProjects(),'id','name')
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view} {update}  {logs}',
        'buttons' => [
            'logs' => function ($url, $model, $key) {

                if(isset($model->membersLogs) && !empty($model->applicationsLogs)){
                    return \yii\helpers\Html::a('Logs', ['/applications/logs', 'id'=>$model->id]);
                }

            },
            'update' => function ($url, $model) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['/applications/update', 'id'=>$model->id], [
                    'title' => Yii::t('app', 'update'),
                ]);
            },
            'view' => function ($url, $model) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/applications/view', 'id'=>$model->id], [
                    'title' => Yii::t('app', 'view'),
                ]);
            },
        ],
        'contentOptions' => ['style' => 'width:70px;'],
    ],

];   