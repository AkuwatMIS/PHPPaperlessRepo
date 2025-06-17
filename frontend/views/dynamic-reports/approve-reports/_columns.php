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
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'report_name',
        'value'=>function($model){
            return ucfirst($model->report->name);
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'description',
        'value'=>function($model){
            $value = '';
            if($model->region_id > 0) {
                $value .= $model->region->name.',';
            }
            else {
                $value .= 'All'.',';
            }
            if($model->area_id > 0) {
                $value .= $model->area->name.',';
            }
            else {
                $value .= 'All'.',';
            }
            if($model->branch_id > 0) {
                $value .= $model->branch->name.',';
            }
            else {
                $value .= 'All'.',';
            }
            if($model->project_id > 0) {
                $value .= $model->project->name.',';
            }
            else {
                $value .= 'All'.',';
            }
            return trim($value);
        },
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
        'value'=>function($model){
            if($model->region_id > 0) {
                return $model->region->name;
            }
            else {
                return 'All';
            }
        },
        'label'=>'Region'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'value'=>function($model){
            if($model->area_id > 0) {
                return $model->area->name;
            }
            else {
                return 'All';
            }
        },
        'label'=>'Area'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'value'=>function($model){
            if($model->branch_id > 0) {
                return $model->branch->name;
            }
            else {
                return 'All';
            }
        },
        'label'=>'Branch'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'value'=>function($model){
            if($model->project_id > 0) {
                return $model->project->name;
            }
            else {
                return 'All';
            }
        },
        'label'=>'Project'
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'report_date',
        'value'=>function($model){
            return str_replace(' - ',' to ',$model->report_date);
        },
        'label'=>'Report Date'
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'filters',
    ],*/
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'visibility',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'notification',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'user_name',
        'label' => 'Requested By',
        'value'=>function($model){
            return $model->user->fullname;
        },
    ],
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
            'report-delete' => function ($url, $model, $key) {
                return \kartik\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['delete', 'id' => $model->id], ['target' => '_blank', 'data-pjax' => '0',]));

            },
        ],
        'template' => '{view} {report-delete}',
        'contentOptions' => ['style' => 'width:100px;'],
    ],

];   