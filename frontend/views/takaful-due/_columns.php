<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
//use common\models\Borrowers;
use kartik\date\DatePicker;

return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/

    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
        //'format'=>'integer',
        //'pageSummary' =>'Total',

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'region_id',
        'label' => 'Region',
        'format' => 'raw',
        'value' => function ($data) {

            return $data->region->name;
        }


    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'Area',
        'format' => 'raw',
        'value' => function ($data) {

            return $data->area->name;
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'Branch',
        'format' => 'raw',
        'value' => function ($data) {

            return $data->branch->name;
        },
    ],

    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'full_name',
        'label' => 'Name',
        'format' => 'raw',
        'value' => function ($data) {
            return $data->loan->application->member->full_name;
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'parentage',
        'label' => 'Parentage',
        'value' => function ($data) {
            return $data->loan->application->member->parentage;
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'cnic',
        'label' => 'Cnic',
        'value' => function ($data) {
            return $data->loan->application->member->cnic;
        },
    ],


    [
        'class' => '\yii\grid\DataColumn',

        'attribute' => 'address',
        'format' => 'raw',
        'label' => 'Address',
        'value' => function ($data) {

            return isset($data->loan->application->member->membersAddresses[0]['address']) ? $data->loan->application->member->membersAddresses[0]['address'] : '';
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'mobile',
        'label' => 'Mobile',
        'value' => function ($data) {

            return isset($data->loan->application->member->membersPhones[0]['phone']) ? $data->loan->application->member->membersPhones[0]['phone'] : '';
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',

        'attribute' => 'Sanction_no',
        'format' => 'raw',
        'label' => 'Sanction_no',
        'value' => function ($data) {

            return $data->loan->sanction_no;
        },
    ],


    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'disbursed_date',
        'value' => function ($data) {

            return (!empty($data->loan->date_disbursed) && $data->loan->date_disbursed != null) ? date('d M Y', $data->loan->date_disbursed) : 'NA';
        },
        'label' => 'Disburse_date',

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'Due_Date',
        'value' => function ($data) {
            return (!empty($data->overdue_date) && $data->overdue_date != null) ? date('d M Y', $data->overdue_date) : 'NA';
        },
        'label' => 'Due_Date',

    ],

    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => ('OLP'),
        'value' => function ($data) {
            return ($data['olp']);
        },


    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => ('Takaful'),
        'value' => function ($data) {

            return $data['takaful_amnt'];


        },

        'label' => 'Takaful Amount',


    ],
    [
        'class' => '\yii\grid\DataColumn',

        'attribute' => ('Takaful'),
        'value' => function ($data) {

            return $data['overdue_amnt'];


        },

        'label' => 'Over Due Amount',


    ]


];
