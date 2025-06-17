<?php
use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use common\components\Helpers\StructureHelper;

return [

    [
        'class' => 'yii\grid\SerialColumn',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
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
       // 'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'team_id',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'field_id',
    ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'is_locked',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'br_serial',
    // ],
     /*[
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'grp_no',
     ],*/
    [
        'class' => ExpandRowColumn::class,
        'attribute' => 'grp_no',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/groups/member-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->id];
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
         'attribute'=>'group_name',
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'grp_type',
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'co_code_count_temp',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'status',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'reject_reason',
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
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'deleted',
    // ],
    [
        'class' => 'yii\grid\ActionColumn',
        'buttons' => [
            'grp-update' => function ($url, $model, $key) {
                if ($model->grp_type == 'IND') {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['ind-update', 'id' => $model->id], ['target' => '_blank'], ['title' => 'Update']);

                } else {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id], ['target' => '_blank'], ['title' => 'Update']);

                }
            },
            'grp-delete' => function ($url, $model, $key) {
                if ($model->grp_type == 'IND' && !isset($model->laons) && empty($model->loans)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete-ind-grp', 'id' => $model->id],
                        [
                            'role' => 'modal-remote', 'title' => 'Delete', 'data-toggle' => 'tooltip',
                            'data-confirm'=>true,
                            'data-confirm-title'=>'Delete Group',
                            'data-confirm'=>'Are you sure want to delete Group against Group No:'.$model->grp_no.'',
                            ]
                        );
                }
            },
        ],
        //{edit}{delete}
        'template' => '{view} {grp-update} {grp-delete}',
        'contentOptions' => ['style' => 'width:70px;'],
    ],

];   