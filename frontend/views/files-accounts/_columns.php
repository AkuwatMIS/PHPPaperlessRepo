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
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'id',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'file_path',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'value' => function ($data, $key, $index) {
            return \common\components\Helpers\StructureHelper::getFilesaccountsstatus($data->status);
        },
        'filter' => \common\components\Helpers\ListHelper::getBankfileStatus(),
        'label' => 'Status',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'total_records',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'updated_records',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'project_id',

        'value' => function ($data, $key, $index) {
            if ($data->project_id == 132) {
                $pName = 'Apni Chhat Apna Ghar';
            } elseif (empty($data->project_id ) && $data->project_id != '0') {
                $pName = 'NA';
            } elseif ($data->project_id == '0'){
                $pName = 'Other';
            }
            return $pName;
        },
        'filter' => \common\components\Helpers\ListHelper::getProjectList(),
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'template' => '{view} {update} {delete} {export}',
        'visibleButtons' => [
            'delete' => function ($model) {
                return ($model->status == 0);
            },
            'update' => function ($model) {
                return ($model->status == 0);
            },
            'export' => function ($model) {
                return ($model->error_description != null);
            },
        ],
        'buttons' => [
            'export' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-export"></span>', ['/files-accounts/error', 'id' => $model->id], ['target' => '_blank', 'data-pjax' => '0',
                    'title' => Yii::t('yii', 'Export Error'),
                ]);
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
    ],
];   