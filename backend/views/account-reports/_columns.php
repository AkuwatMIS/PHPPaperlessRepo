<?php
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\datetime\DateTimePicker;
return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'report_date',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'report_name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'_report_date',
        'hAlign'=>'center',
        'value'=>function ($data) {
            return date("M j, Y h:i", strtotime($data->_report_date));
        },
        'filterType'=>GridView::FILTER_DATE,
        'filterWidgetOptions' => [
            'type' => DateTimePicker::TYPE_INPUT,
            'pluginOptions'=>[
                'format' => 'yyyy-mm-dd',
            ],
            'options' => ['placeholder' => 'Report Date'],
        ],
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_name',
        'label'=>'Project',
        'hAlign'=>'center',
        'value'=>function ($data) {
            if(!$data->project_id){
                return 'Overall';
            }else{
                return isset($data->project->code) ? $data->project->code : '';
            }
        }
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'gender',
    ],*/


    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'_created_at',
        'label'=>'Created Date',
        'hAlign'=>'center',
        'value'=>function ($data) {
            return date("M j, Y h:i",strtotime($data->_created_at));
        },
        'filterType'=>GridView::FILTER_DATE,
        'filterWidgetOptions' => [
            'type' => DateTimePicker::TYPE_INPUT,
            'pluginOptions'=>[
                'format' => 'yyyy-mm-dd',
            ],
            'options' => ['placeholder' => 'Created Date'],
        ],
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'_updated_at',
        'label'=>'Updated Date',
        'hAlign'=>'center',
        'value'=>function ($data) {
            return date("M j, Y h:i",strtotime($data->_updated_at));
        },
        'filterType'=>GridView::FILTER_DATE,
        'filterWidgetOptions' => [
            'type' => DateTimePicker::TYPE_INPUT,
            'pluginOptions'=>[
                'format' => 'yyyy-mm-dd',
            ],
            'options' => ['placeholder' => 'Updated Date'],
        ],
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'period',
        'hAlign'=>'center',
        'filter'=>array('daily'=>'daily','monthly'=>'monthly','annually'=>'annually'),
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'comments',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
        'label'=>'Status',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            if (!$data->status) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('0'=>'In-Progress','1'=>'Generated'),
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'do_update',
        'label'=>'Update',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            if (!$data->do_update) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('0'=>'No','1'=>'Yes'),
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'do_delete',
        'label'=>'Delete',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            if (!$data->do_delete) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('0'=>'No','1'=>'Yes'),
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'deleted',
        'label'=>'Deleted',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            if (!$data->deleted) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('0'=>'No','1'=>'Yes'),
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'is_verified',
        'label'=>'Verified',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            if (!$data->is_verified) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('0'=>'Not Verified','1'=>'Verified'),
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'period',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'comments',
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'status',
     ],*/
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'is_verified',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'do_update',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'do_delete',
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
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'template'=>'{view}{update}',
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