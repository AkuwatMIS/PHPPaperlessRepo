<?php
use yii\helpers\Url;
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
        'attribute'=>'region',
        'value'=>'region.name',
        'label'=>'Region',
        'filter'=> $regions
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area',
        'value'=>'area.name',
        'label'=>'Area',
        'filter'=> $areas
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch',
        'value'=>'branch.name',
        'label'=>'Branch',
        'filter'=> $branches
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'requested_amount',
        'value'=> function ($model, $key, $index, $column){
            return number_format($model->requested_amount);
        },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute' => 'approved_amount',
        'value'=> function ($model, $key, $index, $column){
            return number_format($model->approved_amount);
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'status',
        'filter'=>['pending'=>'Pending','approved'=>'Approved','processed'=>'Processed','rejected'=>'Rejected'],
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'approved_by',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'approved_on',
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
        'contentOptions' => ['style' => 'width:80px'],
        //'dropdown' => false,
        //'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },

        'buttons'=>[
            'show' => function ($url, $model, $key) {
                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['show', 'id'=>$model->id ],['target' => '_blank', 'title' => 'Ledger', 'data-toggle' => 'tooltip'] /*['target'=>'_blank'],['title'=>'Ledger']*/);
            },

            'update' => function ($url, $model, $key) {
                if ($model->status == 'pending' || $model->status == 'approved') {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-send"></span>', ['approval', 'id' => $model->id], ['role' => 'modal-remote', 'title' => 'Approve/Reject', 'data-toggle' => 'tooltip']);
                }
            },
           /* 'approve' => function ($url, $model, $key) {
                if ($model->status == 'pending') {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-send"></span>', ['view', 'id' => $model->id],  ['target'=>'_blank'],['role' => 'modal-remote', 'title' => 'Approve by RM', 'data-toggle' => 'tooltip']);
                }
            },*/
            'precessed' => function ($url, $model, $key) {
                if ($model->status == 'approved') {
                    return \yii\helpers\Html::a('<span class="glyphicon glyphicon-ok"></span>', ['processed', 'id' => $model->id],  ['target'=>'_blank'],[/*'role' => 'modal-remote', */'title' => 'Proceess by RA'/*, 'data-toggle' => 'tooltip'*/]);
                }
            },


        ],
        'visibleButtons' => [
            'precessed' => function ($model) use ($permissions) {
                return (in_array('frontend_processedfundrequests', $permissions));
            },
            'approve' => function ($model) use ($permissions) {
                return (in_array('frontend_viewfundrequests', $permissions));
            },
            'update' => function ($model) use ($permissions) {
                return (in_array('frontend_approvalfundrequests', $permissions));
            },

        ],


        'template' => '{show}  {update}  {precessed} {approve}',
    ],

];   