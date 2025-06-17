<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
        'value'=>'region.name',
        'label'=>'Region',
        'filter'=>$array['regions']

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'value'=>'area.name',
        'label'=>'Area',
        'filter'=>$array['areas']

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'value'=>'branch.name',
        'label'=>'Branch',
        'filter'=>$array['branches']
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_date',
        'value'=>function ($data, $key, $index) {
            return date('d M Y',$data->application_date);
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'full_name',
        //'value'=>'member.full_name',
        'label'=>'Member Name',
        'format'=>'raw',
        //'value'=>'member.full_name',
        'value' => function ($data) {
         return \yii\helpers\Html::a($data->member->full_name, ['members/view', 'id' => $data->member_id],['target'=>'_blank'],['title'=>'Member']);
         },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
        'value'=>'member.cnic',
    ],
    [
        'class'=>'kartik\grid\ExpandRowColumn',
        'value'=> function ($model, $key, $index, $column){
                return \kartik\grid\GridView::ROW_COLLAPSED;
        },
        'detail' => function($model, $key, $index, $column){
            return $this->render('_member-details', [
                'member' => \common\models\Members::find()->where(['id' => $model->member_id])->one(),
                'model' => $model
            ]);
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'req_amount',
        'format'=>'integer'

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fee',
        'format'=>'integer'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'label'=>'Project',
        'value'=>'project.name',
        'filter'=>$array['projects']
    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'project_table',
//    ],
//     [
//         'class'=>'\kartik\grid\DataColumn',
//         'attribute'=>'activity_id',
//         'value'=>'activity.name',
//         'label'=>'Activity'
//     ],
//     [
//         'class'=>'\kartik\grid\DataColumn',
//         'attribute'=>'product_id',
//         'value'=>'product.name',
//         'label'=>'Product'
//     ],

    // [
    //'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'team_id',
    //'value'=>'activity.name',
    //'label'=>'Activity'
    //],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'field_id',
    // ],
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
        'attribute' => 'referral_id',
        'value'=> function ($model, $key, $index, $column){
            return $model->referral->name;
        },
        'filter'=>$array['referrals']
    ],

    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'status',
        'value'=> function ($model, $key, $index, $column){
            return ucfirst($model->status);
        },
        'filter'=> \common\components\Helpers\ListHelper::getLists('application_status')
    ],

    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'is_urban',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'reject_reason',
    // ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'is_lock',
         'value'=>function($data){
             if($data->is_lock==0){
                 return 'Un Locked';
             }
             else{
                 return 'Locked';
             }
         },
         'filter'=>$array['is_lock'],

     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'deleted',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'assigned_to',
    // ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'created_by_name',
         'label'=>'Created By',
         'value'=>'user.username'
     ],
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
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>'Delete', 
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>'Are you sure?',
                          'data-confirm-message'=>'Are you sure want to delete this item'], 
    ],

];   