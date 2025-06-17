<?php
use yii\helpers\Url;

return [
//    [
//        'class' => 'kartik\grid\CheckboxColumn',
//        'width' => '20px',
//    ],
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region_id',
        'value'=>'region.name',
        'label'=>'Region',
        'filter'=>$regions
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area_id',
        'value'=>'area.name',
        'label'=>'Area',
        'filter'=>$areas
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_id',
        'value'=>'branch.name',
        'label'=>'Branch',
        'filter'=>$branches
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
        'value'=>'loan.sanction_no'
    ],
    [
       // 'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_name',
        'value'=>'application.member.full_name',
        'label'=>'Name',
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'member_cnic',
        'value'=>'application.member.cnic',
        'label'=>'Cnic',

    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'receive_date',
        'value'=>function($data){return date('d-M-Y', $data->receive_date);},

    ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'amount',
         //'pageSummary' => true,
     ],
     [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'receipt_no',
     ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'project_id',
        'value'=>'project.name',
        'label'=>'Project',
        'filter'=>$projects
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{logs}',
        'buttons' => [
            'delete' => function ($url, $model, $key) {
                $first_of_current_month = strtotime(date('Y-m-01',strtotime('now')));
                $last_of_current_month = strtotime(date('Y-m-t',strtotime('now')));
                if (!isset($model->application->loans) && ($model->receive_date >= $first_of_current_month && $model->receive_date <= $last_of_current_month)) {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], ['title' => 'Delete', 'onClick' => "return confirm('Are You Sure You Want To Delete This Donation? ')", 'data-toggle' => 'tooltip']);
                }
            },
        ],
        'template' => '{delete}',

        'contentOptions' => ['style' => 'width:70px;'],
    ],

];   