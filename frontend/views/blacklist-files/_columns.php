<?php
use yii\helpers\Url;

return [

        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'file_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'result_file_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
        'value' => function($model)
        {
            if($model->status == '0') {
                return 'Open';
            }  else {
                return 'Completed';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_by',
        'value'=>function($model){
            return $model->user->fullname;
        },
    ],
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
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons' => [
            'download' => function ($url, $model, $key) {
                $folder = 'blacklist/mis_blacklist';
                //return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', ['/exports/'.$model->file_name], ['target'=>'_blank','download'=>'download'],['title'=>'Download File']);
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-download-alt"></span>', ['/blacklist-files/exports', 'folder' => $folder, 'file_name' => $model->result_file_name], ['target' => '_blank', 'data-pjax' => '0',], ['title' => 'Download File']);
                \yii\widgets\Pjax::end();
            }
        ],
        'template' => '{download}{view}{delete}',
        'visibleButtons' =>
            [
                'update' =>  function($model) {
                    if($model->status == '0') {
                        return true;
                    }
                },
                'delete' => function($model) {
                    if($model->status == '0') {
                        return true;
                    }
                },
                'download' => function($model) {
                    if(isset($model->result_file_name)) {
                        return true;
                    }
                },
            ],
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