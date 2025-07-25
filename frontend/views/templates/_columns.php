<?php
use yii\helpers\Url;

return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
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
        'attribute'=>'event_id',
        'label'=>'Event',
        'filter'=>\yii\helpers\ArrayHelper::map(\common\models\Events::find()->all(),'id','name'),
        'value'=>'event.name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'template_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'format'=>'html',
        'attribute'=>'template_text',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'template_type',
        'filter'=>['email'=>'Email','file'=>'File','both'=>'Both'],
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'subject',
    ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'email',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'send_to',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'is_active',
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
        'template'=>'{view} {delete}'
    ],

];   