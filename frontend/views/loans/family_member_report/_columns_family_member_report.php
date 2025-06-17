<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Borrowers;
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
        'value' => 'region.name',
        'filter'=>$regions
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'area_id',
        'label' => 'Area',
        'value' => 'area.name',
        'filter'=>$areas
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'branch_id',
        'label' => 'Branch',
        'value' => 'branch.name',
        'filter'=>$branches
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'project_id',
        'label' => 'Project',
        'value' => 'project.name',
        'filter'=>$projects
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'member_name',
        'label' => 'Name',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data->application->member->full_name, ['members/view', 'id' => $data->application->member->id], ['target' => '_blank'], ['title' => 'Member']);
        },
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'member_parentage',
        'label' => 'Parentage',
        'value' => 'application.member.parentage'
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'member_cnic',
        'label' => 'CNIC',
        'value' => 'application.member.cnic'
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'family_member_cnic',
        'label' => 'Family Member CNIC',
        'value' => 'application.member.family_member_cnic'
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => ('loan_amount'),
        'value'=>function($data){return number_format($data->loan_amount);},
        //'format' => ['decimal'],
        'label' => 'Amount',
        //'pageSummary' => true,

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
        'attribute' => 'date_disbursed',
        'label'=>'Disbursement Date',
        'format' => 'date',
    ],
];   