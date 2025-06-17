<?php
use yii\helpers\Url;

return [
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'region_id',
        'value' => 'region.name',
        'label' => 'Region',
        'filter' => $regions
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'area_id',
        'value' => 'area.name',
        'label' => 'Area',
        'filter' => $areas
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'branch_id',
        'value' => 'branch.name',
        'label' => 'branch',
        'filter' => $branches

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'sanction_no',
        'value' => 'loan.sanction_no'
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'member_name',
        'value' => 'application.member.full_name',
        'label' => 'Name',

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'member_cnic',
        'value' => 'application.member.cnic',
        'label' => 'Cnic',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'receive_date',
        'value' => function ($data) {
            return date('d-M-Y', $data->receive_date);
        },

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'amount',
        'value' => function ($data) {
            return number_format($data->amount);
        }
        //'pageSummary' => true,
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'receipt_no',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'project_id',
        'value' => 'project.name',
        'label' => 'Project',
        'filter' => $projects,
    ],
    ['class' => 'yii\grid\ActionColumn',  /// here I need to edit or remove delete button
        'contentOptions' => ['style' => 'width:70px;'],

        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'buttons' => [
            'delete' => function ($url, $model, $key) {
//                $first_of_current_month = strtotime(date('Y-m-01',strtotime('now')));
//                $last_of_current_month = strtotime(date('Y-m-t',strtotime('now')));
//                if (in_array($model->source, ['branch','1','cc']) && !isset($model->application->loans) && ($model->receive_date >= $first_of_current_month && $model->receive_date <= $last_of_current_month)) {
//                    if(!isset($model->writeOff) && empty($model->writeOff)){
//
//                    }
//                }
                if(($model->source != 'cih')){
                    if(strpos($model->receipt_no,'branchR') === false){
                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['composite-recovery-delete', 'id' => $model->id], ['title' => 'Delete', 'onClick' => "return confirm('Are You Sure You Want To Delete This Recovery? ')", 'data-toggle' => 'tooltip']);
                    }
                }
            },

            'update' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['composite-update-recovery', 'id' => $model->id],
                    ['role' => 'modal-remote', 'title' => 'Update Recovery', 'data-toggle' => 'modal', 'data-target' => '#ajaxCrudModal',
                        'data-pjax' => '0']);
            }

        ],
        'template' => '{delete},{update}',
    ]
];