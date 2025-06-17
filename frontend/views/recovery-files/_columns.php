<?php
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\RecoveryErrors;
use kartik\date\DatePicker;
return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'source',
        'filter' => $source,
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'file_date',
        'value' => function($model) {
            Yii::$app->formatter->locale = 'en-US';
            return Yii::$app->formatter->asDate($model->file_date);
        },
        'filter' => DatePicker::widget([
            'name'=>'RecoveryFiles[file_date]',
            'type' => DatePicker::TYPE_INPUT,
            //'value' => '23-Feb-1982',
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'M d, yyyy'
            ],
        ]),

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'file_name',
        'filter' => $files_list,
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'total_records',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'inserted_records',
        'value'=>function($model)
        {
            return $model->inserted_records;
        },
    ],
    [
        'class'=>'kartik\grid\ExpandRowColumn',
        'value'=> function ($model, $key, $index, $column){
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function($model, $key, $index, $column){
            return $this->render('_errors-details', [
                'model' => RecoveryErrors::find()->where(['recovery_files_id' => $model->id])->all(),
                'id' => $model->id,
            ]);
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'error_records',
    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        //'attribute'=>'error_records',
//        'label'=> 'Resolved Errors',
//        'value' =>function($model)
//        {
//            $resolved = 0;
//            $resolved = RecoveryErrors::find()->where(['recovery_files_id' => $model->id, 'status' => '2'])->count();
//            return $resolved;
//        },
//    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'label' => 'Resolved Errors',
        'value' => function ($model) {
            // Optimized query for resolved errors count
            return Yii::$app->cache->getOrSet('resolved-errors-' . $model->id, function () use ($model) {
                return RecoveryErrors::find()->where(['recovery_files_id' => $model->id, 'status' => '2'])->count();
            }, 3600); // Cache for 1 hour
        },
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'value' => function ($model) {
            $statusLabels = [
                '0' => 'Approval Pending',
                '1' => 'Approved',
                '2' => 'Execute',
                '3' => 'In-Process',
                '4' => 'Completed'
            ];
            return isset($statusLabels[$model->status]) ? $statusLabels[$model->status] : 'Unknown';
        },
        'filter' => $status,
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'template' => '{view} {update} {delete}',
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