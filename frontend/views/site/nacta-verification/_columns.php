<?php
use yii\helpers\Url;
use dimmitri\grid\ExpandRowColumn;
use common\components\Helpers\StructureHelper;
use \yii\helpers\Html;
$permissions = Yii::$app->session->get('permissions');
return [
    [
        'class' => 'yii\grid\SerialColumn',
    ],

    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'matched_status',
       // 'value'=>'application.member.cnic',
    ],

    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_no',
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'application_date',
         'value'=>function ($model, $key, $index) {
             return date('d M Y',$model['application_date']);
         },
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'application_status',
    ],

    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'sanction_no',
         'value'=>function ($model, $key, $index) {
             return $model['sanction_no'];
         },
         //'value'=>'group.grp_no'
    ],
    [
        //'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'branch_name',
        //'label'=>'Project',
        'value'=>function ($model, $key, $index) {
            return $model['branch_name'];
        },
        //'filter'=> $projects
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'member_cnic',
         'value'=>function ($model, $key, $index) {
             return $model['member_cnic'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'member_name',
         'value'=>function ($model, $key, $index) {
             return $model['member_name'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'family_member_cnic',
         'value'=>function ($model, $key, $index) {
             return $model['family_member_cnic'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'family_member_name',
         'value'=>function ($model, $key, $index) {
             return $model['family_member_name'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'guarantor_name',
         'value'=>function ($model, $key, $index) {
             return $model['guarantor_name'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'guarantors_cnic',
         'value'=>function ($model, $key, $index) {
             return $model['guarantors_cnic'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'beneficiary_cnic',
         'value'=>function ($model, $key, $index) {
             return $model['beneficiary_cnic'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'beneficiary_name',
         'value'=>function ($model, $key, $index) {
             return $model['beneficiary_name'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'loan_amount',
         'value'=>function ($model, $key, $index) {
             return $model['loan_amount'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'recovered_amount',
         'value'=>function ($model, $key, $index) {
             return $model['recovered_amount'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'loan_status',
         'value'=>function ($model, $key, $index) {
             return $model['loan_status'];
         },
         //'value'=>'group.grp_no'
    ],
    [
         //'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'recovery_percentage',
         'value'=>function ($model, $key, $index) {
             return $model['recovery_percentage'].'%';
         },
         //'value'=>'group.grp_no'
    ],




];