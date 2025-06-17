<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Borrowers;
use common\components\LoanHelper;
use common\components\RbacHelper;
use yii\helpers\ArrayHelper;
return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'yii\grid\SerialColumn',
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'region_id',
        'value' => 'region.name',
        'label'=>'Regions',
        'filter'=>$data['regions'],
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'area_id',
        'value' => 'area.name',
        'label'=>'Areas',
        'filter'=>$data['areas'],
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'branch_id',
        'label' => 'Branch',
        'value' => 'branch.name',
        'filter'=>$data['branches'],
    ],
     [
         'class'=>'\yii\grid\DataColumn',
         'attribute'=>'sanction_no',
         /*'format' => 'raw',
         'value' => function ($data) {
             return Html::a($data->sanction_no, ['loans/ledger', 'id' => $data->id],['target'=>'_blank'],['title'=>'Borrower']);
         },*/
     ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'loan_amount',
        'value'=>function($data){return number_format($data->loan_amount);},
        'label' => 'Amount',
        //'format'=>['decimal'],
        //'footer'=>true,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'group_no',
        'label' => 'Group No',
        'value' => 'application.group.grp_no'
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_name',
        'label' => 'Name',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data->application->member->full_name, ['members/view', 'id' => $data->application->member->id],['target'=>'_blank'],['title'=>'Borrower']);
        },
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_cnic',
        'label' => 'CNIC',
        'value' => 'application.member.cnic'
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_parentage',
        'label' => 'Parentage',
        'value' => 'application.member.parentage'
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_gender',
        'label' => 'Gender',
        'value' => 'application.member.gender'
    ],
    /*[
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_religion',
        'label' => 'Religion',
        'value' => 'application.member.religion'
    ],*/
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_dob',
        'label' => 'Dob',
        'value' => 'application.member.dob',
        'format'=>'date'
    ],
   /* [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_mobile',
        'label' => 'Mobile',
        'value' => 'application.member.membersMobile'
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        // 'attribute'=>'member_address',
        'label' => 'Address',
        'value' => 'application.member.businessAddress'
    ],*/
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'project_id',
        'label' => 'Project',
        'value' => 'project.name',
        'filter'=>$data['projects']
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'product_id',
        'label' => 'Product',
        'value' => 'product.name',
        //'filter'=>$data['Products']
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'activity_id',
        'label' => 'Activity',
        'value' => 'activity.name',
        //'filter'=>$data['Products']
    ],

];   