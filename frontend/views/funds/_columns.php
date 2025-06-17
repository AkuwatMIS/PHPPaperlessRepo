<?php
use yii\helpers\Url;

return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'name',
        'value'=>'name',
        'label'=>'Bank Name',
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'description',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'project_id',
        'label'=>'Project',
        'value'=>function ($model, $key, $index) {
            return $model->project->name;
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'total_fund',
        'value'=>function ($model, $key, $index) {
            return number_format($model->total_fund);
        },
        'label'=>'Allocated Fund Line(Rs.)',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'fund_received',
        'label'=>'Fund Disbursed(Rs.)',
        'value'=>function ($model, $key, $index) {
            return number_format($model->fund_received);
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'olp',
        'label'=>'Recovery(Rs.)',
        'value'=>function ($model, $key, $index) {
            return number_format($model->recovery);
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'fund_utilized',
        'label'=>'Fund Utilized/OLP(Rs.)',
        'value'=>function ($model, $key, $index) {
            $utilized = ($model->fund_received-$model->recovery);
            return number_format($utilized);
        },
    ],

    [
        'class'=>'\kartik\grid\DataColumn',
        //'attribute'=>'balance',
        'label'=>'Available Balance',
        'value'=>function ($model, $key, $index) {
            return number_format(($model->total_fund-($model->fund_received-$model->recovery)));
        },
    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'recovery_last_update',
//        'label'=>'recovery last update',
//        'hAlign'=>'center',
//        'value'=>function ($data) {
//            return date("M j, Y h:i", strtotime($data->updated_at));
//        },
//        'filterType'=>GridView::FILTER_DATE,
//        'filterWidgetOptions' => [
//            'type' => DateTimePicker::TYPE_INPUT,
//            'pluginOptions'=>[
//                'format' => 'yyyy-mm-dd',
//            ],
//            'options' => ['placeholder' => 'Last Updated'],
//        ],
//    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'email',
    ],
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
        'template'=>'{update}',
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