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
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sanction_no',
        'label' => 'Sanction No'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'old_value',
        'label' => 'Old Value'
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'new_value',
        'label' => 'New Value',

    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'stamp',
        //'value'=>'logs.new_value',
        'value' => function ($data) {

            return date("d-M-Y", $data['stamp']);
        },
        'label' => 'Date',

    ],

];   