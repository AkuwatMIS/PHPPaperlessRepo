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
        'value' => 'loan.region.name',
        'label'=>'Regions',
        'filter'=>$data['regions'],
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'area_id',
        'value' => 'loan.area.name',
        'label'=>'Areas',
        'filter'=>$data['areas'],
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'branch_id',
        'label' => 'Branch',
        'value' => 'loan.branch.name',
        'filter'=>$data['branches'],
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'date_disbursed',
        'label'=>'Disbursement date',
        'value'=>function ($data) {
            return date('j M , Y', ($data->date_disbursed));
        },
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'cheque_no',
        'label'=>'Cheque No',
    ],
     [
         'class'=>'\yii\grid\DataColumn',
         'attribute'=>'loan.sanction_no',
         /*'format' => 'raw',
         'value' => function ($data) {
             return Html::a($data->sanction_no, ['loans/ledger', 'id' => $data->id],['target'=>'_blank'],['title'=>'Borrower']);
         },*/
     ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'loan.loan_amount',
        'value'=>function($data){return number_format($data->loan->loan_amount);},
        'label' => 'Amount',
        //'format'=>['decimal'],
        //'footer'=>true,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'tranch_amount',
        'value'=>function($data){return number_format($data->tranch_amount);},
        'label' => 'Tranche Amount',
        //'format'=>['decimal'],
        //'footer'=>true,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'tranch_no',
        'label' => 'Tranch No',
        //'format'=>['decimal'],
        //'footer'=>true,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'loan.group_no',
        'label' => 'Group No',
        'value' => 'loan.application.group.grp_no'
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_name',
        'label' => 'Name',
        'format' => 'raw',
        'value' => function ($data) {
            return Html::a($data->loan->application->member->full_name, ['members/view', 'id' => $data->loan->application->member->id],['target'=>'_blank'],['title'=>'Borrower']);
        },
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_cnic',
        'label' => 'CNIC',
        'value' => 'loan.application.member.cnic'
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'member_parentage',
        'label' => 'Parentage',
        'value' => 'loan.application.member.parentage'
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'project_id',
        'label' => 'Project',
        'value' => 'loan.project.name',
        'filter'=>$data['projects']
    ],
     [
         'class'=>'\yii\grid\DataColumn',
         'attribute'=>'loan.inst_type',
         'label'=>'Installment Type',
         'filter'=> \common\components\Helpers\ListHelper::getLists('installments_types'),
     ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'loan.inst_months',
        'value'=>function($data){return number_format($data->loan->inst_months);},
        'label'=>'Installment Months',
    ],
];   