<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Borrowers;
use kartik\date\DatePicker;

return [
    [
        'class' => 'yii\grid\SerialColumn',
        //'width' => '30px',
        //'format'=>'integer',
        //'pageSummary' => 'Total',

    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'region_id',
        'value'=>'region.name',
        'label'=>'Region'
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'area_id',
        'value'=>'area.name',
        'label'=>'Area'
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'branch_id',
        'value'=>'branch.name',
        'label'=>'Branch'
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'project_id',
        'value'=>'project.name',
        'label'=>'Project'
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'sanction_no',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data->sanction_no, ['loans/ledger', 'id' => $data->id], ['target' => '_blank'], ['title' => 'Borrower']);
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'loan_amount',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'grpno',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'name',
        'label' => 'Name',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data->name, ['members/view', 'id' => $data->application->member->id], ['target' => '_blank'], ['title' => 'Borrower']);
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'parentage',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'cnic',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'gender',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'date_disbursed',
        'value'=>function($data){return date('Y-M-d',$data->date_disbursed);}
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'loan_expiry',
        'value'=>function($data){return date('Y-M-d',$data->loan_expiry);}
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'inst_months',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'cheque_no',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'mobile',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'address',
    ],
    /*
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'address',
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'dob',
        'value'=>function($data){return date('Y-M-d',$data->dob);},
        'label'=>'Date of Birth',
    ],*/
];   