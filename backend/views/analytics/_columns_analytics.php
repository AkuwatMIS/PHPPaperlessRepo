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
            $data = \common\models\Analytics::find()->where(['analytics.api'=>$model->api])->all();
            $user_array = array();
            foreach ($data as $key=>$value){
                $user_array[] = $value->user_id;
            }
            return $this->render('_user-details', [
                'user_array'=>$user_array,
                'model' => $model
            ]);
        },
    ],*/
    [
        'class' => \dimmitri\grid\ExpandRowColumn::class,
        'attribute' => 'users_count',
        'label'=>'No of Users',
        'value'=>'users_count',
        'ajaxErrorMessage' => 'Oops',
        'ajaxMethod' => 'GET',
        'url' => Url::to(['/analytics/user-details']),
        'submitData' => function ($model, $key, $index) {
            return ['id' => $model->api,'user_id'=>$model->id];
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
        'attribute'=>'users_count',
        'value'=>'users_count',
        'label'=>'No of users',
    ],*/
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'api',
        'label'=>'Api Name',
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'count',
        'label'=>'No of times API Hit',
    ]
    /*[
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