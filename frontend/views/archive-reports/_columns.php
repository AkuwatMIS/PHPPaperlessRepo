<?php
use yii\helpers\Url;
use yii\helpers\Html;

return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    /* [
     'class'=>'\kartik\grid\DataColumn',
     'attribute'=>'id',
 ],*/
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'report_name',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'source',
        'filter'=>$sources
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'date_filter',
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region.name',
        'label'=>'Region',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area.name',
        'label'=>'Area',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch.name',
        'label'=>'Branch',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project.name',
        'label'=>'Project',
    ],*/

    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'activity.name',
        'label'=>'Activity',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'product.name',
        'label'=>'Product',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'gender',
    ],*/
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'requested_by',
    ],*/
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'file_path',
        'format' => 'raw',
        'value'=> function ($model, $key, $index, $column){
            if(isset($model->file_path)){
                return \yii\helpers\Html::a('Download',['download','id'=> $model->id],['class' => 'btn btn-primary']);
            }else{
                return 'File Not Created Yet';
            }

        },
    ],*/
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'format'=>'raw',
        'hAlign'=>'center',
        'value'=> function ($data) {
            if ($data->status==0) {
                return '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
            } else {
                return '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
            }
        },
    ],
    /*[
      'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'created_at',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'updated_at',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'do_delete',
    ],*/
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'buttons' => [
            'download' => function ($url, $model, $key) {
                //return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', ['/exports/'.$model->file_path], ['target'=>'_blank','download'=>'download'],['title'=>'Download File']);
                \yii\widgets\Pjax::begin();
                if ($model->report_name == 'duelist-report') {
                    $folder = 'exports/duelists';
                } else {
                    $folder = 'exports';
                }
                return Html::a('<span class="glyphicon glyphicon-download-alt"></span>', ['/archive-reports/exports', 'folder' => $folder, 'file_name' => $model->file_path], ['target' => '_blank', 'data-pjax' => '0',], ['title' => 'Download File']);
                \yii\widgets\Pjax::end();
            },
        ],
        'viewOptions' => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
        'updateOptions' => ['role' => 'modal-remote', 'title' => 'Update', 'data-toggle' => 'tooltip'],
        'deleteOptions' => ['role' => 'modal-remote', 'title' => 'Delete',
            'data-confirm' => false, 'data-method' => false,// for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => 'Are you sure?',
            'data-confirm-message' => 'Are you sure want to delete this item'],
        'template' => '{download} {view} {update} {delete}',
        'visibleButtons' => [
            'download' => function ($model) {
                $visible = false;
                if ($model->file_path != null) {
                    $visible = true;
                    return $visible;
                }
            },
        ],
    ],

];
?>

