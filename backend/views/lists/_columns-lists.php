<?php
use yii\helpers\Url;
use yii\helpers\Html;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
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
        'attribute'=>'list_name',
    ],

    [
        'class' => 'kartik\grid\ActionColumn',
        'template'=>'{view}',
        'buttons'=>[
            'view' => function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view-datalist','list_name'=> $model->list_name], ['data-pjax' => '0','role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip']);
            }
        ],
        ]

];   