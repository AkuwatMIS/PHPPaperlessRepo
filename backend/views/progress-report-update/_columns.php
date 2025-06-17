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
        'attribute'=>'report_id',
        'filter'=>$progress_report_data,
        'value'=>function($data){
            if ($data->progressreport->project_id == 0) {
                return date('M j, Y', $data->progressreport->report_date).'(Overall)';
            }
            else{
                $project_name=\common\models\Projects::find()->select('name')->where(['id'=>$data->progressreport->project_id ])->one()['name'];
                return date('M j, Y', $data->progressreport->report_date).'('.$project_name.')';


            }

        }

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
        'filter'=>$regions,
        'value'=>'region.name',
        'label'=>'Region'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'filter'=>$areas,
        'value'=>'area.name',
        'label'=>'Area'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'filter'=>$branches,
        'value'=>'branch.name',
        'label'=>'Branch'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
        'label'=>'Status',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            if ($data->status==0) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
        'filter' => array('0'=>'In-Progress','1'=>'Updated'),
    ],
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
        'template'=>'{view} {delete}',
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