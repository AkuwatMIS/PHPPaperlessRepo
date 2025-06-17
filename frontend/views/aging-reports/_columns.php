<?php
use yii\helpers\Url;

return [
   /* [
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
        'attribute'=>'start_month',
        'label'=>'Month',
        'value'=>function($model){
            return date('d M Y',strtotime($model->start_month));
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
        'filter'=> array('due'=>'OLP Aging','overdue'=>'Overdue Aging'),
        'value'=>function($model){
            if($model->type == 'due'){
                return 'OLP Aging';
            }else if ($model->type == 'overdue'){
                return 'Overdue Aging';
            }else if ($model->type == 'overdue_acc'){
                return 'Overdue Aging ACC';
            }else if ($model->type == 'due_acc'){
                return 'OLP Aging ACC';
            }
        }
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'next_three_months',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'next_six_months',
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'next_one_year',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'next_two_year',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'next_three_year',
     ],*/
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'file_name',
     ],
     /*[
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'total',
     ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'status',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],
    /*[
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
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_at',
        'value'=>function($model){
            return date('Y-m-d h:i:s a',$model->created_at);
        },
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'status',
    // ],
    [
        'class' => 'yii\grid\ActionColumn',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons' => [
            'download' => function ($url, $model, $key) {
                $folder = 'overdue_report';
                //return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', ['/exports/'.$model->file_name], ['target'=>'_blank','download'=>'download'],['title'=>'Download File']);
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-download-alt"></span>', ['/aging-reports/exports', 'folder' => $folder, 'file_name' => $model->file_name], ['target' => '_blank', 'data-pjax' => '0',], ['title' => 'Download File']);
                \yii\widgets\Pjax::end();
            },
            'report-delete' => function ($url, $model, $key) {
                return \kartik\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['delete', 'id' => $model->id], ['target' => '_blank', 'data-pjax' => '0',]));

            },
        ],
        'template' => '{download} {view} {update} {report-delete}',
        'visibleButtons' => [
            'download' => function ($model) {
                $visible = false;
                if ($model->file_name != null) {
                    $visible = true;
                    return $visible;
                }
            },
        ],
        'contentOptions' => ['style' => 'width:100px;'],
    ],

];   