<?php
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
        'attribute'=>'full_name',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'parentage_type',
        'value'=> function ($model, $key, $index, $column){
            return ucfirst($model->parentage_type);
        },
        'filter'=> \common\components\Helpers\ListHelper::getLists('parentage_types')
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cnic',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'gender',
        'value'=>function($data){
            if($data->gender=='m'){return 'Male';}
            else if($data->gender=='f'){return 'Female';}
            else{return 'Transgender';}
        },
        'filter'=> \common\components\Helpers\ListHelper::getLists('gender')
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'dob',
         'value'=>function($data){return date('d-M-Y', $data->dob) ;},
         'filter'=>\kartik\date\DatePicker::widget([
             'name' => 'MembersSearch[dob]',
             'options' => ['placeholder' => 'Date of Birth',
             ],
             'pluginOptions' => [
                 'todayHighlight' => true,
                 'format' => 'yyyy-mm-dd',
             ]])
     ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'marital_status',
        'value'=> function ($model, $key, $index, $column){
            return ucfirst($model->marital_status);
        },
        'filter'=> \common\components\Helpers\ListHelper::getLists('marital_status')
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'education',
        'value'=> function ($model, $key, $index, $column){
            return ucfirst($model->education);
        },
        'filter'=> \common\components\Helpers\ListHelper::getLists('education')
    ],
     /*[
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'marital_status',
     ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'family_no',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'family_head',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'family_member_name',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'family_member_cnic',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'religion',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'profile_pic',
    // ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'status',
        'filter'=> \common\components\Helpers\ListHelper::getLists('member_status')
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
        'attribute'=>'branch_id',
        'value'=>'branch.name',
        'label'=>'Branch',
        'filter'=>$array['branches']
    ],
     /*[
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'created_by',
         'value'=>'user.username'
     ],*/
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