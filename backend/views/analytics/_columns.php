<?php
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\datetime\DateTimePicker;

return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    /*[
        'class'=>'kartik\grid\ExpandRowColumn',
        'value'=> function ($model, $key, $index, $column){
            return GridView::ROW_COLLAPSED;
        },
        'detail' => function($model, $key, $index, $column){
            return $this->render('_analytics-details', [
                'model' => $model
            ]);
        },
    ],*/
    [
        'class' => \dimmitri\grid\ExpandRowColumn::class,
        'attribute' => 'email',
        'label'=>'Email',
        'value'=>'email',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/analytics/analytics-details']),
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
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'email',
        'value'=>'email',
        'label'=>'User'
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'fullname',
        'value'=>'fullname',
        'label'=>'Full Name'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'designation',
        'value'=>'role.item_name',
        'label'=>'Designation',
    ],
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'region',
        'value'=>'region.name',
        'label'=>'Region'
    ],*/
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'area',
        'value'=>'area.name',
        'label'=>'Area'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch',
        'value'=>'branch.name',
        'label'=>'Branch'
    ],*/
    /*[
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'api',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'count',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'description',
    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'created_at',
         'hAlign'=>'center',
         'value'=>function ($data) {
             return date("M j, Y h:i", strtotime($data->created_at));
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
         'attribute'=>'updated_at',
         'label'=>'Last Updated',
         'hAlign'=>'center',
         'value'=>function ($data) {
             return date("M j, Y h:i", strtotime($data->updated_at));
         },
         'filterType'=>GridView::FILTER_DATE,
         'filterWidgetOptions' => [
             'type' => DateTimePicker::TYPE_INPUT,
             'pluginOptions'=>[
                 'format' => 'yyyy-mm-dd',
             ],
             'options' => ['placeholder' => 'Last Updated'],
         ],
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
    ],*/


];   